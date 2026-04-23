#!/usr/bin/env python3
"""
Michelin App — Script de déploiement automatique
VPS: 54.37.159.216 (AlmaLinux)
"""

import paramiko
import subprocess
import sys
import os
import time

# ── Configuration ─────────────────────────────────────────
VPS_IP       = "54.37.159.216"
VPS_USER     = "almalinux"
VPS_PASS     = "Bobann2001"
APP_DIR      = "/opt/michelin"
API_URL      = f"http://{VPS_IP}:8000/api"
PROJECT_DIR  = os.path.dirname(os.path.abspath(__file__))

# Clé Mistral — à renseigner si vous en avez une
MISTRAL_API_KEY = os.environ.get("MISTRAL_API_KEY", "YOUR_MISTRAL_KEY_HERE")

# ── Couleurs ──────────────────────────────────────────────
G = "\033[92m"   # vert
R = "\033[91m"   # rouge
Y = "\033[93m"   # jaune
B = "\033[94m"   # bleu
E = "\033[0m"    # reset

def step(n, total, msg):
    print(f"\n{B}▶ [{n}/{total}] {msg}{E}")

def ok(msg):
    print(f"  {G}✓ {msg}{E}")

def info(msg):
    print(f"  {Y}→ {msg}{E}")

def err(msg):
    print(f"  {R}✗ {msg}{E}")

def run_local(cmd, cwd=None, check=True):
    """Exécute une commande locale."""
    info(f"local$ {cmd}")
    res = subprocess.run(cmd, shell=True, cwd=cwd or PROJECT_DIR,
                         capture_output=False, text=True)
    if check and res.returncode != 0:
        err(f"Commande échouée (code {res.returncode})")
        sys.exit(1)
    return res

def ssh_run(client, cmd, timeout=300):
    """Exécute une commande SSH et affiche la sortie."""
    _, stdout, stderr = client.exec_command(cmd, timeout=timeout, get_pty=True)
    output = ""
    while True:
        line = stdout.readline()
        if not line:
            break
        output += line
        print(f"    {line}", end="")
    exit_code = stdout.channel.recv_exit_status()
    if exit_code != 0:
        err_out = stderr.read().decode()
        if err_out:
            print(f"    {R}{err_out}{E}")
    return exit_code, output

