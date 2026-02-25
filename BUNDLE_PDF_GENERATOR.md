# 📦 Bundle PDF Generator - Documentation Complète

## ✅ Transformation Réussie !

La fonctionnalité PDF a été transformée en **Bundle Symfony** réutilisable et professionnel.

## 🏗️ Structure du Bundle

```
src/Bundle/PdfGeneratorBundle/
├── PdfGeneratorBundle.php                    # Classe principale du bundle
├── Service/
│   └── PdfService.php                        # Service de génération PDF
├── DependencyInjection/
│   └── PdfGeneratorExtension.php            # Extension pour la configuration
├── Resources/
│   └── config/
│       └── services.yaml                     # Configuration des services
├── Entity/
│   └── .gitkeep                             # Dossier requis par Doctrine
└── README.md                                 # Documentation du bundle
```

## 📋 Fichiers Créés

### 1. Bundle Principal
- ✅ `src/Bundle/PdfGeneratorBundle/PdfGeneratorBundle.php`

### 2. Service
- ✅ `src/Bundle/PdfGeneratorBundle/Service/PdfService.php`

### 3. Configuration
- ✅ `src/Bundle/PdfGeneratorBundle/DependencyInjection/PdfGeneratorExtension.php`
- ✅ `src/Bundle/PdfGeneratorBundle/Resources/config/services.yaml`

### 4. Documentation
- ✅ `src/Bundle/PdfGeneratorBundle/README.md`
- ✅ `BUNDLE_PDF_GENERATOR.md` (ce fichier)

### 5. Enregistrement
- ✅ Ajouté dans `config/bundles.php`

## 🔧 Modifications Effectuées

### config/bundles.php
```php
App\Bundle\PdfGeneratorBundle\PdfGeneratorBundle::class => ['all' => true],
```

### src/Controller/ChapitreController.php
```php
// Avant
use App\Service\PdfGeneratorService;
public function pdfPreview(Chapitre $chapitre, PdfGeneratorService $pdfGenerator)

// Après
use App\Bundle\PdfGeneratorBundle\Service\PdfService;
public function pdfPreview(Chapitre $chapitre, PdfService $pdfService)
```

## ✅ Ce qui est PRÉSERVÉ

### Frontoffice
- ✅ Aucun changement dans les templates frontoffice
- ✅ Boutons "Prévisualiser PDF" et "Télécharger PDF" fonctionnent
- ✅ Routes identiques (`/front/{id}/pdf` et `/front/{id}/pdf/download`)

### Configuration PDF
- ✅ Logo AutoLearn toujours présent
- ✅ Template `templates/pdf/chapitre.html.twig` inchangé
- ✅ Styles CSS préservés
- ✅ Mise en page identique

### Fonctionnalités
- ✅ Prévisualisation dans le navigateur
- ✅ Téléchargement
- ✅ Génération de certificats
- ✅ Génération de badges

## 🎯 Avantages du Bundle

### 1. Réutilisabilité
Le bundle peut être utilisé dans n'importe quel projet Symfony :
```php
// Dans n'importe quel contrôleur
public function myAction(PdfService $pdfService)
{
    $pdf = $pdfService->generateFromTemplate('pdf/template.html.twig', $data);
}
```

### 2. Encapsulation
Toute la logique PDF est isolée dans le bundle :
- Service dédié
- Configuration centralisée
- Dépendances gérées

### 3. Maintenabilité
- Code organisé et structuré
- Documentation intégrée
- Tests facilités

### 4. Extensibilité
Facile d'ajouter de nouvelles fonctionnalités :
```php
// Ajouter une méthode dans PdfService
public function generateInvoice(array $data): string
{
    return $this->generatePdfString('pdf/invoice.html.twig', $data);
}
```

## 📖 Utilisation

### Méthode 1 : Injection du service
```php
use App\Bundle\PdfGeneratorBundle\Service\PdfService;

class MyController extends AbstractController
{
    public function action(PdfService $pdfService)
    {
        $pdf = $pdfService->generateChapterPdf($chapitre);
    }
}
```

### Méthode 2 : Via l'alias
```php
public function action(ContainerInterface $container)
{
    $pdfService = $container->get('pdf.generator');
    $pdf = $pdfService->generateFromTemplate('pdf/template.html.twig', $data);
}
```

## 🎨 Templates PDF

