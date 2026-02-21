# 📚 Guide: Où Trouver Questions et Options dans le Backoffice

## 🎯 Accès Rapide

**URL directe**: `/backoffice/quiz-management`

**Menu Backoffice**: Gestion des Quiz

---

## 📖 Navigation Étape par Étape

### Étape 1: Accéder à la Gestion des Quiz

1. Connecte-toi au backoffice
2. Dans le menu latéral, cherche **"Gestion des Quiz"**
3. Ou tape directement dans l'URL: `http://localhost:8000/backoffice/quiz-management`

### Étape 2: Afficher les Questions d'un Quiz

```
┌─────────────────────────────────────────────────────────┐
│ 📋 Liste des Quiz                                       │
│                                                          │
│ ┌────────────────────────────────────────────────────┐ │
│ │ #1  Mon Quiz de Python  [Actif]                    │ │
│ │ Description du quiz...                             │ │
│ │                                                     │ │
│ │ [Sélectionner] [Voir] [Modifier] [Supprimer]      │ │
│ └────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

**Action**: Clique sur le bouton **"Sélectionner"** à côté du quiz

### Étape 3: Les Questions Apparaissent

```
┌─────────────────────────────────────────────────────────┐
│ 📋 Liste des Quiz                                       │
│                                                          │
│ ┌────────────────────────────────────────────────────┐ │
│ │ #1  Mon Quiz de Python  [Actif]                    │ │
│ │ [Sélectionner ▲] [Voir] [Modifier] [Supprimer]    │ │
│ │                                                     │ │
│ │   ┌─ Questions ──────────────────────────────────┐ │ │
│ │   │ [Nouvelle Question]                          │ │ │
│ │   │                                               │ │ │
│ │   │ #1  Qu'est-ce que Python?                    │ │ │
│ │   │ 📊 Type: choix_multiple  ⭐ 10 points        │ │ │
│ │   │ [Sélectionner] [Voir] [Modifier] [Supprimer]│ │ │
│ │   │                                               │ │ │
│ │   │ #2  Comment déclarer une variable?           │ │ │
│ │   │ 📊 Type: choix_unique  ⭐ 5 points           │ │ │
│ │   │ [Sélectionner] [Voir] [Modifier] [Supprimer]│ │ │
│ │   └───────────────────────────────────────────────┘ │ │
│ └────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

**Action**: Clique sur **"Sélectionner"** à côté d'une question

### Étape 4: Les Options Apparaissent

```
┌─────────────────────────────────────────────────────────┐
│   │ #1  Qu'est-ce que Python?                        │ │
│   │ [Sélectionner ▲] [Voir] [Modifier] [Supprimer]  │ │
│   │                                                   │ │
│   │   ┌─ Options ────────────────────────────────┐  │ │
│   │   │ [Nouvelle Option]                        │  │ │
│   │   │                                           │  │ │
│   │   │ #1  Un langage de programmation          │  │ │
│   │   │ [✓ Correcte] [Modifier] [Supprimer]     │  │ │
│   │   │                                           │  │ │
│   │   │ #2  Un animal                            │  │ │
│   │   │ [✗ Incorrecte] [Modifier] [Supprimer]   │  │ │
│   │   │                                           │  │ │
│   │   │ #3  Un framework web                     │  │ │
│   │   │ [✗ Incorrecte] [Modifier] [Supprimer]   │  │ │
│   │   └───────────────────────────────────────────┘  │ │
└─────────────────────────────────────────────────────────┘
```

---

## 🎨 Code Couleur

### Quiz
- **Fond**: Violet clair transparent
- **Badge ID**: `#1` en bleu
- **Statut**: 
  - 🟢 Vert = Actif
  - 🟡 Jaune = Brouillon
  - 🔴 Rouge = Archivé

### Questions
- **Fond**: Violet moyen transparent
- **Badge ID**: `#1` en violet
- **Icônes**: 📊 Type, ⭐ Points

### Options
- **Fond Correcte**: Vert transparent avec bordure verte
- **Fond Incorrecte**: Rouge transparent avec bordure rouge
- **Badge ID**: 
  - 🟢 Vert pour option correcte
  - 🔴 Rouge pour option incorrecte

---

## ⚡ Actions Disponibles

### Sur un Quiz
- **Sélectionner**: Affiche/masque les questions
- **Voir**: Affiche les détails du quiz
- **Modifier**: Édite le quiz (titre, description, état)
- **Supprimer**: Supprime le quiz (avec confirmation)
- **Nouveau Quiz**: Bouton en haut à droite

### Sur une Question
- **Sélectionner**: Affiche/masque les options
- **Voir**: Affiche les détails de la question
- **Modifier**: Édite la question (texte, type, points)
- **Supprimer**: Supprime la question (avec confirmation)
- **Nouvelle Question**: Bouton dans la section questions

### Sur une Option
- **Modifier**: Édite l'option (texte, correcte/incorrecte)
- **Supprimer**: Supprime l'option (avec confirmation)
- **Nouvelle Option**: Bouton dans la section options

---

## 🔄 Chargement Dynamique (AJAX)

Les questions et options sont chargées dynamiquement via AJAX:

### Routes API
```
GET /quiz/api/{quizId}/questions
→ Retourne toutes les questions d'un quiz

GET /question/api/{questionId}/options
→ Retourne toutes les options d'une question
```

### Avantages
- ✅ Chargement rapide de la page initiale
- ✅ Données chargées uniquement quand nécessaire
- ✅ Interface fluide et réactive
- ✅ Pas de rechargement de page

