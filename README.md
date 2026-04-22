# 🕵️‍♂️ Michelin Guide - AI Fact-Check Agent

Bienvenue dans le module de "Fact-Check" IA du Guide Michelin. Ce projet utilise un Agent LLM dynamique pour valider ou démonter les tendances virales culinaires en les croisant avec l'expertise Michelin.

## 🚀 Installation & Lancement

### 1. Configuration de l'environnement
Dans le dossier `./server`, créez ou modifiez le fichier `.env` pour ajouter vos clés API :
```bash
# Activation de l'IA réelle
MISTRAL_API_KEY=votre_cle_mistral
```

### 2. Initialisation de la Base de Données
Depuis le dossier `./server` :
```bash
# Lancer les migrations pour créer les tables (FactCheck et KnowledgeBase)
php bin/console doctrine:migrations:migrate --no-interaction
```

### 3. Lancement des serveurs
Exécutez ces commandes dans des terminaux séparés :
- **Backend** : `cd server && php -S localhost:8000 -t public`
- **Frontend** : `cd client && npm run dev`

---

## 🎓 Entraînement de l'Agent IA

L'agent est piloté par une **KnowledgeBase** (Base de Connaissances) stockée en base de données. Il croise les données scrapées des liens avec ces informations prioritaires.

### Peupler la base avec les données réelles (Seeds)
Depuis le dossier `./server` :
```bash
# Importer les données d'entraînement initiales (Septime, Grolet, etc.)
php seed_knowledge.php

# (Optionnel) Importer un historique d'analyses factices
php seed_direct.php
```

### Ajouter de la connaissance manuellement
Utilisez la commande Symfony dédiée pour entraîner l'agent sur de nouveaux restaurants :
```bash
php bin/console app:add-knowledge "Nom du Resto" 8.5 "mot-clé1,mot-clé2"
```
*L'agent utilisera ces mots-clés pour reconnaître automatiquement le restaurant dans les URLs soumises.*

---

## 🛠️ Architecture du Module

- **Agent IA** : [server/src/Service/FactCheckAgent.php](./server/src/Service/FactCheckAgent.php)
  - Scrape les métadonnées (Title/Meta) des liens viraux.
  - Interroge la base de connaissances locale.
  - Utilise Mistral AI pour générer un verdict structuré.
- **Réactivité** : Le site affiche en temps réel les dernières analyses grâce à une interface Vue.js branchée sur l'API `/api/fact-check`.
- **Persistance** : Toutes les preuves sont archivées en SQLite.

---

## 📝 Commandes Utiles

| Commande | Action |
| :--- | :--- |
| `php bin/console app:add-knowledge` | Entraîne l'agent IA manuellement |
| `php bin/console doctrine:migrations:migrate` | Met à jour le schéma de la base de données |
| `php -S localhost:8000 -t public` | Lance l'API locale |
| `npm run dev` | Lance l'interface utilisateur |
