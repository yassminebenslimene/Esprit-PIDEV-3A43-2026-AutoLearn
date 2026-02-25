# 🌍 Système de Traduction Automatique des Chapitres

## 📋 Vue d'ensemble

Le système de traduction automatique permet aux étudiants de traduire instantanément le contenu des chapitres dans 8 langues différentes en utilisant l'API Groq AI avec le modèle Llama.

## ✨ Fonctionnalités

### Langues supportées
- 🇫🇷 Français (langue originale)
- 🇬🇧 English
- 🇪🇸 Español
- 🇸🇦 العربية (Arabe)
- 🇩🇪 Deutsch
- 🇮🇹 Italiano
- 🇵🇹 Português
- 🇨🇳 中文 (Chinois)

### Caractéristiques techniques
- ✅ Traduction en temps réel via API Groq
- ✅ Cache intelligent (7 jours) pour éviter les appels répétés
- ✅ Préservation du formatage HTML
- ✅ Interface utilisateur intuitive avec sélecteur de langue
- ✅ Indicateur de chargement pendant la traduction
- ✅ Gestion d'erreurs robuste
- ✅ Retour automatique au contenu original en cas d'erreur

## 🏗️ Architecture

### 1. Service de traduction (`TranslationService`)

**Fichier**: `src/Service/TranslationService.php`

**Responsabilités**:
- Appel à l'API Groq pour la traduction
- Gestion du cache (7 jours)
- Construction des prompts de traduction
- Validation des langues supportées

**Méthodes principales**:
```php
// Traduire un texte simple
translate(string $text, string $targetLang, ?string $sourceLang = null): string

// Traduire un chapitre complet (titre + contenu)
translateChapter($chapitre, string $targetLang): array

// Obtenir les langues supportées
getSupportedLanguages(): array

// Vérifier si une langue est supportée
isLanguageSupported(string $langCode): bool
```

### 2. Contrôleur API (`TranslationController`)

**Fichier**: `src/Controller/TranslationController.php`

**Routes**:
- `GET /api/chapitres/{id}/translate?lang=en` - Traduire un chapitre
- `GET /api/languages` - Liste des langues supportées

**Réponse JSON**:
```json
{
    "status": "success",
    "titre": "Introduction to Programming",
    "contenu": "Programming is the art of...",
    "cached": true
}
```

### 3. Interface utilisateur

**Fichier**: `templates/frontoffice/chapitre/show.html.twig`

**Composants**:
- Sélecteur de langue (dropdown)
- Script JavaScript pour appels AJAX
- Indicateurs de chargement
- Gestion d'erreurs côté client

## 🔧 Configuration

### 1. Clé API Groq

La clé API est déjà configurée dans `.env.local`:
```env
GROQ_API_KEY=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
```

### 2. Configuration du service

Dans `config/services.yaml`:
```yaml
App\Service\TranslationService:
    arguments:
        $groqApiKey: '%env(GROQ_API_KEY)%'
```

### 3. Modèle IA utilisé

```php
private const MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';
```

## 📖 Utilisation

### Pour l'étudiant

1. Ouvrir la page d'un chapitre
2. Utiliser le sélecteur de langue en haut à droite
3. Sélectionner la langue désirée
4. Le contenu se traduit automatiquement
5. Revenir au français pour voir le contenu original

### Pour le développeur

**Traduire un texte simple**:
```php
$translationService = $this->container->get(TranslationService::class);
$translated = $translationService->translate('Bonjour le monde', 'en');
// Résultat: "Hello world"
```

**Traduire un chapitre**:
```php
$chapitre = $chapitreRepository->find($id);
$translated = $translationService->translateChapter($chapitre, 'es');
// Résultat: ['titre' => '...', 'contenu' => '...']
```

**Appel API depuis JavaScript**:
```javascript
const response = await fetch(`/api/chapitres/${chapitreId}/translate?lang=en`);
const data = await response.json();
console.log(data.titre, data.contenu);
```

## 🎯 Flux de traduction

