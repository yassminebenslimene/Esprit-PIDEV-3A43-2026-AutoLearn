# Guide d'Intégration des APIs de Traduction

## 🎯 Situation Actuelle

L'API de traduction fonctionne actuellement en **MODE DEMO** avec une traduction simulée qui ajoute un préfixe `[EN]`, `[ES]`, etc. devant le texte.

## 🔧 Options d'API de Traduction Réelles

### Option 1: Google Cloud Translation API (RECOMMANDÉ)

**Avantages:**
- ✅ Très bonne qualité de traduction
- ✅ Support de 100+ langues
- ✅ Fiable et rapide
- ✅ 500,000 caractères gratuits par mois

**Installation:**

```bash
composer require google/cloud-translate
```

**Configuration dans `.env`:**

```env
GOOGLE_TRANSLATE_API_KEY=votre_cle_api_ici
```

**Code à ajouter dans `TranslationService.php`:**

```php
use Google\Cloud\Translate\V2\TranslateClient;

private function translateWithGoogle(string $text, string $targetLang): ?string
{
    try {
        $translate = new TranslateClient([
            'key' => $_ENV['GOOGLE_TRANSLATE_API_KEY']
        ]);

        $result = $translate->translate($text, [
            'target' => $targetLang,
            'source' => 'fr'
        ]);

        return $result['text'];
    } catch (\Exception $e) {
        $this->logger->error('Google Translate error', ['message' => $e->getMessage()]);
        return null;
    }
}
```

**Obtenir une clé API:**
1. Aller sur https://console.cloud.google.com/
2. Créer un projet
3. Activer "Cloud Translation API"
4. Créer une clé API dans "Credentials"

---

### Option 2: DeepL API (Meilleure qualité)

**Avantages:**
- ✅ Meilleure qualité de traduction
- ✅ 500,000 caractères gratuits par mois
- ✅ Spécialisé dans les langues européennes

**Installation:**

```bash
composer require deeplcom/deepl-php
```

**Configuration dans `.env`:**

```env
DEEPL_API_KEY=votre_cle_api_ici
```

**Code à ajouter dans `TranslationService.php`:**

```php
use DeepL\Translator;

private function translateWithDeepL(string $text, string $targetLang): ?string
{
    try {
        $translator = new Translator($_ENV['DEEPL_API_KEY']);
        
        $result = $translator->translateText(
            $text,
            'fr',
            strtoupper($targetLang)
        );

        return $result->text;
    } catch (\Exception $e) {
        $this->logger->error('DeepL error', ['message' => $e->getMessage()]);
        return null;
    }
}
```

**Obtenir une clé API:**
1. Aller sur https://www.deepl.com/pro-api
2. S'inscrire pour le plan gratuit
3. Copier la clé API

---

### Option 3: MyMemory Translation API (Gratuit, sans clé)

**Avantages:**
- ✅ Complètement gratuit
- ✅ Pas besoin de clé API
- ✅ Facile à intégrer

**Inconvénients:**
- ❌ Qualité moyenne
- ❌ Limite de 1000 requêtes par jour

**Code à ajouter dans `TranslationService.php`:**

```php
private function translateWithMyMemory(string $text, string $sourceLang, string $targetLang): ?string
{
    try {
        $url = sprintf(
            'https://api.mymemory.translated.net/get?q=%s&langpair=%s|%s',
            urlencode($text),
            $sourceLang,
            $targetLang
        );

        $response = $this->httpClient->request('GET', $url, [
            'timeout' => self::TIMEOUT
        ]);

        $data = $response->toArray();
        
        if (isset($data['responseData']['translatedText'])) {
            return $data['responseData']['translatedText'];
        }

        return null;
    } catch (\Exception $e) {
        $this->logger->error('MyMemory error', ['message' => $e->getMessage()]);
        return null;
    }
}
```

**Utilisation:**

Remplacez dans la méthode `translate()`:

```php
return $this->translateWithMyMemory($text, $sourceLang, $targetLang);
```

---

### Option 4: LibreTranslate (Auto-hébergé)

**Avantages:**
- ✅ Open source
- ✅ Gratuit
- ✅ Pas de limite
- ✅ Confidentialité totale

**Installation:**

```bash
# Avec Docker
docker run -ti --rm -p 5000:5000 libretranslate/libretranslate
```

**Configuration dans `.env`:**

```env
LIBRETRANSLATE_URL=http://localhost:5000/translate
```

**Code déjà présent dans `TranslationService.php`** (décommentez-le)

---

## 🚀 Activation d'une API Réelle

### Étape 1: Choisir une API

Je recommande **MyMemory** pour commencer (gratuit, sans clé) ou **Google Translate** pour la production.

### Étape 2: Modifier `TranslationService.php`

Remplacez la ligne:

```php
return $this->translateDemo($text, $targetLang);
```

Par:

```php
return $this->translateWithMyMemory($text, $sourceLang, $targetLang);
```

### Étape 3: Tester

```bash
php bin/console cache:clear
```

Puis testez dans le navigateur!

---

## 📊 Comparaison des APIs

| API | Gratuit | Qualité | Limite | Clé requise |
|-----|---------|---------|--------|-------------|
| Google Translate | 500k chars/mois | ⭐⭐⭐⭐⭐ | Oui | Oui |
| DeepL | 500k chars/mois | ⭐⭐⭐⭐⭐ | Oui | Oui |
| MyMemory | Illimité | ⭐⭐⭐ | 1000 req/jour | Non |
| LibreTranslate | Illimité | ⭐⭐⭐⭐ | Non | Non (auto-hébergé) |

---

## 🔐 Sécurité

**Important:** Ne jamais exposer les clés API dans le code!

Toujours utiliser `.env`:

```env
# .env
GOOGLE_TRANSLATE_API_KEY=votre_cle_secrete
DEEPL_API_KEY=votre_cle_secrete
```

Et ajouter dans `.env.example`:

```env
# .env.example
GOOGLE_TRANSLATE_API_KEY=
DEEPL_API_KEY=
```

---

## 🧪 Mode Demo Actuel

Le mode demo actuel est parfait pour:
- ✅ Tester l'architecture
- ✅ Développer l'interface
- ✅ Vérifier le cache
- ✅ Présenter le concept

Il ajoute simplement un préfixe `[EN]`, `[ES]`, etc. devant le texte original.

---

## 💡 Recommandation

Pour votre projet, je recommande:

1. **Développement/Tests:** Garder le mode demo actuel
2. **Production:** Utiliser Google Translate API ou DeepL

Le mode demo permet de développer et tester sans dépendre d'une API externe!