### Template de chapitre (inchangé)
```twig
{# templates/pdf/chapitre.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <style>
        .logo {
            width: 150px;
        }
    </style>
</head>
<body>
    <img src="{{ absolute_url(asset('images/logo-autolearn.png')) }}" class="logo">
    <h1>{{ chapitre.titre }}</h1>
    <div>{{ chapitre.contenu|raw }}</div>
</body>
</html>
```

## 🔍 Méthodes Disponibles

### generateFromTemplate()
```php
$dompdf = $pdfService->generateFromTemplate(
    'pdf/template.html.twig',
    ['data' => 'value'],
    ['orientation' => 'landscape']
);
```

### generatePdfString()
```php
$pdfContent = $pdfService->generatePdfString(
    'pdf/template.html.twig',
    ['data' => 'value']
);
```

### generateChapterPdf()
```php
$dompdf = $pdfService->generateChapterPdf($chapitre);
```

### generateCertificate()
```php
$pdfContent = $pdfService->generateCertificate([
    'studentName' => 'John Doe',
    'courseName' => 'PHP',
    'date' => new \DateTime()
]);
```

### generateBadge()
```php
$pdfContent = $pdfService->generateBadge([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'eventName' => 'Conference 2024'
]);
```

## ⚙️ Configuration

### Options par défaut
```yaml
# src/Bundle/PdfGeneratorBundle/Resources/config/services.yaml
App\Bundle\PdfGeneratorBundle\Service\PdfService:
    arguments:
        $options:
            defaultFont: 'DejaVu Sans'
            isRemoteEnabled: true
            isHtml5ParserEnabled: true
            isFontSubsettingEnabled: true
```

### Options personnalisées
```php
$pdf = $pdfService->generateFromTemplate('pdf/template.html.twig', $data, [
    'paperSize' => 'A4',
    'orientation' => 'landscape',
    'defaultFont' => 'Arial'
]);
```

## 🧪 Tests

### Test de génération
```bash
# Accéder à un chapitre
http://localhost:8000/front/1/pdf

# Télécharger
http://localhost:8000/front/1/pdf/download
```

### Vérifier le bundle
```bash
php bin/console debug:container pdf.generator
php bin/console debug:autowiring PdfService
```

## 📊 Comparaison Avant/Après

### Avant (Service simple)
```
src/Service/PdfGeneratorService.php
```
- Service isolé
- Pas de structure bundle
- Difficile à réutiliser

### Après (Bundle Symfony)
```
src/Bundle/PdfGeneratorBundle/
├── PdfGeneratorBundle.php
├── Service/PdfService.php
├── DependencyInjection/
├── Resources/config/
└── README.md
```
- Structure professionnelle
- Réutilisable
- Bien documenté
- Extensible

## 🎓 Bonnes Pratiques

### 1. Utiliser l'injection de dépendances
```php
public function __construct(private PdfService $pdfService) {}
```

### 2. Gérer les erreurs
```php
try {
    $pdf = $pdfService->generateChapterPdf($chapitre);
} catch (\Exception $e) {
    $this->addFlash('error', 'Erreur de génération PDF');
}
```

### 3. Optimiser les images
```twig
<img src="{{ absolute_url(asset('images/logo.png')) }}" width="150">
```

## 🐛 Dépannage

### Le bundle n'est pas reconnu
```bash
php bin/console cache:clear
composer dump-autoload
```

### Les images ne s'affichent pas
- Vérifier `isRemoteEnabled: true`
- Utiliser `absolute_url(asset('...'))`

### Erreur de police
- Utiliser 'DejaVu Sans' pour UTF-8
- Installer les polices sur le serveur

## 📚 Ressources

- [Documentation Dompdf](https://github.com/dompdf/dompdf)
- [Symfony Bundles](https://symfony.com/doc/current/bundles.html)
- [Bundle README](src/Bundle/PdfGeneratorBundle/README.md)

## ✅ Checklist de Migration

- [x] Bundle créé
- [x] Service implémenté
- [x] Configuration ajoutée
- [x] Bundle enregistré dans bundles.php
- [x] Contrôleur mis à jour
- [x] Cache vidé
- [x] Tests effectués
- [x] Documentation créée
- [x] Frontoffice préservé
- [x] Logo AutoLearn préservé

## 🎉 Résultat

Un bundle PDF professionnel, réutilisable et bien structuré, sans aucun changement visible pour l'utilisateur final !

**Temps de transformation :** Quelques minutes
**Impact utilisateur :** Aucun
**Qualité du code :** Améliorée
**Maintenabilité :** Excellente
