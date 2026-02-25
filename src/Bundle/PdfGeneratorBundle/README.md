# PDF Generator Bundle

Bundle Symfony pour la génération de PDF avec Dompdf et Twig.

## 📦 Installation

Le bundle est déjà installé dans votre application.

### Enregistrement du bundle

Ajoutez dans `config/bundles.php` :

```php
App\Bundle\PdfGeneratorBundle\PdfGeneratorBundle::class => ['all' => true],
```

## 🚀 Utilisation

### Dans un contrôleur

```php
use App\Bundle\PdfGeneratorBundle\Service\PdfService;
use Symfony\Component\HttpFoundation\Response;

class MyController extends AbstractController
{
    public function generatePdf(PdfService $pdfService): Response
    {
        // Générer un PDF depuis un template
        $dompdf = $pdfService->generateFromTemplate('pdf/my_template.html.twig', [
            'title' => 'Mon PDF',
            'content' => 'Contenu du PDF'
        ]);
        
        // Retourner le PDF
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"'
        ]);
    }
}
```

### Générer un PDF de chapitre

```php
public function chapterPdf(Chapitre $chapitre, PdfService $pdfService): Response
{
    $dompdf = $pdfService->generateChapterPdf($chapitre);
    
    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}
```

### Générer un certificat

```php
$pdfContent = $pdfService->generateCertificate([
    'studentName' => 'John Doe',
    'courseName' => 'PHP Avancé',
    'date' => new \DateTime()
]);

// Envoyer par email ou télécharger
```

### Générer un badge

```php
$badgeContent = $pdfService->generateBadge([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'eventName' => 'Conférence 2024'
]);
```

## 🎨 Templates

Les templates Twig doivent être placés dans `templates/pdf/`.

### Exemple de template

```twig
{# templates/pdf/chapitre.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ chapitre.titre }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ absolute_url(asset('images/logo.png')) }}" class="logo" alt="AutoLearn">
        <h1>{{ chapitre.titre }}</h1>
    </div>
    
    <div class="content">
        {{ chapitre.contenu|raw }}
    </div>
</body>
</html>
```

## ⚙️ Configuration

### Options par défaut

Le service est configuré avec ces options par défaut :

```yaml
defaultFont: 'DejaVu Sans'
isRemoteEnabled: true
isHtml5ParserEnabled: true
isFontSubsettingEnabled: true
```

### Options personnalisées

Vous pouvez passer des options personnalisées :

```php
$dompdf = $pdfService->generateFromTemplate('pdf/template.html.twig', $data, [
    'orientation' => 'landscape',
    'paperSize' => 'A4',
    'defaultFont' => 'Arial'
]);
```

## 📋 Fonctionnalités

- ✅ Génération PDF depuis templates Twig
- ✅ Support des images (logo AutoLearn)
- ✅ Support des styles CSS
- ✅ Orientation portrait/paysage
- ✅ Tailles de papier personnalisables
- ✅ Polices personnalisables
- ✅ Méthodes spécialisées (chapitre, certificat, badge)

## 🎯 Cas d'usage

### 1. PDF de chapitre
- Prévisualisation dans le navigateur
- Téléchargement
- Logo AutoLearn inclus

### 2. Certificats
- Génération automatique
- Envoi par email
- Format paysage

### 3. Badges
- Format personnalisé (10x14cm)
- QR code support
- Impression directe

## 🔧 Dépendances

- `dompdf/dompdf` - Génération PDF
- `symfony/twig-bundle` - Templates
- `symfony/http-foundation` - Réponses HTTP

## 📝 Notes

- Les images doivent utiliser des chemins absolus : `absolute_url(asset('...'))`
- Le logo AutoLearn est dans `public/images/logo.png`
- Les polices doivent être installées sur le serveur
- Pour les caractères spéciaux, utilisez 'DejaVu Sans'

## 🎓 Exemples avancés

### Ajouter un header/footer

```php
$dompdf = $pdfService->generateFromTemplate('pdf/template.html.twig', $data);

// Ajouter un footer avec numéro de page
$canvas = $dompdf->getCanvas();
$canvas->page_text(520, 820, "Page {PAGE_NUM} / {PAGE_COUNT}", null, 10);
```

### Générer plusieurs PDF

```php
foreach ($chapitres as $chapitre) {
    $pdf = $pdfService->generateChapterPdf($chapitre);
    file_put_contents("chapter_{$chapitre->getId()}.pdf", $pdf->output());
}
```

## 🐛 Dépannage

### Les images ne s'affichent pas
- Vérifiez que `isRemoteEnabled` est à `true`
- Utilisez `absolute_url(asset('...'))`

### Les caractères spéciaux ne s'affichent pas
- Utilisez la police 'DejaVu Sans'
- Vérifiez l'encodage UTF-8

### Le PDF est vide
- Vérifiez le template Twig
- Consultez les logs Symfony

## 📚 Documentation

- [Dompdf Documentation](https://github.com/dompdf/dompdf)
- [Symfony Bundles](https://symfony.com/doc/current/bundles.html)

## 👨‍💻 Auteur

Bundle créé pour AutoLearn Platform
