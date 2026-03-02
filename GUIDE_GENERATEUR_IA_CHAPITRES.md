# 🤖 Guide du Générateur IA de Chapitres

## Vue d'ensemble

Le générateur IA de chapitres permet de créer automatiquement des chapitres de cours complets avec du contenu pédagogique détaillé, basé sur un titre de chapitre que vous spécifiez.

## Comment utiliser

### 1. Accéder au générateur

1. Allez dans **Backoffice** → **Cours**
2. Cliquez sur **"Chapitres"** pour un cours
3. Cliquez sur le bouton **"🤖 Générer un Chapitre avec l'IA"**

### 2. Saisir le titre du chapitre

Une fenêtre modale s'ouvre où vous pouvez:
- Entrer le titre spécifique du chapitre que vous voulez générer
- Exemple: "Introduction aux bases de données relationnelles"
- Exemple: "Les types de données en SQL"
- Exemple: "Normalisation des bases de données"

### 3. Génération automatique

L'IA va générer automatiquement:
- **Titre**: Le titre que vous avez saisi
- **Contenu**: Un contenu pédagogique détaillé (minimum 500 caractères) adapté au titre et au contexte du cours
- **Ressources**: Des ressources et références utiles pour approfondir le sujet

### 4. Résultat

Le chapitre est automatiquement:
- Créé dans la base de données
- Ajouté à la liste des chapitres du cours
- Numéroté dans l'ordre (ordre automatique)
- Prêt à être modifié si nécessaire

## Avantages

✅ **Contenu personnalisé**: Chaque chapitre est généré spécifiquement selon le titre que vous donnez

✅ **Gain de temps**: Plus besoin d'écrire manuellement tout le contenu

✅ **Qualité pédagogique**: L'IA structure le contenu de manière claire et éducative

✅ **Contexte intelligent**: L'IA prend en compte le cours, la matière et le niveau

## Architecture technique

### Fichiers impliqués

1. **Template**: `templates/backoffice/cours/chapitres.html.twig`
   - Modal de saisie du titre
   - Bouton de génération
   - JavaScript pour l'interaction

2. **Contrôleur**: `src/Controller/AIGeneratorController.php`
   - Route: `/backoffice/ai-generator/generate-chapter/{coursId}`
   - Méthode: POST
   - Reçoit le titre du chapitre
   - Crée le chapitre en base de données

3. **Service**: `src/Service/CourseGeneratorService.php`
   - Appelle l'API Groq (modèle: llama-3.3-70b-versatile)
   - Construit le prompt avec le titre spécifique
   - Parse la réponse JSON

4. **Configuration**: `config/services.yaml`
   - Configuration du service avec la clé API Groq

### Flux de données

```
1. Utilisateur clique sur "Générer un Chapitre avec l'IA"
   ↓
2. Modal s'ouvre pour saisir le titre du chapitre
   ↓
3. Utilisateur entre le titre (ex: "Les jointures SQL")
   ↓
4. Requête POST vers /backoffice/ai-generator/generate-chapter/{coursId}
   avec { chapterTitle: "Les jointures SQL" }
   ↓
5. CourseGeneratorService appelle l'API Groq
   avec un prompt incluant:
   - Titre du cours
   - Matière
   - Niveau
   - Titre du chapitre demandé
   ↓
6. L'IA génère un JSON avec titre, contenu, ressources
   ↓
7. Le contrôleur crée un objet Chapitre et le sauvegarde
   ↓
8. Réponse JSON success: true
   ↓
9. La page se recharge et affiche le nouveau chapitre
```


### Exemple de prompt envoyé à l'IA

```
Génère un chapitre de cours pour:
Cours: base de donne - base de donne (debutant)
Matière: Informatique
Niveau: Débutant

Titre du chapitre demandé: "Les jointures SQL"

Réponds UNIQUEMENT avec un objet JSON valide avec cette structure:
{
    "titre": "Les jointures SQL",
    "contenu": "Contenu détaillé...",
    "ressources": "Ressources..."
}

Le contenu doit être:
- Spécifiquement adapté au titre "Les jointures SQL"
- Pédagogique et bien structuré
- Adapté au niveau Débutant
- Complet avec des explications claires et des exemples concrets
- En français
- Minimum 500 caractères
```

## Configuration requise

### Variables d'environnement

Dans `.env.local`:
```env
GROQ_API_KEY=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
```

### Dépendances

- `symfony/http-client`: Pour les appels API
- `psr/log`: Pour le logging des erreurs

## Dépannage

### Erreur "Réponse invalide de l'API Groq"
- Vérifier que la clé API est correcte dans `.env.local`
- Vérifier la connexion internet

### Erreur "Erreur de parsing JSON"
- L'IA a peut-être retourné un format invalide
- Vérifier les logs dans `var/log/dev.log`

### Le contenu généré n'est pas pertinent
- Vérifier que le titre du chapitre est clair et précis
- Essayer de reformuler le titre
- Exemple: Au lieu de "Intro", utiliser "Introduction aux concepts de base"

## Améliorations futures possibles

- Permettre de régénérer un chapitre existant
- Ajouter des options de personnalisation (longueur, style, etc.)
- Générer plusieurs chapitres d'un coup à partir d'un plan
- Intégrer des exemples de code automatiquement
- Ajouter la génération de quiz associés au chapitre
