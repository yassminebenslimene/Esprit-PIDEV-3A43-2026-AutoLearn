# 🧪 Comment Tester la Traduction

## ✅ Le serveur est déjà démarré!

URL: **http://127.0.0.1:8000**

---

## 📍 Où tester la traduction?

### Option 1: Page des chapitres (RECOMMANDÉ)

**URL**: `http://127.0.0.1:8000/chapitre`

**Ce qui sera traduit**:
- ✅ "Découvrez nos chapitres" → "Discover our chapters" (EN) / "Descubre nuestros capítulos" (ES)
- ✅ "Chapitre X" → "Chapter X" (EN) / "Capítulo X" (ES)
- ✅ "Lire le chapitre" → "Read chapter" (EN) / "Leer capítulo" (ES)
- ✅ "Passer le quiz" → "Take quiz" (EN) / "Hacer el cuestionario" (ES)
- ✅ "Retour aux cours" → "Back to courses" (EN) / "Volver a los cursos" (ES)

### Option 2: Détail d'un chapitre

**URL**: `http://127.0.0.1:8000/chapitre/{id}` (remplacer {id} par l'ID d'un chapitre)

**Ce qui sera traduit**:
- ✅ "Chapitre 7" → "Chapter 7" (EN) / "Capítulo 7" (ES)
- ✅ "Lecture estimée : 5 min" → "Estimated reading : 5 min" (EN) / "Lectura estimada : 5 min" (ES)
- ✅ "Niveau débutant" → "Beginner level" (EN) / "Nivel principiante" (ES)
- ✅ "Ressources complémentaires" → "Additional resources" (EN) / "Recursos adicionales" (ES)
- ✅ "Retour à la liste des chapitres" → "Back to chapter list" (EN) / "Volver a la lista de capítulos" (ES)

### Option 3: Page d'accueil

**URL**: `http://127.0.0.1:8000`

**Ce qui sera traduit**:
- ✅ Navigation: "Home", "Events", "Courses", "Community"
- ✅ Sélecteur de langue dans la navbar

---

## 🎯 Étapes de test détaillées

### Test 1: Vérifier le sélecteur de langue

1. Ouvrir votre navigateur
2. Aller sur: `http://127.0.0.1:8000`
3. Chercher l'icône **globe 🌐** dans la navbar (en haut à droite)
4. Cliquer dessus
5. Vous devriez voir:
   - 🇫🇷 Français
   - 🇬🇧 English
   - 🇪🇸 Español
   - 🇸🇦 العربية

### Test 2: Changer la langue en espagnol

1. Cliquer sur **🇪🇸 Español**
2. La page se recharge
3. Vérifier que la navigation change:
   - "Home" → "Inicio"
   - "Events" → "Eventos"
   - "Courses" → "Cursos"
   - "Community" → "Comunidad"

### Test 3: Tester sur la page des chapitres

1. Aller sur: `http://127.0.0.1:8000/chapitre`
2. Cliquer sur le globe 🌐
3. Choisir **Español**
4. Vérifier que TOUT change:
   - "Découvrez nos chapitres" → "Descubre nuestros capítulos"
   - "Chapitre X" → "Capítulo X"
   - "Lire le chapitre" → "Leer capítulo"
   - "Passer le quiz" → "Hacer el cuestionario"
   - "Retour aux cours" → "Volver a los cursos"

### Test 4: Tester sur le détail d'un chapitre

1. Sur la page des chapitres, cliquer sur "Leer capítulo" (si en espagnol)
2. Vérifier les traductions:
   - "Chapitre 7" → "Capítulo 7"
   - "Lecture estimée : 5 min" → "Lectura estimada : 5 min"
   - "Niveau débutant" → "Nivel principiante"
   - "Ressources complémentaires" → "Recursos adicionales"
   - "Retour à la liste des chapitres" → "Volver a la lista de capítulos"

### Test 5: Tester toutes les langues