def connect_ssh():
    """Connexion SSH au VPS."""
    info(f"Connexion SSH à {VPS_USER}@{VPS_IP}...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(VPS_IP, username=VPS_USER, password=VPS_PASS, timeout=30)
    ok("Connexion SSH établie")
    return client

# ══════════════════════════════════════════════════════════
#  MAIN
# ══════════════════════════════════════════════════════════

print(f"""
{B}╔══════════════════════════════════════════╗
║   MICHELIN APP — DÉPLOIEMENT PRODUCTION  ║
╚══════════════════════════════════════════╝{E}

  VPS IP  : {VPS_IP}
  API URL : {API_URL}
  Client  : http://{VPS_IP}
  Mobile  : http://{VPS_IP}:8080
""")

# ── ÉTAPE 1 : Build des images Docker ─────────────────────
step(1, 5, "Build des images Docker en local...")

run_local(f"docker build -t michelin-server:latest -f Dockerfile.server .")
ok("michelin-server:latest buildé")

run_local(f'docker build -t michelin-client:latest --build-arg VITE_API_URL="{API_URL}" -f Dockerfile.client .')
ok("michelin-client:latest buildé")

run_local(f'docker build -t michelin-mobile:latest --build-arg VITE_API_URL="{API_URL}" -f Dockerfile.mobile .')
ok("michelin-mobile:latest buildé")

# ── ÉTAPE 2 : Export des images ───────────────────────────
step(2, 5, "Export des images en archives...")

for img in ["server", "client", "mobile"]:
    info(f"Export michelin-{img}...")
    run_local(f"docker save michelin-{img}:latest | gzip > /tmp/michelin-{img}.tar.gz")
    ok(f"/tmp/michelin-{img}.tar.gz")

# ── ÉTAPE 3 : Connexion SSH ───────────────────────────────
step(3, 5, "Connexion et préparation du VPS...")

client = connect_ssh()
sftp = client.open_sftp()

# ── ÉTAPE 3a : Installation de Docker (AlmaLinux/RHEL) ────
info("Vérification/installation de Docker...")

docker_install_script = """
#!/bin/bash
set -e

# Vérifier si Docker est déjà installé
if command -v docker &>/dev/null; then
    echo "Docker déjà installé: $(docker --version)"
    exit 0
fi

echo "→ Installation de Docker sur AlmaLinux..."

# Supprimer les anciens paquets
sudo dnf remove -y docker docker-client docker-client-latest \
    docker-common docker-latest docker-latest-logrotate \
    docker-logrotate docker-engine podman runc 2>/dev/null || true

# Ajouter le dépôt Docker CE
sudo dnf install -y dnf-plugins-core
sudo dnf config-manager --add-repo https://download.docker.com/linux/rhel/docker-ce.repo

# Installer Docker CE
sudo dnf install -y docker-ce docker-ce-cli containerd.io \
    docker-buildx-plugin docker-compose-plugin

# Démarrer et activer Docker
sudo systemctl enable --now docker

# Ajouter l'utilisateur au groupe docker
sudo usermod -aG docker almalinux

echo "✓ Docker installé: $(docker --version)"
"""

exit_code, _ = ssh_run(client, docker_install_script, timeout=300)
if exit_code != 0:
    err("Échec installation Docker")
    sys.exit(1)

# ── ÉTAPE 3b : docker-compose ─────────────────────────────
info("Vérification/installation de docker-compose...")
docker_compose_script = """
if docker compose version &>/dev/null 2>&1; then
    echo "docker compose plugin OK: $(docker compose version)"
elif command -v docker-compose &>/dev/null; then
    echo "docker-compose OK: $(docker-compose --version)"
else
    echo "→ Installation de docker-compose standalone..."
    COMPOSE_VERSION=$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep tag_name | cut -d '"' -f4)
    sudo curl -L "https://github.com/docker/compose/releases/download/${COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" \
        -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    echo "✓ docker-compose $(docker-compose --version)"
fi
"""
ssh_run(client, docker_compose_script, timeout=120)

# ── ÉTAPE 3c : Créer le dossier de l'app ──────────────────
info(f"Création du dossier {APP_DIR}...")
ssh_run(client, f"sudo mkdir -p {APP_DIR} && sudo chown -R {VPS_USER}:{VPS_USER} {APP_DIR}")

# ── ÉTAPE 3d : Ouvrir les ports firewall ──────────────────
info("Configuration du firewall...")
firewall_script = """
if command -v firewall-cmd &>/dev/null; then
    sudo firewall-cmd --permanent --add-port=80/tcp 2>/dev/null || true
    sudo firewall-cmd --permanent --add-port=8000/tcp 2>/dev/null || true
    sudo firewall-cmd --permanent --add-port=8080/tcp 2>/dev/null || true
    sudo firewall-cmd --reload 2>/dev/null || true
    echo "✓ Ports 80, 8000, 8080 ouverts (firewalld)"
elif command -v ufw &>/dev/null; then
    sudo ufw allow 80/tcp
    sudo ufw allow 8000/tcp
    sudo ufw allow 8080/tcp
    echo "✓ Ports 80, 8000, 8080 ouverts (ufw)"
else
    echo "→ Pas de firewall détecté, ports supposés libres"
fi
"""
ssh_run(client, firewall_script)

# ── ÉTAPE 4 : Transfert des fichiers sur le VPS ───────────
step(4, 5, "Transfert des fichiers vers le VPS...")

# Transférer docker-compose.prod.yml
info("Envoi docker-compose.prod.yml...")
sftp.put(f"{PROJECT_DIR}/docker-compose.prod.yml", f"{APP_DIR}/docker-compose.prod.yml")
ok("docker-compose.prod.yml envoyé")

# Transférer nginx-api.conf
info("Envoi nginx-api.conf...")
sftp.put(f"{PROJECT_DIR}/nginx-api.conf", f"{APP_DIR}/nginx-api.conf")
ok("nginx-api.conf envoyé")

# Transférer les images Docker
for img in ["server", "client", "mobile"]:
    info(f"Transfert michelin-{img}.tar.gz (peut prendre quelques minutes)...")
    sftp.put(f"/tmp/michelin-{img}.tar.gz", f"/tmp/michelin-{img}.tar.gz")
    ok(f"michelin-{img}.tar.gz transféré")

sftp.close()

# ── ÉTAPE 5 : Déploiement sur le VPS ─────────────────────
step(5, 5, "Déploiement et démarrage des conteneurs...")

deploy_script = f"""
#!/bin/bash
set -e

APP_DIR="{APP_DIR}"
MISTRAL_KEY="{MISTRAL_API_KEY}"

echo "→ Chargement des images Docker..."
docker load < /tmp/michelin-server.tar.gz
docker load < /tmp/michelin-client.tar.gz
docker load < /tmp/michelin-mobile.tar.gz
echo "✓ Images chargées"

# Vérifier les images
docker images | grep michelin

echo "→ Création du fichier .env de production..."
cat > $APP_DIR/.env << 'ENVEOF'
MISTRAL_API_KEY={MISTRAL_API_KEY}
ENVEOF

echo "→ Arrêt des anciens conteneurs..."
cd $APP_DIR

# Tenter avec 'docker compose' (plugin v2) ou 'docker-compose' (v1)
COMPOSE_CMD="docker compose"
if ! docker compose version &>/dev/null 2>&1; then
    COMPOSE_CMD="docker-compose"
fi

$COMPOSE_CMD -f docker-compose.prod.yml down --remove-orphans 2>/dev/null || true

echo "→ Démarrage des conteneurs..."
$COMPOSE_CMD -f docker-compose.prod.yml up -d

echo "→ Attente démarrage DB (15s)..."
sleep 15

echo "→ Migration base de données Symfony..."
$COMPOSE_CMD -f docker-compose.prod.yml exec -T server \
    php bin/console doctrine:migrations:migrate --no-interaction --env=prod 2>/dev/null || \
$COMPOSE_CMD -f docker-compose.prod.yml exec -T server \
    php bin/console doctrine:schema:update --force --env=prod 2>/dev/null || \
    echo "⚠ Migration ignorée"

echo "→ Nettoyage des archives..."
rm -f /tmp/michelin-*.tar.gz

echo ""
echo "✓ Conteneurs démarrés :"
$COMPOSE_CMD -f docker-compose.prod.yml ps
"""

exit_code, _ = ssh_run(client, deploy_script, timeout=600)

if exit_code != 0:
    err("Problème lors du déploiement")
    # Afficher les logs
    ssh_run(client, f"cd {APP_DIR} && docker compose -f docker-compose.prod.yml logs --tail=50 2>/dev/null || docker-compose -f docker-compose.prod.yml logs --tail=50 2>/dev/null || true")
    client.close()
    sys.exit(1)

client.close()

# ── Résultat final ────────────────────────────────────────
print(f"""
{G}╔══════════════════════════════════════════════════╗
║         🎉 DÉPLOIEMENT RÉUSSI !                  ║
╠══════════════════════════════════════════════════╣
║                                                  ║
║  🖥  Client Desktop  http://{VPS_IP}             ║
║  📱  Mobile PWA      http://{VPS_IP}:8080        ║
║  🔌  API Backend     http://{VPS_IP}:8000/api    ║
║                                                  ║
╚══════════════════════════════════════════════════╝{E}
""")
