# 🔍 Guide de la Recherche Globale

## ✅ Fonctionnalité implémentée!

Un système de recherche global permet maintenant aux étudiants de rechercher dans TOUS les contenus de la plateforme.

---

## 📍 Où trouver la barre de recherche?

### Dans la navbar (en haut)
```
┌─────────────────────────────────────────────────────────────┐
│  AUTOLEARN    [Rechercher...]  🔍                           │
│                                                              │
│  Home  Cours  Events  Challenge  Contact  🌐 FR ▼  Login   │
└─────────────────────────────────────────────────────────────┘
                  ↑
            Barre de recherche
```

---

## 🎯 Ce que vous pouvez rechercher

### 1. Cours
- Titre du cours
- Description du cours
- Exemple: "Python", "JavaScript", "Java"

### 2. Chapitres
- Titre du chapitre
- Contenu du chapitre
- Exemple: "Variables", "Fonctions", "Classes"

### 3. Quiz
- Titre du quiz
- Description du quiz
- Exemple: "Quiz Python", "Test JavaScript"

### 4. Événements
- Titre de l'événement
- Description de l'événement
- Exemple: "Hackathon", "Conférence"

### 5. Challenges
- Titre du challenge
- Description du challenge
- Exemple: "Défi algorithmique", "Challenge web"

---

## 🚀 Comment utiliser la recherche?

### Étape 1: Taper votre recherche
1. Cliquer dans la barre de recherche
2. Taper au moins 2 caractères
3. Exemple: "python"

### Étape 2: Lancer la recherche
- Appuyer sur **Entrée**
- Ou cliquer sur l'icône **🔍**

### Étape 3: Voir les résultats
Une page s'ouvre avec tous les résultats trouvés:
- Cours correspondants
- Chapitres correspondants
- Quiz correspondants
- Événements correspondants
- Challenges correspondants

---

## 📊 Page de résultats

### En-tête
```
╔═══════════════════════════════════════════════════════════╗
║           🔍 Résultats de recherche pour                  ║
║                    "python"                               ║
║              5 résultats trouvés                          ║
╚═══════════════════════════════════════════════════════════╝
```

### Carte de résultat
```
┌─────────────────────────────────────────────────────────┐
│ [COURS] 📖                                              │
│                                                          │
│ Introduction à Python                                    │
│                                                          │
│ Apprenez les bases de Python avec des exemples         │
│ pratiques et des exercices...                           │
│                                                          │
│ [Voir les détails →]                                    │
└─────────────────────────────────────────────────────────┘
```

### Types de résultats avec couleurs

**Cours** (Violet):
```
[COURS] 📖 Introduction à Python
```

**Chapitre** (Rose):
```
[CHAPITRE] 📄 Variables et Types de données
```

**Quiz** (Bleu):
```
[QUIZ] ❓ Test Python - Niveau 1
```

**Événement** (Vert):
```
[ÉVÉNEMENT] 📅 Hackathon Python 2026
```

**Challenge** (Orange):
```
[DÉFI] 🏆 Challenge Algorithmique
```

---

## 🎨 Fonctionnalités de la recherche

### 1. Recherche intelligente
- ✅ Recherche dans les titres
- ✅ Recherche dans les descriptions
- ✅ Recherche dans le contenu
- ✅ Insensible à la casse (majuscules/minuscules)

### 2. Résultats groupés par type
- ✅ Cours en premier
- ✅ Chapitres ensuite
- ✅ Quiz après
- ✅ Événements et challenges

### 3. Limitation des résultats
- ✅ Maximum 10 résultats par type
- ✅ Total maximum: 50 résultats
- ✅ Résultats les plus pertinents

### 4. Multilingue
- ✅ Placeholder traduit
- ✅ Résultats traduits
- ✅ Messages traduits

---

## 🧪 Exemples de recherche

### Recherche de cours
```
Recherche: "python"
Résultats:
  - Cours: Introduction à Python
  - Chapitre: Variables Python
  - Quiz: Test Python Débutant
```

### Recherche de concept
```
Recherche: "fonction"
Résultats:
  - Chapitre: Les fonctions en JavaScript
  - Chapitre: Fonctions Python avancées
  - Quiz: Quiz sur les fonctions
```

### Recherche d'événement
```
Recherche: "hackathon"
Résultats:
  - Événement: Hackathon 2026
  - Challenge: Défi Hackathon
```

---

## 📱 Responsive

La recherche fonctionne sur tous les appareils:
- ✅ Desktop
- ✅ Tablette
- ✅ Mobile

---

## 🌍 Traductions

### Français
- Placeholder: "Rechercher des cours, chapitres, quiz..."
- Résultats: "Résultats de recherche pour"
- Aucun résultat: "Aucun résultat trouvé"

### Anglais
- Placeholder: "Search for courses, chapters, quizzes..."
- Results: "Search results for"
- No results: "No results found"

### Espagnol
- Placeholder: "Buscar cursos, capítulos, cuestionarios..."
- Resultados: "Resultados de búsqueda para"
- Sin resultados: "No se encontraron resultados"

### Arabe
- Placeholder: "البحث عن دورات، فصول، اختبارات..."
- النتائج: "نتائج البحث عن"
- لا توجد نتائج: "لم يتم العثور على نتائج"

---

## 🎯 Test rapide

### 1. Ouvrir la page d'accueil
```
http://127.0.0.1:8000
```

### 2. Cliquer dans la barre de recherche
En haut, à côté du logo

### 3. Taper "python"

### 4. Appuyer sur Entrée

### 5. Voir les résultats
Une page s'ouvre avec tous les cours, chapitres, quiz contenant "python"

---

## 🔧 Détails techniques

### Route
```
/search?q=python
```

### Contrôleur
```php
src/Controller/SearchController.php
```

### Template
```
templates/frontoffice/search/results.html.twig
```

### Méthode de recherche
- Recherche SQL avec `LIKE`
- Paramètre: `%query%`
- Limite: 10 résultats par type

---

## ✅ Checklist de test

- [ ] Serveur démarré: http://127.0.0.1:8000
- [ ] Cache vidé: `php bin/console cache:clear`
- [ ] Barre de recherche visible dans la navbar
- [ ] Placeholder traduit selon la langue
- [ ] Recherche fonctionne (minimum 2 caractères)
- [ ] Page de résultats s'affiche
- [ ] Résultats groupés par type
- [ ] Cartes de résultats avec couleurs
- [ ] Boutons "Voir les détails" fonctionnent
- [ ] Message "Aucun résultat" si rien trouvé

---

## 🎉 Résultat final

La recherche globale permet maintenant de:
- ✅ Rechercher dans TOUS les contenus
- ✅ Voir les résultats groupés par type
- ✅ Accéder rapidement aux détails
- ✅ Utiliser dans toutes les langues

**Testez maintenant**: http://127.0.0.1:8000

Tapez "python" dans la barre de recherche! 🔍