1. Changer en **English** (🇬🇧)
   - Vérifier: "Chapter", "Read chapter", "Take quiz"
2. Changer en **العربية** (🇸🇦)
   - Vérifier: "الفصل", "قراءة الفصل", "خذ الاختبار"
3. Revenir en **Français** (🇫🇷)
   - Vérifier: "Chapitre", "Lire le chapitre", "Passer le quiz"

---

## 🐛 Si ça ne marche pas

### Problème 1: Le sélecteur de langue n'apparaît pas

**Solution**:
```bash
# Vider le cache
php bin/console cache:clear

# Redémarrer le serveur
symfony server:stop
symfony serve
```

### Problème 2: Les textes ne changent pas

**Vérifier**:
1. Que vous êtes bien sur `http://127.0.0.1:8000`
2. Que le cache a été vidé
3. Que vous avez cliqué sur le sélecteur de langue
4. Que la page s'est rechargée après le changement

**Solution**:
```bash
# Vider complètement le cache
php bin/console cache:clear --no-warmup
rm -rf var/cache/*  # Sur Windows: rmdir /s /q var\cache

# Redémarrer le navigateur
# Vider le cache du navigateur (Ctrl+Shift+Delete)
```

### Problème 3: Erreur 404 ou 500

**Solution**:
```bash
# Vérifier les logs
tail -f var/log/dev.log

# Vérifier que toutes les routes existent
php bin/console debug:router | grep chapitre
```

---

## 📸 Captures d'écran attendues

### Avant (Français)
```
Chapitre 7
Lecture estimée : 5 min
Niveau débutant
Retour à la liste des chapitres
```

### Après (Espagnol)
```
Capítulo 7
Lectura estimada : 5 min
Nivel principiante
Volver a la lista de capítulos
```

### Après (Anglais)
```
Chapter 7
Estimated reading : 5 min
Beginner level
Back to chapter list
```

### Après (Arabe)
```
الفصل 7
وقت القراءة المقدر : 5 دقيقة
مستوى مبتدئ
العودة إلى قائمة الفصول
```

---

## ✅ Checklist de test

- [ ] Le serveur Symfony est démarré (`http://127.0.0.1:8000`)
- [ ] Le cache a été vidé (`php bin/console cache:clear`)
- [ ] Le sélecteur de langue apparaît dans la navbar (icône globe 🌐)
- [ ] Les 4 langues sont disponibles (FR, EN, ES, AR)
- [ ] Changer la langue recharge la page
- [ ] Les textes de la navbar changent
- [ ] Les textes de la page des chapitres changent
- [ ] Les textes du détail d'un chapitre changent
- [ ] La langue persiste lors de la navigation

---

## 🎉 Résultat attendu

Quand vous changez la langue en **Español**, TOUS les textes doivent être en espagnol:
- Navigation
- Titres
- Boutons
- Labels
- Messages

**Aucun texte ne doit rester en français!** ✅

---

## 📞 Besoin d'aide?

Si les traductions ne fonctionnent toujours pas:

1. Vérifier que le fichier `translations/messages.es.yaml` existe
2. Vérifier que `config/packages/translation.yaml` contient `'es'` dans `enabled_locales`
3. Vérifier que `src/Controller/LanguageController.php` contient `'es'` dans `$allowedLocales`
4. Vider complètement le cache: `php bin/console cache:clear --no-warmup`
5. Redémarrer le serveur: `symfony server:stop` puis `symfony serve`
6. Vider le cache du navigateur (Ctrl+Shift+Delete)
7. Tester en navigation privée

---

## 🚀 URLs de test rapide

- **Page d'accueil**: http://127.0.0.1:8000
- **Liste des chapitres**: http://127.0.0.1:8000/chapitre
- **Liste des cours**: http://127.0.0.1:8000/cours
- **Backoffice**: http://127.0.0.1:8000/cours (avec traduction aussi!)

Testez maintenant! 🎯
