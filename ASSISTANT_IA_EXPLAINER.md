# 🤖 Assistant Pédagogique IA - Explainer de Chapitres

## Vue d'ensemble

Fonctionnalité d'intelligence artificielle permettant de générer automatiquement des explications personnalisées de chapitres de cours avec synthèse vocale intégrée.

## ✨ Fonctionnalités

### 1. Génération d'explications IA
- ✅ Résumé concis du chapitre (2-3 phrases)
- ✅ Explication détaillée et pédagogique
- ✅ 5 points clés à retenir
- ✅ Adaptation au niveau (débutant/avancé)

### 2. Synthèse vocale (Web Speech API)
- ✅ Lecture automatique de l'explication
- ✅ Contrôles Play/Pause/Stop
- ✅ Réglage de la vitesse de lecture (0.5x à 2x)
- ✅ Voix française naturelle

### 3. Interface utilisateur
- ✅ Design moderne et responsive
- ✅ Animations fluides
- ✅ Feedback visuel en temps réel
- ✅ Accessibilité optimisée

## 🏗️ Architecture

### Backend (Symfony)

#### Service: ChapterExplainerService
```php
src/Service/ChapterExplainerService.php
```

**Responsabilités:**
- Appel à l'API Groq avec le modèle Llama 4 Scout
- Construction du prompt selon le niveau
- Parsing de la réponse structurée
- Gestion des erreurs

**Modèle IA utilisé:**
```php
private const MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';
```

#### Contrôleur: ChapterExplainerController
```php
src/Controller/ChapterExplainerController.php
```

**Routes:**
- `GET /chapter-explainer/{id}` - Page d'explication
- `POST /chapter-explainer/api/explain/{id}` - API de génération

### Frontend

#### Template Twig
```
templates/frontoffice/chapter_explainer/index.html.twig
```

**Composants:**
- Sélecteur de niveau (débutant/avancé)
- Bouton de génération
- Zone de chargement animée
- Affichage des résultats (résumé, explication, points clés)
- Contrôles audio (play, pause, stop, vitesse)

#### JavaScript
- Appel AJAX à l'API
- Gestion de la synthèse vocale (Web Speech API)
- Contrôles audio interactifs
- Formatage du texte pour la lecture

## 🔧 Configuration

### 1. Clé API Groq

**Fichier:** `.env.local`
```env
###> groq/api ###
GROQ_API_KEY=gsk_votre_cle_ici
###< groq/api ###
```

**Obtenir une clé:**
1. Aller sur https://console.groq.com/keys
2. Créer un compte gratuit
3. Générer une nouvelle clé API
4. Copier dans `.env.local`

### 2. Configuration du service

**Fichier:** `config/services.yaml`
```yaml
App\Service\ChapterExplainerService:
    arguments:
        $groqApiKey: '%env(GROQ_API_KEY)%'
```

## 📖 Utilisation

### Pour les étudiants

1. **Accéder à un chapitre**
   - Aller sur la page d'un chapitre
   - Cliquer sur le bouton "🤖 Assistant IA"

2. **Choisir le niveau**
   - Débutant: Langage simple, exemples concrets
   - Avancé: Vocabulaire technique, explications approfondies

3. **Générer l'explication**
   - Cliquer sur "Générer l'explication"
   - Attendre 5-10 secondes

4. **Écouter l'explication**
   - Cliquer sur "Lire" pour la synthèse vocale
   - Ajuster la vitesse si nécessaire
   - Utiliser Pause/Stop pour contrôler la lecture

### Exemple de résultat

**Résumé:**
> Ce chapitre introduit les concepts fondamentaux de la programmation orientée objet en PHP. Il couvre les classes, les objets, l'encapsulation et l'héritage.

**Explication:**
> La programmation orientée objet (POO) est un paradigme qui organise le code autour d'objets plutôt que de fonctions. En PHP, une classe est un modèle qui définit les propriétés et méthodes d'un objet...

**Points clés:**
- Une classe est un modèle pour créer des objets
- L'encapsulation protège les données avec private/public
- L'héritage permet de réutiliser du code
- Les méthodes définissent le comportement des objets
- Le constructeur initialise les propriétés

## 🎨 Design

### Couleurs
- Primary: `#667eea` (Bleu violet)
- Secondary: `#764ba2` (Violet)
- Success: `#28a745` (Vert)
- Info: `#17a2b8` (Cyan)

### Icônes
- 🤖 Robot - Assistant IA
- 📄 File - Résumé
- 📖 Book - Explication
- 🔑 Key - Points clés
- 🔊 Speaker - Audio

## 🔊 Web Speech API

### Compatibilité navigateurs
- ✅ Chrome/Edge (Chromium)
- ✅ Safari
- ✅ Firefox (support partiel)
- ❌ IE11

### Fonctionnalités audio
```javascript
// Créer une utterance
utterance = new SpeechSynthesisUtterance(text);
utterance.lang = 'fr-FR';
utterance.rate = 1.0; // Vitesse

// Lire
speechSynthesis.speak(utterance);

// Pause
speechSynthesis.pause();

// Reprendre
speechSynthesis.resume();

// Arrêter
speechSynthesis.cancel();
```

