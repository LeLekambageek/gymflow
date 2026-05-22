# 🚀 Guide Déploiement sur Railway

## 📋 Prérequis

- Compte GitHub (pour lier ton dépôt)
- Compte Railway (gratuit) : https://railway.app
- Git installé localement
- Ton code pushé sur GitHub

---

## 1️⃣ Préparer ton dépôt GitHub

### ✅ Vérifier que le code est sur GitHub

```bash
git remote -v  # Doit afficher l'URL GitHub
git push      # Envoyer le code sur GitHub
```

### ✅ Vérifier les fichiers importants

S'assurer que tu as :
- ✅ `composer.json` - Dépendances PHP
- ✅ `.env.example` - Template variables d'environnement
- ✅ `database/migrations/` - Migrations
- ✅ `Procfile` - Instructions de démarrage (à créer)

---

## 2️⃣ Créer le fichier `Procfile` (IMPORTANT!)

Ce fichier dit à Railway comment lancer ton app.

```
web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --force
```

⚠️ À créer à la **racine du projet** : `c:\xampp\htdocs\gymflow\Procfile`

---

## 3️⃣ Créer le fichier `.railwayignore`

```
node_modules/
.env.local
.env.*.local
storage/logs/
bootstrap/cache/*.php
```

⚠️ À créer à la **racine du projet** : `c:\xampp\htdocs\gymflow\.railwayignore`

---

## 4️⃣ Créer `.env.production`

```
APP_NAME=GymFlow
APP_ENV=production
APP_KEY=base64:... # Sera généré
APP_DEBUG=false
APP_URL=https://ton-app.railway.app

DB_CONNECTION=mysql
DB_HOST={{MYSQL_HOST}}
DB_PORT={{MYSQL_PORT}}
DB_DATABASE={{MYSQL_DB}}
DB_USERNAME={{MYSQL_USER}}
DB_PASSWORD={{MYSQL_PASSWORD}}

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

⚠️ Les `{{VARIABLE}}` seront remplacées par Railway

---

## 5️⃣ Se connecter à Railway

1. Va sur https://railway.app
2. Clique sur **"New Project"**
3. Connecte ton **compte GitHub**
4. Sélectionne ton dépôt **GymFlow**

---

## 6️⃣ Configurer l'app sur Railway

### Étape 1 : Ajouter MySQL

1. Dans Railway Dashboard
2. Clique sur **"+ Add"** → **MySQL**
3. Railway va provisionner une base de données

### Étape 2 : Configurer les variables d'environnement

Railway détecte automatiquement les variables MySQL. Tu dois ajouter :

```
APP_KEY=base64:xxx          # Générer avec: php artisan key:generate
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ton-url-railway.app
```

### Étape 3 : Déployer

Railway va déployer automatiquement quand tu pousses du code sur GitHub.

---

## 7️⃣ Générer la clé APP_KEY

**Avant** le déploiement, génère la clé :

```bash
php artisan key:generate --show
```

Copie la valeur `base64:...` et ajoute-la dans les variables d'environnement Railroad.

---

## 8️⃣ Étapes finales de déploiement

### Commit et Push

```bash
git add Procfile .railwayignore
git commit -m "Add Railway deployment files"
git push origin main  # ou ta branche principale
```

Railway va détecter les changements et redéployer automatiquement.

### Vérifier le déploiement

1. Va sur ton dashboard Railway
2. Clique sur ton app
3. Onglet "Deployments" → Vérifier le status
4. Onglet "Logs" → Voir les logs en temps réel

---

## 9️⃣ Migrer la base de données

Le `Procfile` exécute automatiquement :
```
php artisan migrate --force
```

Si tu dois rouler le seeder :

```bash
railway run php artisan db:seed
```

---

## 🔟 Accéder à ton app

Une fois déployée, tu auras une URL type :
```
https://gymflow-production-abc123.railway.app
```

---

## ⚠️ Problèmes courants

| Problème | Solution |
|----------|----------|
| "No such file or directory: Procfile" | Vérifier que Procfile est à la racine |
| "SQLSTATE[HY000]" | Vérifier les variables DB dans Railway |
| "APP_KEY is not set" | Ajouter APP_KEY dans les variables |
| "Class not found" | Faire `php artisan optimize:clear` puis redéployer |

---

## 📱 Architecture déploiement

```
GitHub Repo
    ↓
   git push
    ↓
Railway (détecte changements)
    ↓
Build (composer install, npm build)
    ↓
Migrate (php artisan migrate)
    ↓
Deploy (démarrage app)
    ↓
🚀 App en ligne!
```

---

## 💡 Optimisations

### Cache la config
```bash
php artisan config:cache
php artisan route:cache
```

### Mode production
```
APP_ENV=production
APP_DEBUG=false
```

### Variables sensibles
Ne JAMAIS commit `.env` - toujours utiliser les variables Railway!

---

## 📞 Support Railway

- Docs: https://docs.railway.app
- Laravel guide: https://docs.railway.app/guides/laravel
- Discord: https://discord.gg/railway

---

## ✅ Checklist avant déploiement

- [ ] Code sur GitHub
- [ ] `Procfile` créé à la racine
- [ ] `.railwayignore` créé
- [ ] `composer.json` à jour
- [ ] Migrations prêtes
- [ ] APP_KEY généré
- [ ] Variables d'environnement configurées
- [ ] MySQL ajouté dans Railway
- [ ] `git push` exécuté
- [ ] Logs vérifiés

