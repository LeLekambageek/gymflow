# 🔐 PROCÉDURE DE SÉCURITÉ ADMIN - RÉCUPÉRATION DES ACCÈS

## ⚠️ Situation d'urgence : Le propriétaire a oublié son mot de passe

### Étape 1 : Créer un compte admin via terminal

Si vous avez accès au serveur via SSH ou Artisan, exécutez :

```bash
php artisan admin:create
```

Vous serez invité à entrer :
- 📝 Nom de l'administrateur
- 📧 Email unique
- 🔑 Mot de passe (min. 8 caractères)

### Étape 2 : Se connecter avec le compte admin

1. Allez sur `/login`
2. Connectez-vous avec les identifiants créés
3. Accédez au tableau de bord admin : `/admin/dashboard`

### Étape 3 : Réinitialiser le mot de passe du propriétaire

1. Depuis `/admin/dashboard`, trouvez l'utilisateur "Owner" (propriétaire)
2. Cliquez sur **"🔑 Réinitialiser mot de passe"**
3. Entrez un nouveau mot de passe temporaire
4. Le propriétaire pourra se connecter et changer son mot de passe dans son profil

---

## 🎯 Permissions de l'administrateur

Le compte **admin** peut :
- ✅ Réinitialiser les mots de passe de tous les utilisateurs
- ✅ Modifier les noms et emails des utilisateurs
- ✅ Changer les rôles (owner ↔ manager ↔ admin)
- ✅ Consulter tous les utilisateurs

Le compte **admin** **NE PEUT PAS** :
- ❌ Accéder aux données métier (membres, cours, paiements, etc.)
- ❌ Accéder au tableau de bord financier
- ❌ Gérer le staff ou les cours

---

## 🛡️ Recommandations de sécurité

1. **Créer UN SEUL compte admin** et le protéger avec un mot de passe très fort
2. **Ne pas utiliser le compte admin au quotidien** - réservé aux urgences
3. **Changer régulièrement** le mot de passe admin
4. **Limiter les accès** au créateur de compte admin
5. **Documenter** qui a accès au compte admin et quand il a été créé

---

## 📋 Rôles disponibles

| Rôle | Permissions |
|------|-------------|
| **Admin** | Gestion des utilisateurs et réinitialisation de mots de passe |
| **Owner** | Accès complet à tous les modules |
| **Manager** | Gestion des membres et des abonnements (accès limité) |

---

## 🔄 Procédure complète en cas d'urgence

```
1. Accès terminal/SSH → php artisan admin:create
2. Remplir les infos admin
3. Se connecter avec compte admin
4. Aller à /admin/dashboard
5. Réinitialiser le mot de passe du propriétaire
6. Propriétaire reçoit le mot de passe temporaire
7. Propriétaire se connecte et change son mot de passe
8. Optionnel : Supprimer le compte admin après résolution
```

---

## ❓ FAQ

**Q: Puis-je avoir plusieurs comptes admin?**
- R: Oui, il suffit de réexécuter `php artisan admin:create`

**Q: Comment supprimer un compte admin?**
- R: Depuis le dashboard admin, modifiez son rôle à "manager" ou supprimez l'utilisateur

**Q: Le mot de passe admin est-il sécurisé?**
- R: Oui, il est hashé avec bcrypt (impossible à récupérer)

**Q: Comment archiver le compte admin après utilisation?**
- R: Changez son rôle à "manager" et bloquez-le de toute action
