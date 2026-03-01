# Nouvelles Fonctionnalités Challenge - Implémentées

## 📋 Résumé des modifications

Ce document décrit les nouvelles fonctionnalités ajoutées au système de challenges.

---

## ✅ 1. Modification de l'entité Challenge

### Changements:
- **Supprimé:** `date_debut` et `date_fin`
- **Ajouté:** `duree` (en minutes)

### Raison:
Au lieu de définir des dates de début et de fin, on définit maintenant la durée du challenge en minutes. C'est plus flexible et plus simple à gérer.

### Fichiers modifiés:
- `src/Entity/Challenge.php`
- `src/Form/ChallengeType.php`
- `templates/backoffice/challenge_form.html.twig`

### Migration:
```sql
ALTER TABLE challenge ADD COLUMN duree INT NOT NULL DEFAULT 30;
ALTER TABLE challenge DROP COLUMN date_debut;
ALTER TABLE challenge DROP COLUMN date_fin;
```

---

## ✅ 2. Liste déroulante pour le niveau

### Changements:
Le champ `niveau` est maintenant une liste déroulante avec 3 options:
- **Débutant**
- **Intermédiaire**
- **Avancé**

### Avant:
```php
->add('niveau', TextType::class)
```

### Après:
```php
->add('niveau', ChoiceType::class, [
    'choices' => [
        'Débutant' => 'Débutant',
        'Intermédiaire' => 'Intermédiaire',
        'Avancé' => 'Avancé'
    ]
])
```

### Fichiers modifiés:
- `src/Form/ChallengeType.php`

---

## ✅ 3. Génération d'exercices par IA (Groq)

### Fonctionnalité:
Un bouton "🤖 Générer avec IA" permet de créer automatiquement des exercices en utilisant l'API Groq.

### Comment ça marche:
1. Cliquer sur "Générer avec IA" dans la page des exercices
2. Saisir:
   - **Sujet:** Ex: "Les boucles en PHP"
   - **Niveau:** Débutant, Intermédiaire ou Avancé
   - **Nombre:** 1-10 exercices
3. L'IA génère automatiquement les exercices avec:
   - Question claire et précise
   - Réponse complète et correcte
   - Points adaptés au niveau

### Fichiers créés:
- `src/Service/ExerciceGeneratorAIService.php` - Service de génération IA
- Modal dans `templates/backoffice/exercice.html.twig`

### Endpoint ajouté:
- `POST /backoffice/exercice/generate-ai` - Génère les exercices via IA

### Exemple de prompt IA:
```
Génère exactement 5 exercices sur le sujet: "Les boucles en PHP"
Niveau: Débutant (questions simples, concepts de base)
Points: 5-10 points par exercice
```

### Réponse IA (JSON):
```json
{
    "exercices": [
        {
            "question": "Qu'est-ce qu'une boucle for en PHP?",
            "reponse": "Une boucle for permet de répéter un bloc de code un nombre défini de fois...",
            "points": 8
        }
    ]
}
```

---

## ✅ 4. Système de rating (évaluation)

### Fonctionnalité:
Après avoir terminé un challenge, l'utilisateur peut l'évaluer avec un système d'étoiles (1-5).

### Où:
- Page de complétion du challenge (`challenge_complete.html.twig`)
- Après l'affichage du score

### Interface:
```
⭐ Évaluez ce challenge
Votre avis nous aide à améliorer nos contenus

★ ★ ★ ★ ★  (cliquable)

✓ Merci pour votre vote!
```

### Fonctionnalités:
- Hover effect sur les étoiles
- Envoi AJAX du vote
- Message de confirmation
- Mise à jour automatique de la note moyenne du challenge

### Endpoint utilisé:
- `POST /challenge/vote` - Enregistre le vote (déjà existant)

### Fichiers modifiés:
- `templates/frontoffice/challenge_complete.html.twig`

---

## 📊 Récapitulatif technique

### Services créés:
1. **ExerciceGeneratorAIService** - Génération d'exercices par IA

### Endpoints ajoutés:
1. `POST /backoffice/exercice/generate-ai` - Génération IA

### Modifications de base de données:
1. Table `challenge`:
   - Ajout: `duree INT NOT NULL`
   - Suppression: `date_debut DATETIME`
   - Suppression: `date_fin DATETIME`

### Templates modifiés:
1. `templates/backoffice/challenge_form.html.twig` - Formulaire challenge
2. `templates/backoffice/exercice.html.twig` - Modal génération IA
3. `templates/frontoffice/challenge_complete.html.twig` - Système de rating

### Formulaires modifiés:
1. `src/Form/ChallengeType.php` - Liste déroulante niveau + durée

---

## 🚀 Comment utiliser

### 1. Créer un challenge:
1. Aller dans Backoffice > Challenges
2. Cliquer sur "Ajouter un challenge"
3. Remplir:
   - Titre
   - Description
   - **Durée (en minutes)** ← NOUVEAU
   - **Niveau (liste déroulante)** ← NOUVEAU
4. Sélectionner exercices et quiz
5. Enregistrer

### 2. Générer des exercices avec IA:
1. Aller dans Backoffice > Exercices
2. Cliquer sur "🤖 Générer avec IA"
3. Remplir:
   - Sujet: "Les fonctions en JavaScript"
   - Niveau: Intermédiaire
   - Nombre: 5
4. Cliquer sur "Générer"
5. Les exercices sont créés automatiquement!

### 3. Évaluer un challenge:
1. Compléter un challenge
2. Sur la page de résultats, cliquer sur les étoiles
3. Le vote est enregistré automatiquement

---

## 🔧 Configuration requise

### API Groq:
Assurez-vous que votre clé API Groq est configurée dans `.env`:
```env
GROQ_API_KEY=your_groq_api_key_here
GROQ_API_URL=https://api.groq.com/openai/v1/chat/completions
GROQ_MODEL=llama-3.3-70b-versatile
```

---

## 📝 Notes importantes

1. **Durée du challenge:** Exprimée en minutes (ex: 30 pour 30 minutes)
2. **Niveau:** Toujours utiliser la liste déroulante (Débutant, Intermédiaire, Avancé)
3. **Génération IA:** Nécessite une connexion internet et une clé API Groq valide
4. **Rating:** Chaque utilisateur peut voter une seule fois par challenge (mise à jour possible)

---

## ✨ Améliorations futures possibles

1. Afficher la durée restante pendant le challenge
2. Ajouter des statistiques sur les votes (graphiques)
3. Générer des quiz avec l'IA (pas seulement des exercices)
4. Filtrer les challenges par niveau dans le frontoffice
5. Exporter les exercices générés par IA en PDF

---

**Date de mise à jour:** 1er mars 2026
**Version:** 2.0
