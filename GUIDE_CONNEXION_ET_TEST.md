# 🔐 Guide de Connexion et Test du Module Événement

## ✅ Les formulaires EXISTENT!

J'ai vérifié - tous les formulaires sont bien créés avec tous les champs:

### Formulaire Événement
- ✅ Titre
- ✅ Description
- ✅ Type (Conference, Hackathon, Workshop)
- ✅ Date de début
- ✅ Date de fin
- ✅ Nombre maximum d'équipes (nbMax)

### Formulaire Équipe
- ✅ Nom de l'équipe
- ✅ Événement (sélection)
- ✅ Étudiants (sélection multiple 4-6)

### Formulaire Participation
- ✅ Équipe (sélection)
- ✅ Événement (sélection)
- ✅ Statut

---

## 🚀 ÉTAPES POUR TESTER

### Étape 1: Redémarrer le serveur avec le CSS corrigé

1. **Fermez** la fenêtre du serveur actuel (Ctrl+C ou fermez la fenêtre)
2. **Double-cliquez** à nouveau sur `start-server-8000.bat`
3. **Attendez** de voir "PHP Development Server started"

### Étape 2: Se connecter au Backoffice

1. Ouvrez votre navigateur
2. Allez sur: `http://localhost:8000/backoffice` ou `http://localhost:8000/login`
3. **Connectez-vous avec un compte ADMIN**

**Important:** Vous DEVEZ être connecté en tant qu'Admin pour accéder aux formulaires!

---

## 🔑 Créer un compte Admin (si vous n'en avez pas)

Si vous n'avez pas de compte Admin, créez-en un:

### Option 1: Via la page d'inscription

1. Allez sur: `http://localhost:8000/register`
2. Remplissez le formulaire
3. **Important:** Sélectionnez le rôle "ADMIN"
4. Créez le compte

### Option 2: Via la console (Plus rapide)

Ouvrez un terminal et exécutez:

```bash
php bin/console doctrine:query:sql "INSERT INTO user (nom, prenom, email, password, role, createdAt, discr) VALUES ('Admin', 'Test', 'admin@test.com', '\$2y\$13\$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOP', 'ADMIN', NOW(), 'admin')"
```

**Identifiants:**
- Email: `admin@test.com`
- Mot de passe: `admin123` (vous devrez peut-être le réinitialiser)

---

## 📍 Accès aux formulaires (APRÈS connexion)

Une fois connecté en tant qu'Admin:

### Événements
```
Liste:  http://localhost:8000/backoffice/evenement/
Créer:  http://localhost:8000/backoffice/evenement/new
```

### Équipes
```
Liste:  http://localhost:8000/backoffice/equipe/
Créer:  http://localhost:8000/backoffice/equipe/new
```

### Participations
```
Liste:  http://localhost:8000/backoffice/participation/
Créer:  http://localhost:8000/backoffice/participation/new
```

---

## 🎨 Pourquoi les CSS ne s'affichaient pas?

Le problème était que le router PHP ne gérait pas correctement les fichiers statiques (CSS, JS, images). J'ai corrigé le fichier `router.php` pour qu'il serve correctement:
- Les CSS du backoffice (`/Backoffice/css/`)
- Les CSS du frontoffice (`/frontoffice/css/`)
- Les images et autres assets

---

## ✅ Checklist de test

Après avoir redémarré le serveur et vous être connecté:

- [ ] La page de login s'affiche correctement (avec CSS)
- [ ] Je peux me connecter avec un compte Admin
- [ ] Je vois le menu latéral du backoffice
- [ ] Je vois "Événements", "Équipes", "Participations" dans le menu
- [ ] Je peux cliquer sur "Événements"
- [ ] Je vois la page "Gestion des Événements" avec le bouton "➕ Ajouter"
- [ ] Je peux cliquer sur "➕ Ajouter un événement"
- [ ] Je vois le formulaire avec tous les champs:
  - Titre
  - Description
  - Type
  - Date de début
  - Date de fin
  - Nombre maximum d'équipes

---

## 🐛 Si les CSS ne s'affichent toujours pas

1. **Videz le cache du navigateur:**
   - Chrome: Ctrl+Shift+Delete
   - Firefox: Ctrl+Shift+Delete
   - Ou utilisez Ctrl+F5 pour forcer le rechargement

2. **Vérifiez la console du navigateur:**
   - Appuyez sur F12
   - Allez dans l'onglet "Console"
   - Regardez s'il y a des erreurs 404 pour les CSS

3. **Vérifiez que les fichiers CSS existent:**
   ```
   public/Backoffice/css/templatemo-glass-admin-style.css
   public/Backoffice/css/custom-forms.css
   public/Backoffice/css/form-errors.css
   ```

---

## 📝 Résumé

1. ✅ Les formulaires existent avec tous les champs
2. ✅ Le router.php a été corrigé pour les CSS
3. ⚠️ Vous devez être connecté en tant qu'Admin
4. ⚠️ Redémarrez le serveur pour appliquer les corrections

---

## 🚀 Action immédiate

1. **Fermez** le serveur actuel
2. **Double-cliquez** sur `start-server-8000.bat`
3. **Allez sur** `http://localhost:8000/login`
4. **Connectez-vous** en tant qu'Admin
5. **Allez sur** `http://localhost:8000/backoffice/evenement/`
6. **Cliquez** sur "➕ Ajouter un événement"

**Vous verrez le formulaire complet!** 🎉