## 🚀 Performance

### Temps de réponse
- Génération IA: 5-10 secondes
- Affichage: Instantané
- Synthèse vocale: Temps réel

### Optimisations
- Timeout API: 30 secondes
- Parsing robuste avec fallback
- Gestion d'erreurs complète
- Cache navigateur pour les assets

## 🐛 Gestion des erreurs

### Erreurs API
```php
try {
    $response = $this->httpClient->request(...);
} catch (\Exception $e) {
    return [
        'summary' => 'Erreur lors de la génération',
        'explanation' => 'Veuillez réessayer',
        'keyPoints' => ['Erreur de connexion']
    ];
}
```

### Erreurs frontend
```javascript
try {
    const response = await fetch(...);
    const data = await response.json();
} catch (error) {
    alert('Erreur de connexion à l\'API');
}
```

## 📊 Logs

Les logs sont enregistrés dans `var/log/dev.log`:

```
[info] Generating chapter explanation level=beginner content_length=1234
[info] Chapter explanation generated successfully
[error] Error generating chapter explanation error="Connection timeout"
```

## 🧪 Tests

### Test manuel

1. **Tester la génération**
```bash
# Accéder à un chapitre
http://localhost:8000/chapter-explainer/1
```

2. **Tester l'API directement**
```bash
curl -X POST http://localhost:8000/chapter-explainer/api/explain/1 \
  -d "level=beginner"
```

### Vérifier la configuration
```bash
# Vérifier que la clé API est chargée
php bin/console debug:container --env-vars | grep GROQ
```

## 📝 Prompt Engineering

### Structure du prompt
```
Analyse ce chapitre de cours et fournis une explication structurée.

CHAPITRE:
[Contenu du chapitre]

INSTRUCTIONS:
[Instructions selon le niveau]

Réponds au format suivant:
RÉSUMÉ:
[Résumé]

EXPLICATION:
[Explication]

POINTS CLÉS:
- [Point 1]
- [Point 2]
...
```

### Adaptation au niveau

**Débutant:**
> Utilise un langage simple et des exemples concrets pour les débutants.

**Avancé:**
> Utilise un vocabulaire technique et des explications approfondies.

## 🎯 Cas d'usage

### 1. Révision rapide
L'étudiant peut obtenir un résumé rapide avant un examen.

### 2. Apprentissage adapté
Choisir le niveau selon sa compréhension actuelle.

### 3. Accessibilité
Les étudiants malvoyants peuvent écouter le contenu.

### 4. Apprentissage mobile
Écouter les explications en déplacement.

## 🔐 Sécurité

### Protection de la clé API
- ✅ Stockée dans `.env.local` (non versionné)
- ✅ Jamais exposée au frontend
- ✅ Utilisée uniquement côté serveur

### Validation des entrées
```php
if (!in_array($level, ['beginner', 'advanced'])) {
    return $this->json(['error' => 'Niveau invalide'], 400);
}
```

## 📈 Améliorations futures

### Court terme
- [ ] Cache des explications générées
- [ ] Historique des explications
- [ ] Export PDF de l'explication
- [ ] Partage de l'explication

### Long terme
- [ ] Choix de la voix (masculine/féminine)
- [ ] Support multilingue
- [ ] Questions-réponses interactives
- [ ] Génération de quiz basée sur l'explication

## 🎓 Démonstration

### Scénario de démo (1 journée)

**Matin (2h):**
1. Montrer la page d'un chapitre
2. Cliquer sur "Assistant IA"
3. Sélectionner "Débutant"
4. Générer l'explication
5. Montrer le résumé, l'explication, les points clés

**Après-midi (2h):**
1. Tester avec "Avancé"
2. Démontrer la synthèse vocale
3. Ajuster la vitesse de lecture
4. Montrer la responsivité mobile
5. Tester avec différents chapitres

## 📞 Support

### Problèmes courants

**L'IA ne génère rien:**
- Vérifier la clé API dans `.env.local`
- Vérifier la connexion internet
- Consulter les logs: `var/log/dev.log`

**La synthèse vocale ne fonctionne pas:**
- Vérifier le navigateur (Chrome recommandé)
- Vérifier les permissions audio
- Tester avec un autre navigateur

**Erreur 404:**
- Vider le cache: `php bin/console cache:clear`
- Vérifier les routes: `php bin/console debug:router`

## ✅ Checklist de déploiement

- [ ] Clé API Groq configurée dans `.env.local`
- [ ] Service configuré dans `services.yaml`
- [ ] Cache Symfony vidé
- [ ] Test sur un chapitre réel
- [ ] Test de la synthèse vocale
- [ ] Test sur mobile
- [ ] Vérification des logs
- [ ] Documentation à jour

## 🎉 Résultat final

Un assistant pédagogique IA complet, fonctionnel et démontrable en une journée, offrant:
- Explications personnalisées générées par IA
- Synthèse vocale pour l'accessibilité
- Interface moderne et intuitive
- Expérience utilisateur fluide

**Temps de développement:** 1 journée
**Temps de démo:** 30 minutes
**Impact pédagogique:** Élevé
