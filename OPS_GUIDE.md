# 📘 Guide Opérationnel – Michelin Hackathon

VPS actuel : `ubuntu@54.37.159.216` | Mot de passe : `Bobann2001`

---

## 1. 🧠 Base de Connaissance IA (sur le VPS)

La base de connaissance est ce qui permet à l'IA Michelin de répondre aux requêtes
Vibes et Fact-Check. Elle est stockée dans la table `knowledge_base` de PostgreSQL.

### 1.1 Trouver le nom du conteneur serveur

À chaque redémarrage du Swarm, le nom du conteneur change. Lance toujours cette commande d'abord :

```bash
docker ps --format '{{.Names}} {{.Image}}' | grep michelin-server
# Exemple de résultat : michelin_server.1.vjxt2fox8m0oy4k87ki6ejukf michelin-server:latest
```

Les commandes suivantes utilisent `CONTAINER` comme variable à remplacer par ce nom.

### 1.2 Voir le contenu actuel de la base de connaissance

```bash
# Depuis le VPS (après ssh ubuntu@54.37.159.216)
CONTAINER=$(docker ps --format '{{.Names}}' | grep michelin_server\. | head -1)

docker exec $CONTAINER php bin/console dbal:run-sql "SELECT id, name, base_score, status FROM knowledge_base ORDER BY base_score DESC"
```

### 1.3 Ajouter un établissement à la base de connaissance

La commande Symfony `app:add-knowledge` prend 5 arguments :

```
php bin/console app:add-knowledge <nom> <score/10> <mots-clés> [spécialités] [ambiance]
```

**Exemple :**
```bash
CONTAINER=$(docker ps --format '{{.Names}}' | grep michelin_server\. | head -1)

docker exec $CONTAINER php bin/console app:add-knowledge \
  "Le Jules Verne" \
  9.5 \
  "vue,paris,tour eiffel,gastronomie,luxe" \
  "Homard breton, Tournedos Rossini, Soufflé au Grand Marnier" \
  "Vue panoramique sur Paris, service blanc, romantique"
```

**Autre exemple (rapide) :**
```bash
docker exec $CONTAINER php bin/console app:add-knowledge \
  "Septime" 8.2 "bistronomie,paris 11e,chef,naturel,bio"
```

> 💡 Score ≥ 7 → statut "Validé Michelin" | Score < 7 → "Risque de déception"

### 1.4 Compter les entrées dans la base

```bash
CONTAINER=$(docker ps --format '{{.Names}}' | grep michelin_server\. | head -1)
docker exec $CONTAINER php bin/console dbal:run-sql "SELECT COUNT(*) FROM knowledge_base"
```

### 1.5 Supprimer une entrée

```bash
CONTAINER=$(docker ps --format '{{.Names}}' | grep michelin_server\. | head -1)
docker exec $CONTAINER php bin/console dbal:run-sql "DELETE FROM knowledge_base WHERE id = 3"
```

### 1.6 Lancer le seeder complet (si le fichier seed existe)

```bash
CONTAINER=$(docker ps --format '{{.Names}}' | grep michelin_server\. | head -1)
docker exec $CONTAINER php seed_real_data.php
# ou
docker exec $CONTAINER php seed_knowledge.php
```

---

## 2. 🔄 Mettre à jour le VPS après des modifications locales

Depuis la racine du projet sur ta machine locale (`/hackaton_projet_michelin`) :

### Étape 1 – S'assurer que le code est à jour (optionnel)
```bash
git pull origin main
```

### Étape 2 – Rebuild les images modifiées

Si tu as modifié le **backend (server/)** :
```bash
docker build -t michelin-server:latest -f Dockerfile.server .
```

Si tu as modifié le **frontend desktop (client/)** :
```bash
docker build -t michelin-client:latest --build-arg VITE_API_URL="http://54.37.159.216:8000/api" -f Dockerfile.client .
```

Si tu as modifié le **mobile PWA (mobile-pwa/)** :
```bash
docker build -t michelin-mobile:latest --build-arg VITE_API_URL="http://54.37.159.216:8000/api" -f Dockerfile.mobile .
```

### Étape 3 – Exporter les images en archives
```bash
# Adapter selon ce qui a changé
docker save michelin-server:latest | gzip > michelin-server.tar.gz
docker save michelin-client:latest | gzip > michelin-client.tar.gz
docker save michelin-mobile:latest | gzip > michelin-mobile.tar.gz
```

### Étape 4 – Envoyer les archives + configs vers le VPS (rsync = rapide)
```bash
rsync -avzP michelin-server.tar.gz michelin-client.tar.gz michelin-mobile.tar.gz ubuntu@54.37.159.216:/tmp/
rsync -avzP docker-compose.prod.yml nginx-api.conf nginx-client.conf nginx-mobile.conf ubuntu@54.37.159.216:/opt/michelin/
```

### Étape 5 – Charger les images et redéployer (via SSH)
```bash
ssh ubuntu@54.37.159.216 "
  docker load < /tmp/michelin-server.tar.gz &&
  docker load < /tmp/michelin-client.tar.gz &&
  docker load < /tmp/michelin-mobile.tar.gz &&
  cd /opt/michelin &&
  docker stack deploy -c docker-compose.prod.yml michelin &&
  docker service ls &&
  rm -f /tmp/michelin-*.tar.gz
"
```

