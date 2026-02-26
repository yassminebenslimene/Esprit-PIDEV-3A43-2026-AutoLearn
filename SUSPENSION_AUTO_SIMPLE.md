# 🤖 Suspension Automatique - Guide Simple

## ✅ C'est Quoi?

Un système qui suspend automatiquement les étudiants inactifs depuis 7 jours et envoie des emails à l'étudiant ET aux admins.

---

## 🚀 Comment Utiliser?

### Test (sans rien modifier)
```bash
php bin/console app:auto-suspend-inactive-users --dry-run
```

### Exécution Réelle
```bash
php bin/console app:auto-suspend-inactive-users
```

### Script Interactif
```bash
.\run_auto_suspend.bat
```

---

## ⏰ Automatiser (Exécution Quotidienne)

### Windows
1. Ouvrir "Planificateur de tâches"
2. Créer une tâche:
   - Quotidien à 2h du matin
   - Programme: `C:\php\php.exe`
   - Arguments: `bin/console app:auto-suspend-inactive-users`
   - Dossier: `C:\Users\hitec\OneDrive\Bureau\AutoLearn\autolearn`

---

## 📧 Emails Envoyés

- ✅ **Étudiant**: "Votre compte a été suspendu pour inactivité"
- ✅ **Admins**: "L'étudiant X a été suspendu automatiquement"

---

## 🎯 Fonctionnalités

- ✅ Détecte les étudiants inactifs depuis 7+ jours
- ✅ Suspend automatiquement
- ✅ Envoie des emails
- ✅ Bloque la connexion
- ✅ Réactivation manuelle possible

---

## 📚 Documentation Complète

- **SUSPENSION_AUTO_RESUME.md** - Résumé détaillé
- **SUSPENSION_AUTOMATIQUE_GUIDE.md** - Guide complet

---

**C'est une fonctionnalité métier avancée!** 🚀
