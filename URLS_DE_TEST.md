# 🎯 URLs de Test - Système de Traduction

## ✅ Serveur démarré sur: http://127.0.0.1:8000

---

## 🧪 Page de test interactive

**URL**: http://127.0.0.1:8000/test-traduction.html

Cette page contient:
- ✅ Liens directs vers toutes les pages à tester
- ✅ Instructions détaillées
- ✅ Liste des traductions attendues

---

## 📍 Pages principales à tester

### 1. Page d'accueil
**URL**: http://127.0.0.1:8000

**Éléments traduits**:
- Navigation (Home, Events, Courses, Community)
- Sélecteur de langue (globe 🌐)
- Boutons de connexion/inscription

---

### 2. Liste des chapitres ⭐ RECOMMANDÉ
**URL**: http://127.0.0.1:8000/chapitre

**Éléments traduits**:
- Titre: "Découvrez nos chapitres" → "Descubre nuestros capítulos" (ES)
- Badge: "Chapitre X" → "Capítulo X" (ES)
- Bouton: "Lire le chapitre" → "Leer capítulo" (ES)
- Bouton: "Passer le quiz" → "Hacer el cuestionario" (ES)
- Lien: "Retour aux cours" → "Volver a los cursos" (ES)

**Test**:
```
1. Ouvrir http://127.0.0.1:8000/chapitre
2. Cliquer sur le globe 🌐 (en haut à droite)
3. Choisir "Español"
4. Vérifier que TOUT est en espagnol
```

---

### 3. Détail d'un chapitre
**URL**: http://127.0.0.1:8000/chapitre/{id}

Remplacer `{id}` par l'ID d'un chapitre existant (exemple: 1, 2, 3...)

**Éléments traduits**:
- Badge: "Chapitre 7" → "Capítulo 7" (ES)
- Métadonnée: "Lecture estimée : 5 min" → "Lectura estimada : 5 min" (ES)
- Métadonnée: "Niveau débutant" → "Nivel principiante" (ES)
- Titre section: "Ressources complémentaires" → "Recursos adicionales" (ES)
- Bouton: "Retour à la liste des chapitres" → "Volver a la lista de capítulos" (ES)
- Sélecteur de langue dans la page

**Test**:
```
1. Ouvrir http://127.0.0.1:8000/chapitre/1 (ou un autre ID)
2. Cliquer sur le globe 🌐
3. Choisir "Español"
4. Vérifier toutes les traductions
```

---

### 4. Liste des cours (Backoffice)
**URL**: http://127.0.0.1:8000/cours

**Éléments traduits**:
- Menu latéral: "Cours", "Gestion Quiz", "Événements"
- Navbar: "Rechercher"
- Sélecteur de langue (globe 🌐)

---

## 🌍 Langues disponibles

| Langue | Code | Drapeau | Exemple |
|--------|------|---------|---------|
| Français | fr | 🇫🇷 | Chapitre 7 |
| Anglais | en | 🇬🇧 | Chapter 7 |
| Espagnol | es | 🇪🇸 | Capítulo 7 |
| Arabe | ar | 🇸🇦 | الفصل 7 |

---

## 🎯 Test rapide (2 minutes)

### Étape 1: Ouvrir la page de test
```
http://127.0.0.1:8000/test-traduction.html
```

### Étape 2: Cliquer sur "Liste des chapitres"

### Étape 3: Changer la langue
1. Cliquer sur le globe 🌐
2. Choisir "Español"
3. La page se recharge

### Étape 4: Vérifier les traductions
- [ ] "Découvrez nos chapitres" → "Descubre nuestros capítulos"
- [ ] "Chapitre X" → "Capítulo X"
- [ ] "Lire le chapitre" → "Leer capítulo"
- [ ] "Passer le quiz" → "Hacer el cuestionario"
- [ ] "Retour aux cours" → "Volver a los cursos"

### Étape 5: Tester une autre langue
1. Cliquer sur le globe 🌐
2. Choisir "English"
3. Vérifier: "Chapter", "Read chapter", "Take quiz"

---

## 🔍 Où trouver le sélecteur de langue?

### Frontoffice (pages publiques)
- **Position**: Navbar en haut à droite
- **Icône**: Globe 🌐
- **Type**: Dropdown avec drapeaux

### Backoffice (pages admin)
- **Position**: Navbar en haut à droite (à côté du bouton thème)
- **Icône**: Globe 🌐
- **Type**: Menu glassmorphism

---

## 📊 Tableau de traductions

| Français | English | Español | العربية |
|----------|---------|---------|---------|
| Chapitre | Chapter | Capítulo | الفصل |
| Lecture estimée | Estimated reading | Lectura estimada | وقت القراءة المقدر |
| Niveau débutant | Beginner level | Nivel principiante | مستوى مبتدئ |
| Ressources complémentaires | Additional resources | Recursos adicionales | موارد إضافية |
| Retour à la liste | Back to list | Volver a la lista | العودة إلى القائمة |
| Lire le chapitre | Read chapter | Leer capítulo | قراءة الفصل |
| Passer le quiz | Take quiz | Hacer el cuestionario | خذ الاختبار |
| Retour aux cours | Back to courses | Volver a los cursos | العودة إلى الدورات |

---

## 🐛 Dépannage

### Le sélecteur de langue n'apparaît pas
```bash
# Vider le cache
php bin/console cache:clear

# Vérifier que le serveur tourne
symfony server:status

# Redémarrer le serveur si nécessaire
symfony server:stop
symfony serve
```

### Les textes ne changent pas
1. Vérifier que vous avez cliqué sur le sélecteur de langue
2. Vérifier que la page s'est rechargée
3. Vider le cache du navigateur (Ctrl+Shift+Delete)
4. Tester en navigation privée

### Erreur 404
Vérifier que l'URL est correcte:
- ✅ http://127.0.0.1:8000/chapitre
- ❌ http://localhost:8000/chapitre (utiliser 127.0.0.1)

---

## ✅ Checklist finale

- [ ] Serveur démarré: http://127.0.0.1:8000
- [ ] Cache vidé: `php bin/console cache:clear`
- [ ] Page de test ouverte: http://127.0.0.1:8000/test-traduction.html
- [ ] Sélecteur de langue visible (globe 🌐)
- [ ] 4 langues disponibles (FR, EN, ES, AR)
- [ ] Traductions fonctionnent sur la page des chapitres
- [ ] Traductions fonctionnent sur le détail d'un chapitre
- [ ] La langue persiste lors de la navigation

---

## 🎉 Résultat attendu

Quand vous changez la langue en **Español**:
- ✅ Navigation en espagnol
- ✅ Titres en espagnol
- ✅ Boutons en espagnol
- ✅ Labels en espagnol
- ✅ Messages en espagnol

**AUCUN texte ne doit rester en français!**

---

## 📞 URLs de référence

- **Page de test**: http://127.0.0.1:8000/test-traduction.html
- **Liste chapitres**: http://127.0.0.1:8000/chapitre
- **Page d'accueil**: http://127.0.0.1:8000
- **Backoffice**: http://127.0.0.1:8000/cours

**Commencez par la page de test!** 🚀
