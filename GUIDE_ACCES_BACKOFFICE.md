# 🎯 Guide d'accès aux formulaires de gestion - Module Événement

## 📍 Comment accéder aux formulaires dans le Backoffice

### 1️⃣ Connexion au Backoffice

**URL**: `http://localhost:8000/backoffice` ou `http://localhost:8000/login`

Connectez-vous avec un compte **Admin**.

---

## 🎪 Gestion des Événements

### Accès via le menu latéral:
1. Dans le menu de gauche, cherchez la section **"Gestion"**
2. Cliquez sur **"Événements"** (icône calendrier 📅)

### Routes directes:
- **Liste des événements**: `http://localhost:8000/backoffice/evenement/`
- **Créer un événement**: `http://localhost:8000/backoffice/evenement/new`
- **Modifier un événement**: `http://localhost:8000/backoffice/evenement/{id}/edit`
- **Voir un événement**: `http://localhost:8000/backoffice/evenement/{id}`
- **Supprimer un événement**: `http://localhost:8000/backoffice/evenement/{id}/delete`

### Formulaire de création d'événement:
```
Champs disponibles:
✅ Titre (obligatoire)
✅ Description (obligatoire)
✅ Type (Conference, Hackathon, Workshop)
✅ Date de début (obligatoire)
✅ Date de fin (obligatoire)
✅ Nombre maximum d'équipes (obligatoire, > 0)
```

---

## 👥 Gestion des Équipes

### Accès via le menu latéral:
1. Dans le menu de gauche, section **"Gestion"**
2. Cliquez sur **"Équipes"** (icône groupe 👥)

### Routes directes:
- **Liste des équipes**: `http://localhost:8000/backoffice/equipe/`
- **Créer une équipe**: `http://localhost:8000/backoffice/equipe/new`
- **Modifier une équipe**: `http://localhost:8000/backoffice/equipe/{id}/edit`
- **Voir une équipe**: `http://localhost:8000/backoffice/equipe/{id}`
- **Supprimer une équipe**: `http://localhost:8000/backoffice/equipe/{id}/delete`

### Formulaire de création d'équipe:
```
Champs disponibles:
✅ Nom de l'équipe (obligatoire)
✅ Événement (sélection dans la liste)
✅ Étudiants (sélection multiple: 4 à 6 étudiants)
   💡 Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs étudiants
```

---

## ✅ Gestion des Participations

### Accès via le menu latéral:
1. Dans le menu de gauche, section **"Gestion"**
2. Cliquez sur **"Participations"** (icône check ✓)

### Routes directes:
- **Liste des participations**: `http://localhost:8000/backoffice/participation/`
- **Créer une participation**: `http://localhost:8000/backoffice/participation/new`
- **Modifier une participation**: `http://localhost:8000/backoffice/participation/{id}/edit`
- **Voir une participation**: `http://localhost:8000/backoffice/participation/{id}`
- **Supprimer une participation**: `http://localhost:8000/backoffice/participation/{id}/delete`

### Formulaire de création de participation:
```
Champs disponibles:
✅ Équipe (sélection dans la liste)
✅ Événement (sélection dans la liste)
✅ Statut (En attente, Accepté, Refusé)
   💡 Le statut sera validé automatiquement selon les règles
```

---

## 🎨 Apparence dans le menu

Le menu latéral du backoffice affiche:

```
┌─────────────────────────────┐
│ Main Menu                   │
│  □ Dashboard                │
│  □ Analytics                │
│                             │
│ Gestion                     │
│  □ Gestion Quiz             │
│  📅 Événements         ← ICI│
│  👥 Équipes            ← ICI│
│  ✓ Participations      ← ICI│
│                             │
│ Système                     │
│  □ Users                    │
│  □ Settings                 │
└─────────────────────────────┘
```

---

## 🚀 Workflow recommandé

### Pour créer un événement complet:

1. **Créer un Événement**
   - Menu > Gestion > Événements
   - Cliquer sur "➕ Ajouter un événement"
   - Remplir le formulaire
   - Cliquer sur "Créer"

2. **Créer des Équipes**
   - Menu > Gestion > Équipes
   - Cliquer sur "➕ Ajouter une équipe"
   - Sélectionner l'événement créé
   - Sélectionner 4 à 6 étudiants
   - Cliquer sur "Créer"

3. **Créer des Participations**
   - Menu > Gestion > Participations
   - Cliquer sur "➕ Ajouter une participation"
   - Sélectionner une équipe
   - Sélectionner un événement
   - Le statut sera validé automatiquement
   - Cliquer sur "Créer"

---

## 📊 Tableaux de gestion

### Page Liste des Événements
Affiche un tableau avec:
- Titre
- Type
- Date Début
- Date Fin
- Statut (badge coloré)
- Places Max
- Actions (👁 Voir | ✏ Modifier | 🗑 Supprimer)

### Page Liste des Équipes
Affiche un tableau avec:
- Nom
- Événement
- Nombre d'étudiants
- Actions (👁 Voir | ✏ Modifier | 🗑 Supprimer)

### Page Liste des Participations
Affiche un tableau avec:
- Équipe
- Événement
- Statut (badge coloré: Accepté/Refusé/En attente)
- Actions (👁 Voir | ✏ Modifier | 🗑 Supprimer)

---

## 💡 Astuces

1. **Boutons d'action**:
   - 👁 **Voir**: Affiche les détails complets
   - ✏ **Modifier**: Ouvre le formulaire d'édition
   - 🗑 **Supprimer**: Supprime après confirmation

2. **Messages de confirmation**:
   - Après chaque action, un message vert apparaît en haut de la page
   - "Événement créé avec succès"
   - "Équipe modifiée avec succès"
   - etc.

3. **Navigation**:
   - Utilisez le bouton "Retour à la liste" pour revenir
   - Utilisez le bouton "Annuler" pour annuler une création/modification

---

## 🐛 Si vous ne voyez pas les liens

1. **Vider le cache**:
   ```bash
   php bin/console cache:clear
   ```

2. **Vérifier que vous êtes connecté en tant qu'Admin**

3. **Rafraîchir la page** (Ctrl+F5 ou Cmd+Shift+R)

4. **Vérifier les routes**:
   ```bash
   php bin/console debug:router | findstr "evenement equipe participation"
   ```

---

## ✅ Checklist de vérification

- [ ] Je suis connecté au backoffice
- [ ] Je vois la section "Gestion" dans le menu
- [ ] Je vois "Événements", "Équipes", "Participations" dans le menu
- [ ] Je peux cliquer sur ces liens
- [ ] Les pages de liste s'affichent correctement
- [ ] Les boutons "Ajouter" sont visibles
- [ ] Les formulaires s'affichent correctement

---

Bon test! 🎉
