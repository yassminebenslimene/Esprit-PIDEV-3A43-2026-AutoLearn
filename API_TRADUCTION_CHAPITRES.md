# API de Traduction Dynamique des Chapitres

## 📋 Vue d'ensemble

L'API de traduction permet aux étudiants de consulter le contenu des chapitres dans différentes langues (FR, EN, ES, DE, IT) sans dupliquer les données en base.

## 🎯 Fonctionnalités

✅ Traduction dynamique via LibreTranslate API  
✅ Mise en cache des traductions pour optimiser les performances  
✅ Support de 5 langues (FR, EN, ES, DE, IT)  
✅ Gestion des erreurs (timeout, API indisponible)  
✅ Interface utilisateur avec sélecteur de langue  

## 🔧 Architecture

### 1. Entités

**ChapitreTraduction** (`src/Entity/GestionDeCours/ChapitreTraduction.php`)
- Stocke les traductions en cache
- Relation ManyToOne avec Chapitre
- Index sur (chapitre_id, langue) pour optimiser les requêtes

### 2. Service de Traduction

**TranslationService** (`src/Service/TranslationService.php`)
- Appelle l'API LibreTranslate
- Timeout de 10 secondes
- Validation des langues supportées
- Gestion des erreurs avec logging

### 3. API Controller

**ChapitreApiController** (`src/Controller/Api/ChapitreApiController.php`)
- Route: `GET /api/chapitres/{id}?lang=en`
- Vérifie le cache avant d'appeler l'API externe
- Sauvegarde les nouvelles traductions en cache

## 📡 Utilisation de l'API

### Endpoint

```
GET /api/chapitres/{id}?lang={langue}
```

### Paramètres

- `id` (path) : ID du chapitre
- `lang` (query) : Code langue (fr, en, es, de, it)

### Exemples de requêtes

```bash
# Contenu en français (original)
GET /api/chapitres/5?lang=fr

# Contenu en anglais
GET /api/chapitres/5?lang=en

# Contenu en espagnol
GET /api/chapitres/5?lang=es
```

### Réponse succès (200)

```json
{
  "id": 5,
  "titre": "Introduction to Programming",
  "contenu": "Welcome to this chapter...",
  "ordre": 1,
  "langue": "en",
  "cached": true
}
```

### Réponse erreur (400)

```json
{
  "status": "error",
  "message": "Langue non supportée"
}
```

### Réponse erreur (503)

```json
{
  "status": "error",
  "message": "Service de traduction temporairement indisponible"
}
```

## 🎨 Interface Utilisateur

Le sélecteur de langue a été ajouté dans `templates/frontoffice/chapitre/show.html.twig`:

- Dropdown avec 5 langues disponibles
- Traduction en temps réel via JavaScript
- Indicateur de chargement pendant la traduction
- Gestion des erreurs avec message utilisateur
- Restauration du contenu original si erreur

## 🔄 Flux de Traduction

1. **Étudiant sélectionne une langue**
   ↓
2. **Frontend appelle `/api/chapitres/{id}?lang=en`**
   ↓
3. **Backend vérifie si langue = FR**
   - Si OUI → Retourne contenu original
   - Si NON → Continue
   ↓
4. **Backend vérifie le cache**
   - Si traduction existe → Retourne depuis cache
   - Si NON → Continue
   ↓
5. **Backend appelle LibreTranslate API**
   ↓
6. **Backend sauvegarde traduction en cache**
   ↓
7. **Backend retourne JSON traduit**

## 🚀 Optimisations

### Mise en cache
- Les traductions sont sauvegardées en base de données
- Index sur (chapitre_id, langue) pour requêtes rapides
- Évite les appels répétés à l'API externe

### Gestion des erreurs
- Timeout de 10 secondes pour éviter les blocages
- Logging des erreurs pour debugging
- Messages d'erreur clairs pour l'utilisateur
- Fallback sur contenu original en cas d'erreur

### Performance
- Vérification cache avant appel API
- Requêtes optimisées avec index
- Réponse immédiate pour langue française

## 🔐 Sécurité

✅ Appels API uniquement côté backend  
✅ Validation des langues supportées  
✅ Pas d'exposition de clés API côté frontend  
✅ Protection contre les injections SQL (Doctrine ORM)  

## 📊 Base de données

### Table: chapitre_traduction

```sql
CREATE TABLE chapitre_traduction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapitre_id INT NOT NULL,
    langue VARCHAR(5) NOT NULL,
    titre_traduit VARCHAR(500) NOT NULL,
    contenu_traduit TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_chapitre_langue (chapitre_id, langue),
    FOREIGN KEY (chapitre_id) REFERENCES chapitre(id) ON DELETE CASCADE
);
```

## 🧪 Tests

Pour tester l'API:

1. Démarrer le serveur: `symfony serve`
2. Accéder à un chapitre: `http://localhost:8000/chapitre/{id}`
3. Sélectionner une langue dans le dropdown
4. Vérifier la traduction affichée

### Test manuel de l'API

```bash
# Test avec curl
curl "http://localhost:8000/api/chapitres/1?lang=en"
```

## 🛠️ Maintenance

### Nettoyage des traductions obsolètes

Le repository inclut une méthode pour supprimer les traductions de plus de 30 jours:

```php
$repository->deleteOldTranslations();
```

Vous pouvez créer une commande Symfony pour automatiser ce nettoyage.

## 📝 Langues supportées

| Code | Langue    |
|------|-----------|
| fr   | Français  |
| en   | English   |
| es   | Español   |
| de   | Deutsch   |
| it   | Italiano  |

## ⚠️ Limitations

- LibreTranslate est un service gratuit avec des limites de requêtes
- La qualité de traduction peut varier selon le contenu
- Timeout de 10 secondes peut être insuffisant pour longs textes
- Pas de support pour langues RTL (arabe, hébreu)

## 🔮 Améliorations futures

- [ ] Support de plus de langues
- [ ] Rate limiting pour éviter abus
- [ ] Traduction des ressources (PDF, vidéos)
- [ ] Interface admin pour gérer les traductions
- [ ] Statistiques d'utilisation par langue
- [ ] Traduction automatique lors de la création de chapitre
