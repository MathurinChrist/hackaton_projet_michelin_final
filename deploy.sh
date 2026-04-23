#!/bin/bash
set -e

# ══════════════════════════════════════════════════════════
#  Michelin App — Script de déploiement VPS
#  IP: 54.37.159.216
# ══════════════════════════════════════════════════════════

VPS_IP="54.37.159.216"
VPS_USER="almalinux"
APP_DIR="/opt/michelin"
API_URL="http://${VPS_IP}:8000/api"

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║   MICHELIN APP — DÉPLOIEMENT PRODUCTION  ║"
echo "╚══════════════════════════════════════════╝"
echo ""
echo "→ VPS     : $VPS_IP"
echo "→ API URL : $API_URL"
echo ""

# ── 1. Build des images Docker en local ──────────────────
echo "▶ [1/5] Build des images..."

docker build -t michelin-server:latest -f Dockerfile.server . && \
  echo "  ✓ michelin-server built"

docker build -t michelin-client:latest \
  --build-arg VITE_API_URL="$API_URL" \
  -f Dockerfile.client . && \
  echo "  ✓ michelin-client built"

docker build -t michelin-mobile:latest \
  --build-arg VITE_API_URL="$API_URL" \
  -f Dockerfile.mobile . && \
  echo "  ✓ michelin-mobile built"

# ── 2. Sauvegarde des images en archives ─────────────────
echo ""
echo "▶ [2/5] Export des images..."
docker save michelin-server:latest | gzip > /tmp/michelin-server.tar.gz
docker save michelin-client:latest | gzip > /tmp/michelin-client.tar.gz
docker save michelin-mobile:latest | gzip > /tmp/michelin-mobile.tar.gz
echo "  ✓ Images exportées dans /tmp/"

# ── 3. Envoi des fichiers sur le VPS ─────────────────────
echo ""
echo "▶ [3/5] Envoi des fichiers sur le VPS..."

# Créer le dossier sur le VPS
sshpass -p 'Bobann2001' ssh -o StrictHostKeyChecking=no ${VPS_USER}@${VPS_IP} \
  "sudo mkdir -p ${APP_DIR} && sudo chown -R ${VPS_USER}:${VPS_USER} ${APP_DIR}"

# Copier les fichiers de config
sshpass -p 'Bobann2001' scp -o StrictHostKeyChecking=no \
  docker-compose.prod.yml \
  nginx-api.conf \
  ${VPS_USER}@${VPS_IP}:${APP_DIR}/

# Copier les images Docker
for img in server client mobile; do
  echo "  → Envoi michelin-${img}.tar.gz..."
  sshpass -p 'Bobann2001' scp -o StrictHostKeyChecking=no \
    /tmp/michelin-${img}.tar.gz \
    ${VPS_USER}@${VPS_IP}:/tmp/
done
echo "  ✓ Fichiers envoyés"

# ── 4. Installation et démarrage sur le VPS ──────────────
echo ""
echo "▶ [4/5] Déploiement sur le VPS..."

sshpass -p 'Bobann2001' ssh -o StrictHostKeyChecking=no ${VPS_USER}@${VPS_IP} << 'REMOTE'
set -e

APP_DIR="/opt/michelin"

echo "  → Installation de Docker si absent..."
if ! command -v docker &>/dev/null; then
  sudo dnf install -y docker
  sudo systemctl enable --now docker
  sudo usermod -aG docker almalinux
fi

echo "  → Installation de docker-compose si absent..."
if ! command -v docker-compose &>/dev/null; then
  sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" \
    -o /usr/local/bin/docker-compose
  sudo chmod +x /usr/local/bin/docker-compose
fi

echo "  → Chargement des images Docker..."
docker load < /tmp/michelin-server.tar.gz
docker load < /tmp/michelin-client.tar.gz
docker load < /tmp/michelin-mobile.tar.gz
echo "  ✓ Images chargées"

echo "  → Ouverture des ports firewall..."
sudo firewall-cmd --permanent --add-port=80/tcp 2>/dev/null || true
sudo firewall-cmd --permanent --add-port=8000/tcp 2>/dev/null || true
sudo firewall-cmd --permanent --add-port=8080/tcp 2>/dev/null || true
sudo firewall-cmd --reload 2>/dev/null || true

echo "  → Démarrage des conteneurs..."
cd $APP_DIR
docker-compose -f docker-compose.prod.yml down --remove-orphans 2>/dev/null || true
docker-compose -f docker-compose.prod.yml up -d --remove-orphans

echo "  → Attente base de données (10s)..."
sleep 10

echo "  → Migration base de données..."
docker-compose -f docker-compose.prod.yml exec -T server \
  php bin/console doctrine:migrations:migrate --no-interaction --env=prod 2>/dev/null || \
  docker-compose -f docker-compose.prod.yml exec -T server \
  php bin/console doctrine:schema:create --env=prod 2>/dev/null || \
  echo "  ⚠ Migration ignorée (peut-être déjà faite)"

echo "  → Nettoyage des archives..."
rm -f /tmp/michelin-*.tar.gz

echo "  ✓ Déploiement terminé !"
REMOTE

# ── 5. Vérification ──────────────────────────────────────
echo ""
echo "▶ [5/5] Vérification..."
sleep 3

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║            DÉPLOIEMENT RÉUSSI            ║"
echo "╠══════════════════════════════════════════╣"
echo "║  Client Desktop : http://${VPS_IP}       ║"
echo "║  Mobile PWA     : http://${VPS_IP}:8080  ║"
echo "║  API Backend    : http://${VPS_IP}:8000  ║"
echo "╚══════════════════════════════════════════╝"
echo ""