---

## 🆕 Créer une Nouvelle Question

### Méthode 1: Depuis la liste des questions
1. Clique sur "Sélectionner" pour afficher les questions d'un quiz
2. Clique sur **"Nouvelle Question"** (bouton en haut de la section)
3. Le quiz sera pré-sélectionné automatiquement

### Méthode 2: URL directe
```
/backoffice/question/new?quiz={quizId}
```

### Formulaire Question
- **Texte**: La question elle-même
- **Type**: choix_unique, choix_multiple, vrai_faux, texte_libre
- **Points**: Nombre de points (1-100)
- **Quiz**: Sélection du quiz parent

---

## 🆕 Créer une Nouvelle Option

### Méthode 1: Depuis la liste des options
1. Affiche les questions d'un quiz
2. Clique sur "Sélectionner" pour afficher les options d'une question
3. Clique sur **"Nouvelle Option"** (bouton en haut de la section)
4. La question sera pré-sélectionnée automatiquement

### Méthode 2: URL directe
```
/backoffice/option/new?question={questionId}
```

### Formulaire Option
- **Texte**: Le texte de l'option
- **Est Correcte**: Cocher si c'est la bonne réponse
- **Question**: Sélection de la question parente

---

## 🔍 Hiérarchie Complète

```
Cours
  └─ Chapitre
      └─ Quiz
          └─ Question
              └─ Option
```

### Navigation
1. **Cours**: `/backoffice/cours`
2. **Chapitres**: `/backoffice/cours/{coursId}/chapitres`
3. **Quiz**: `/backoffice/cours/{coursId}/chapitre/{chapitreId}/quizzes`
4. **Questions + Options**: `/backoffice/quiz-management`

---

## 💡 Astuces

### Fermer les Sections
- Clique à nouveau sur "Sélectionner" pour fermer une section
- Les données restent en cache (pas de rechargement)

### Recherche Rapide
- Utilise Ctrl+F dans le navigateur pour chercher un quiz/question
- Les IDs sont affichés pour faciliter la recherche

### Édition Rapide
- Clique directement sur "Modifier" sans ouvrir les détails
- Utilise "Voir" pour consulter sans risque de modification

### Suppression Sécurisée
- Toutes les suppressions demandent confirmation
- La suppression d'un quiz supprime aussi ses questions
- La suppression d'une question supprime aussi ses options

---

## ❓ FAQ

**Q: Je ne vois pas mes questions après avoir cliqué sur "Sélectionner"**
- R: Vérifie que le quiz contient bien des questions. Si vide, utilise "Nouvelle Question"

**Q: Les options ne s'affichent pas**
- R: Vérifie que la question contient des options. Si vide, utilise "Nouvelle Option"

**Q: Comment savoir si une option est correcte?**
- R: Les options correctes ont un fond vert et badge vert "✓ Correcte"
- Les options incorrectes ont un fond rouge et badge rouge "✗ Incorrecte"

**Q: Puis-je avoir plusieurs options correctes?**
- R: Oui, pour les questions de type "choix_multiple"
- Non, pour les questions de type "choix_unique" (une seule bonne réponse)

**Q: Comment modifier l'ordre des questions/options?**
- R: Actuellement, l'ordre est basé sur l'ID (ordre de création)
- Pour réorganiser, il faudrait ajouter un champ "ordre" dans les entités

---

## 🎓 Exemple Complet

### Créer un Quiz avec Questions et Options

1. **Créer le Quiz**
   - Va dans "Gestion des Quiz"
   - Clique "Nouveau Quiz"
   - Titre: "Quiz Python Débutant"
   - Description: "Testez vos connaissances en Python"
   - État: Actif
   - Chapitre: Sélectionne un chapitre
   - Enregistre

2. **Ajouter une Question**
   - Retourne à "Gestion des Quiz"
   - Trouve ton quiz
   - Clique "Sélectionner"
   - Clique "Nouvelle Question"
   - Texte: "Qu'est-ce que Python?"
   - Type: choix_unique
   - Points: 10
   - Enregistre

3. **Ajouter des Options**
   - Clique "Sélectionner" sur ta question
   - Clique "Nouvelle Option"
   - Texte: "Un langage de programmation"
   - Est Correcte: ✓ Coché
   - Enregistre
   
   - Clique "Nouvelle Option"
   - Texte: "Un animal"
   - Est Correcte: ✗ Non coché
   - Enregistre
   
   - Clique "Nouvelle Option"
   - Texte: "Un framework web"
   - Est Correcte: ✗ Non coché
   - Enregistre

4. **Vérifier**
   - Retourne à la liste
   - Clique "Sélectionner" sur le quiz
   - Clique "Sélectionner" sur la question
   - Tu devrais voir tes 3 options avec la première en vert (correcte)

---

## ✅ Checklist de Vérification

Avant de considérer un quiz complet:

- [ ] Le quiz a un titre et une description
- [ ] Le quiz est associé à un chapitre
- [ ] Le quiz contient au moins 1 question
- [ ] Chaque question a un texte clair
- [ ] Chaque question a un type défini
- [ ] Chaque question a des points attribués
- [ ] Chaque question a au moins 2 options
- [ ] Au moins 1 option est marquée comme correcte
- [ ] Les options incorrectes sont bien marquées
- [ ] Le quiz est en état "Actif" pour être visible aux étudiants

---

**Bon travail! 🎉**
