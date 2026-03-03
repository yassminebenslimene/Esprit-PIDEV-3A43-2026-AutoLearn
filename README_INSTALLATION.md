# 📥 GUIDE D'INSTALLATION COMPLET

## 🎯 Pour toi (après git pull)

### Solution rapide (1 commande) :
```bash
.\MISE_A_JOUR_COMPLETE.bat
```

Ce script fait TOUT automatiquement ! ✅

---

## 🎯 Pour ton ami (première installation)

### Étape 1 : Cloner le projet
```bash
git clone [url-du-repo]
cd autolearn
```

### Étape 2 : Exécuter le script
```bash
.\MISE_A_JOUR_COMPLETE.bat
```

### Étape 3 : Créer .env.local
```env
# .env.local
GROQ_API_KEY=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
MAILER_DSN=sendgrid+api://[ta-cle-sendgrid]@default
WEATHER_API_KEY=5177b7da6160976397c624428cd12f3d
```

### Étape 4 : Démarrer
```bash
symfony serve
```

Ouvrir : http://127.0.0.1:8000

---

## 📋 SCRIPTS DISPONIBLES

### 1. MISE_A_JOUR_COMPLETE.bat ⭐ (RECOMMANDÉ)
**Utilisation :** Après chaque `git pull`

**Ce qu'il fait :**
- ✅ Désactive les bundles non installés
- ✅ Installe les dépendances (composer install)
- ✅ Met à jour la base de données
- ✅ Vide le cache
- ✅ Vérifie l'installation

**Commande :**
```bash
.\MISE_A_JOUR_COMPLETE.bat
```

---

### 2. NETTOYAGE_FINAL.bat
**Utilisation :** Si problèmes de bundles manquants

**Ce qu'il fait :**
- ✅ Désactive CalendarBundle
- ✅ Désactive Workflow
- ✅ Désactive SimpleThingsEntityAudit
- ✅ Vide le cache

**Commande :**
```bash
.\NETTOYAGE_FINAL.bat
```

---

### 3. nettoyer-apres-pull.bat
**Utilisation :** Nettoyage simple

**Ce qu'il fait :**
- ✅ Supprime fichiers de configuration problématiques
- ✅ Composer install
- ✅ Cache clear

**Commande :**
```bash
.\nettoyer-apres-pull.bat
```

---

## 🗄️ COMMANDES BASE DE DONNÉES

### Mettre à jour le schéma
```bash
php bin/console doctrine:schema:update --force
```

### Voir les changements avant d'appliquer
```bash
php bin/console doctrine:schema:update --dump-sql
```

### Créer une migration
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## 🚨 RÉSOLUTION DES ERREURS COURANTES

### Erreur : "Class CalendarBundle not found"
**Solution :**
```bash
.\NETTOYAGE_FINAL.bat
```

### Erreur : "Service state_machine.evenement_publishing not found"
**Solution :**
```bash
.\NETTOYAGE_FINAL.bat
```

### Erreur : "Column 'duree' not found"
**Solution :**
```bash
php bin/console doctrine:schema:update --force
php bin/console cache:clear
```

### Erreur : "There is no extension able to load configuration"
**Solution :**
```bash
.\NETTOYAGE_FINAL.bat
```

---

## 📊 MODULES FONCTIONNELS

### ✅ Actifs
- Gestion des cours et chapitres
- Quiz avec IA (Groq)
- Notifications
- Recherche
- Traduction (8 langues)
- Explainer IA avec synthèse vocale
- Générateur de chapitres IA
- Système de progression
- Bundle PDF
- Communautés
- Challenges
- Gestion des utilisateurs

### ⚠️ Désactivés (nécessitent installation)
- Module Événement (nécessite symfony/workflow)
- CalendarBundle (nécessite tattali/calendar-bundle)
- SimpleThingsEntityAuditBundle (nécessite simplethings/entity-audit-bundle)

---

## 🔑 CLÉS API NÉCESSAIRES

### Dans .env.local :
```env
# IA Groq (OBLIGATOIRE pour les fonctionnalités IA)
GROQ_API_KEY=gsk_...

# Email SendGrid (OBLIGATOIRE pour les emails)
MAILER_DSN=sendgrid+api://...@default

# Météo (OPTIONNEL)
WEATHER_API_KEY=...

# Hugging Face (OPTIONNEL)
HUGGINGFACE_API_KEY=...
```

---

## 📝 STRUCTURE DU PROJET

```
autolearn/
├── src/
│   ├── Controller/          # Contrôleurs
│   ├── Entity/              # Entités Doctrine
│   ├── Service/             # Services métier
│   ├── Repository/          # Repositories
│   ├── Command/             # Commandes console
│   ├── EventSubscriber/     # Event subscribers
│   └── Bundle/              # Bundles custom
├── templates/
│   ├── backoffice/          # Templates admin
│   └── frontoffice/         # Templates utilisateur
├── public/                  # Assets publics
├── config/                  # Configuration
└── migrations/              # Migrations BDD
```

---

## 🎓 FONCTIONNALITÉS IA

### 1. Générateur de chapitres
- Génère automatiquement des chapitres
- Basé sur le titre du cours
- Utilise Groq API (Llama 3.3)

### 2. Explainer IA
- Explique les chapitres
- Synthèse vocale intégrée
- Niveaux : débutant/avancé

### 3. Traduction
- 8 langues supportées
- Cache de 7 jours
- Traduction instantanée

### 4. Quiz IA
- Génération automatique de quiz
- Correction automatique
- Tuteur IA pour explications

---

## 🔄 WORKFLOW GIT

### Pour toi (développeur principal)
```bash
# Après modifications
git add .
git commit -m "Description"
git push origin [ta-branche]
```

### Pour ton ami (après ton push)
```bash
# Récupérer tes modifications
git pull origin [ta-branche]

# Mettre à jour
.\MISE_A_JOUR_COMPLETE.bat
```

---

## 📞 SUPPORT

### Problèmes courants
1. Erreur de bundle → `NETTOYAGE_FINAL.bat`
2. Erreur de colonne BDD → `doctrine:schema:update --force`
3. Erreur de cache → `php bin/console cache:clear`
4. Erreur de dépendances → `composer install`

### Commande de diagnostic
```bash
php bin/console about
php bin/console debug:container
php bin/console doctrine:schema:validate
```

---

## ✅ CHECKLIST INSTALLATION

- [ ] Git clone
- [ ] Exécuter `MISE_A_JOUR_COMPLETE.bat`
- [ ] Créer `.env.local` avec les clés API
- [ ] Vérifier la base de données
- [ ] Lancer `symfony serve`
- [ ] Ouvrir http://127.0.0.1:8000
- [ ] Tester le login
- [ ] Tester une fonctionnalité IA

---

**Tout est prêt ! 🎉**

Pour toute question, consulte les fichiers :
- `MISE_A_JOUR_BDD.md` - Problèmes de base de données
- `RESOLUTION_COMPLETE_WORKFLOW.md` - Problèmes de workflow
- `CORRECTIONS_APRES_PULL.md` - Historique des corrections
- `GUIDE_NOTIFICATIONS.md` - Système de notifications
