# Guide de Test des Validations Serveur

## Comment tester que les validations fonctionnent

### 1. Test Quiz - Titre invalide

**Test 1: Titre trop court**
1. Allez sur `/quiz/new`
2. Entrez un titre avec seulement 2 caractères: "AB"
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le titre doit contenir au moins 3 caractères."

**Test 2: Titre avec caractères invalides**
1. Allez sur `/quiz/new`
2. Entrez un titre avec des symboles: "Quiz @#$%"
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le titre contient des caractères non autorisés."

**Test 3: Titre vide**
1. Allez sur `/quiz/new`
2. Laissez le titre vide
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le titre du quiz est obligatoire."

---

### 2. Test Quiz - Description invalide

**Test 1: Description trop courte**
1. Allez sur `/quiz/new`
2. Entrez une description avec seulement 5 caractères: "Court"
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "La description doit contenir au moins 10 caractères."

**Test 2: Description vide**
1. Allez sur `/quiz/new`
2. Laissez la description vide
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "La description est obligatoire."

---

### 3. Test Quiz - État invalide

**Test: État non sélectionné**
1. Allez sur `/quiz/new`
2. Remplissez titre et description correctement
3. Ne sélectionnez pas d'état (laissez "Sélectionnez un état")
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "L'état du quiz est obligatoire."

---

### 4. Test Question - Texte invalide

**Test 1: Question trop courte**
1. Allez sur `/question/new`
2. Entrez une question avec seulement 5 caractères: "Quoi?"
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "La question doit contenir au moins 10 caractères."

**Test 2: Question vide**
1. Allez sur `/question/new`
2. Laissez le texte de la question vide
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le texte de la question est obligatoire."

---

### 5. Test Question - Points invalides

**Test 1: Points = 0**
1. Allez sur `/question/new`
2. Entrez 0 dans le champ points
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le nombre de points doit être entre 1 et 100."

**Test 2: Points négatifs**
1. Allez sur `/question/new`
2. Entrez -5 dans le champ points
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le nombre de points doit être positif."

**Test 3: Points > 100**
1. Allez sur `/question/new`
2. Entrez 150 dans le champ points
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le nombre de points doit être entre 1 et 100."

**Test 4: Points vide**
1. Allez sur `/question/new`
2. Laissez le champ points vide
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le nombre de points est obligatoire."

---

### 6. Test Question - Quiz non sélectionné

**Test: Sans quiz associé**
1. Allez sur `/question/new`
2. Remplissez texte et points correctement
3. Ne sélectionnez pas de quiz (laissez "Sélectionnez un quiz")
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "La question doit être associée à un quiz."

---

### 7. Test Option - Texte invalide

**Test 1: Texte vide**
1. Allez sur `/option/new`
2. Laissez le texte de l'option vide
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "Le texte de l'option est obligatoire."

**Test 2: Texte trop long (> 255 caractères)**
1. Allez sur `/option/new`
2. Entrez un texte de plus de 255 caractères
3. Remplissez les autres champs correctement
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "L'option ne peut pas dépasser 255 caractères."

---

### 8. Test Option - Question non sélectionnée

**Test: Sans question associée**
1. Allez sur `/option/new`
2. Remplissez le texte correctement
3. Ne sélectionnez pas de question (laissez "Sélectionnez une question")
4. Cliquez sur "Enregistrer"
5. ✅ **Résultat attendu**: Message d'erreur "L'option doit être associée à une question."

---

## Test de Validation Multiple

**Test: Formulaire complètement vide**
1. Allez sur `/quiz/new`
2. Ne remplissez aucun champ
3. Cliquez sur "Enregistrer"
4. ✅ **Résultat attendu**: Plusieurs messages d'erreur affichés:
   - "Le titre du quiz est obligatoire."
   - "La description est obligatoire."
   - "L'état du quiz est obligatoire."

---

## Vérification que les validations sont côté serveur

### Méthode 1: Désactiver JavaScript
1. Ouvrez les outils de développement (F12)
2. Désactivez JavaScript dans les paramètres
3. Essayez de soumettre un formulaire invalide
4. ✅ **Résultat attendu**: Les validations fonctionnent toujours

### Méthode 2: Modifier le HTML
1. Ouvrez les outils de développement (F12)
2. Inspectez un champ input
3. Supprimez les attributs `maxlength`, `min`, `max`, `required`
4. Essayez de soumettre des données invalides
5. ✅ **Résultat attendu**: Les validations serveur bloquent toujours

### Méthode 3: Utiliser curl ou Postman
```bash
# Test avec curl - Titre trop court
curl -X POST http://localhost:8000/quiz/new \
  -d "quiz[titre]=AB" \
  -d "quiz[description]=Description valide de test" \
  -d "quiz[etat]=actif"
```
✅ **Résultat attendu**: La validation serveur rejette la requête

---

## Checklist de Validation

### Quiz
- [ ] Titre vide → Erreur
- [ ] Titre < 3 caractères → Erreur
- [ ] Titre > 255 caractères → Erreur
- [ ] Titre avec caractères spéciaux → Erreur
- [ ] Description vide → Erreur
- [ ] Description < 10 caractères → Erreur
- [ ] Description > 2000 caractères → Erreur
- [ ] État non sélectionné → Erreur
- [ ] État invalide (autre que actif/inactif/brouillon/archive) → Erreur

### Question
- [ ] Texte vide → Erreur
- [ ] Texte < 10 caractères → Erreur
- [ ] Texte > 1000 caractères → Erreur
- [ ] Points vide → Erreur
- [ ] Points = 0 → Erreur
- [ ] Points négatif → Erreur
- [ ] Points > 100 → Erreur
- [ ] Points non entier (ex: 5.5) → Erreur
- [ ] Quiz non sélectionné → Erreur

### Option
- [ ] Texte vide → Erreur
- [ ] Texte > 255 caractères → Erreur
- [ ] Question non sélectionnée → Erreur

---

## Résultat Attendu

✅ **Toutes les validations doivent fonctionner même si:**
- JavaScript est désactivé
- Les attributs HTML sont modifiés
- La requête est envoyée directement via curl/Postman
- Le formulaire est soumis via un script automatisé

✅ **Les messages d'erreur doivent:**
- Être affichés en français
- Être clairs et explicites
- Apparaître à côté du champ concerné
- Être visibles avec le style glassmorphism