### Étape 6 – Si tu as ajouté une nouvelle migration Symfony
```bash
CONTAINER=$(docker ps --format '{{.Names}}' | grep michelin_server\. | head -1)
# Option A – Schema auto (rapide, sans migration)
docker exec $CONTAINER php bin/console doctrine:schema:update --force
# Option B – Migrations classiques (si elles sont compatibles PostgreSQL)
# docker exec $CONTAINER php bin/console doctrine:migrations:migrate --no-interaction
```

> ⚠️ Les migrations actuelles du projet utilisent la syntaxe MySQL (`AUTO_INCREMENT`).
> Elles sont **incompatibles** avec PostgreSQL. Utilise toujours `schema:update --force` en prod.

---

## 3. 🚀 Déployer sur un NOUVEAU VPS

Suis ces étapes dans l'ordre pour partir de zéro sur un nouveau serveur.

### 3.1 Prérequis sur le nouveau VPS (Ubuntu 22.04 recommandé)

```bash
# Se connecter au nouveau VPS
ssh ubuntu@<NOUVELLE_IP>

# Installer Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker ubuntu
newgrp docker

# Initialiser Docker Swarm (1 seul nœud = manager)
docker swarm init

# Créer le dossier de déploiement
sudo mkdir -p /opt/michelin
sudo chown ubuntu:ubuntu /opt/michelin
```

### 3.2 Mettre à jour l'IP dans le projet (sur ta machine locale)

Dans `docker-compose.prod.yml`, changer :
```yaml
CORS_ALLOW_ORIGIN: "^https?://54.37.159.216(:[0-9]+)?$$"
```
→ Remplacer `54.37.159.216` par `<NOUVELLE_IP>`

Puis rebuild les frontends avec la nouvelle IP :
```bash
docker build -t michelin-client:latest --build-arg VITE_API_URL="http://<NOUVELLE_IP>:8000/api" -f Dockerfile.client .
docker build -t michelin-mobile:latest --build-arg VITE_API_URL="http://<NOUVELLE_IP>:8000/api" -f Dockerfile.mobile .
docker build -t michelin-server:latest -f Dockerfile.server .
```

### 3.3 Exporter toutes les images
```bash
docker save michelin-server:latest | gzip > michelin-server.tar.gz
docker save michelin-client:latest | gzip > michelin-client.tar.gz
docker save michelin-mobile:latest | gzip > michelin-mobile.tar.gz
```

### 3.4 Envoyer tout vers le nouveau VPS
```bash
rsync -avzP michelin-server.tar.gz michelin-client.tar.gz michelin-mobile.tar.gz ubuntu@<NOUVELLE_IP>:/tmp/
rsync -avzP docker-compose.prod.yml nginx-api.conf nginx-client.conf nginx-mobile.conf ubuntu@<NOUVELLE_IP>:/opt/michelin/
```

### 3.5 Charger, déployer et initialiser la BDD
```bash
ssh ubuntu@<NOUVELLE_IP> "
  docker load < /tmp/michelin-server.tar.gz &&
  docker load < /tmp/michelin-client.tar.gz &&
  docker load < /tmp/michelin-mobile.tar.gz &&
  cd /opt/michelin &&
  docker stack deploy -c docker-compose.prod.yml michelin &&
  echo 'Attente démarrage des services...' && sleep 20 &&
  docker service ls &&
  rm -f /tmp/michelin-*.tar.gz
"
```

### 3.6 Créer le schéma de base de données
```bash
ssh ubuntu@<NOUVELLE_IP>
# Une fois connecté :
CONTAINER=$(docker ps --format '{{.Names}}' | grep michelin_server\. | head -1)
docker exec $CONTAINER php bin/console doctrine:schema:update --force
```

### 3.7 Vérification finale
```bash
curl http://<NOUVELLE_IP>:80       # Frontend desktop → 200
curl http://<NOUVELLE_IP>:8080     # Mobile PWA → 200
curl http://<NOUVELLE_IP>:8000/api/restaurants  # API → 200
```

---

## 4. 📌 Cheatsheet rapide

| Action | Commande locale |
|--------|-----------------|
| Voir les services | `ssh ubuntu@VPS "docker service ls"` |
| Voir les logs API | `ssh ubuntu@VPS "docker service logs michelin_server --tail 50"` |
| Redémarrer un service | `ssh ubuntu@VPS "docker service update --force michelin_server"` |
| Voir les conteneurs | `ssh ubuntu@VPS "docker ps"` |
| Exécuter une commande Symfony | `ssh ubuntu@VPS "docker exec \$(docker ps --format '{{.Names}}' \| grep michelin_server\\.) php bin/console <commande>"` |
| Accéder à la BDD PostgreSQL | `ssh ubuntu@VPS "docker exec michelin_db.1.xxx psql -U michelin_user -d michelin_app"` |

---

*Généré le 23 avril 2026 – VPS : 54.37.159.216*