```
┌─────────────────┐
│  Utilisateur    │
│  sélectionne    │
│  une langue     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  JavaScript     │
│  envoie requête │
│  AJAX           │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Controller      │
│ vérifie cache   │
└────────┬────────┘
         │
    ┌────┴────┐
    │ Cache?  │
    └────┬────┘
         │
    ┌────┴────────┐
    │             │
   OUI           NON
    │             │
    │             ▼
    │    ┌─────────────────┐
    │    │ TranslationSvc  │
    │    │ appelle Groq    │
    │    └────────┬────────┘
    │             │
    │             ▼
    │    ┌─────────────────┐
    │    │  Groq API       │
    │    │  traduit texte  │
    │    └────────┬────────┘
    │             │
    │             ▼
    │    ┌─────────────────┐
    │    │ Mise en cache   │
    │    │ (7 jours)       │
    │    └────────┬────────┘
    │             │
    └─────────────┘
         │
         ▼
┌─────────────────┐
│  Réponse JSON   │
│  vers frontend  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Affichage du   │
│  contenu traduit│
└─────────────────┘
```

## 🚀 Performance

### Système de cache
- **Durée**: 7 jours
- **Clé**: Hash MD5 du texte + langue cible + langue source
- **Avantage**: Évite les appels API répétés pour le même contenu

### Temps de réponse
- **Première traduction**: 2-5 secondes (appel API)
- **Traductions suivantes**: < 100ms (depuis cache)

## 🔒 Sécurité

### Gestion des erreurs
- Validation des langues supportées
- Vérification de l'existence du chapitre
- Timeout de 30 secondes sur les appels API
- Retour au contenu original en cas d'erreur

### Logs
Tous les appels sont loggés avec:
- Langue cible
- Langue source
- Longueur du texte
- Succès/échec de la traduction

## 🧪 Tests

### Test manuel

1. **Test de traduction basique**:
```bash
# Ouvrir un chapitre
http://localhost:8000/frontoffice/chapitre/1

# Sélectionner "English"
# Vérifier que le contenu est traduit
```

2. **Test du cache**:
```bash
# Traduire une première fois (lent)
# Traduire à nouveau (rapide = cache)
```

3. **Test d'erreur**:
```bash
# Désactiver temporairement l'API
# Vérifier que le contenu original s'affiche
```

### Test API direct

```bash
# Traduire un chapitre en anglais
curl http://localhost:8000/api/chapitres/1?lang=en

# Liste des langues supportées
curl http://localhost:8000/api/languages
```

## 📊 Statistiques d'utilisation

Le système peut être étendu pour tracker:
- Nombre de traductions par langue
- Chapitres les plus traduits
- Temps de réponse moyen
- Taux de cache hit/miss

## 🔮 Améliorations futures

### Court terme
- [ ] Ajouter un bouton "Copier la traduction"
- [ ] Permettre de télécharger le PDF traduit
- [ ] Afficher un badge "Traduit par IA"

### Moyen terme
- [ ] Traduction des ressources additionnelles
- [ ] Traduction des quiz associés
- [ ] Mode comparaison (original + traduction côte à côte)

### Long terme
- [ ] Détection automatique de la langue préférée de l'utilisateur
- [ ] Historique des traductions consultées
- [ ] Suggestions de traductions basées sur le profil
- [ ] Support de langues additionnelles

## 📝 Notes importantes

1. **Coût API**: Chaque traduction consomme des tokens Groq. Le cache réduit considérablement les coûts.

2. **Qualité**: Le modèle Llama 4 Scout produit des traductions de haute qualité, mais peut parfois être trop littéral.

3. **HTML**: Le système préserve les balises HTML dans le contenu traduit.

4. **Limitations**: 
   - Maximum 4000 tokens par traduction
   - Timeout de 30 secondes
   - Nécessite une connexion internet

## 🆘 Dépannage

### Problème: "Erreur de traduction"
**Solution**: Vérifier que la clé API Groq est valide dans `.env.local`

### Problème: Traduction très lente
**Solution**: Vérifier la connexion internet et les logs Symfony

### Problème: Contenu non traduit
**Solution**: Vérifier que le code de langue est correct (fr, en, es, etc.)

## 📚 Ressources

- [Documentation Groq API](https://console.groq.com/docs)
- [Modèle Llama 4 Scout](https://groq.com/models/)
- [Symfony Cache Component](https://symfony.com/doc/current/components/cache.html)

---

**Créé le**: 2026-02-25  
**Auteur**: Système AutoLearn  
**Version**: 1.0.0
