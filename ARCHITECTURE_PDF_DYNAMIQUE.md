# 🏗️ Architecture du Système PDF Dynamique

## 📐 Vue d'Ensemble

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUX DE GÉNÉRATION PDF                       │
└─────────────────────────────────────────────────────────────────┘

1. Étudiant clique sur "Télécharger PDF"
                    ↓
2. Route Symfony appelée (/chapitre/front/{id}/pdf)
                    ↓
3. ChapitreController récupère le chapitre depuis la BDD
                    ↓
4. PdfGeneratorService appelé avec le chapitre
                    ↓
5. Template Twig (pdf/chapitre.html.twig) rendu en HTML
                    ↓
6. Dompdf convertit HTML → PDF
                    ↓
7. PDF retourné au navigateur (inline ou download)
                    ↓
8. Étudiant voit/télécharge le PDF
```

---

## 🗂️ Structure des Fichiers

```
autolearn/
│
├── src/
│   ├── Controller/
│   │   └── ChapitreController.php          # Routes PDF ajoutées
│   │       ├── pdfPreview()                # Afficher PDF dans navigateur
│   │       └── pdfDownload()               # Télécharger PDF
│   │
│   └── Service/
│       └── PdfGeneratorService.php         # Service de génération PDF
│           ├── generatePdf()               # Méthode générique
│           └── generateChapterPdf()        # Méthode spécifique chapitre
│
├── templates/
│   ├── pdf/
│   │   └── chapitre.html.twig              # Template PDF stylisé
│   │       ├── Header fixe (logo + titre)
│   │       ├── Métadonnées (cours, niveau, date)
│   │       ├── Contenu formaté
│   │       └── Footer fixe (branding + pagination)
│   │
│   └── frontoffice/
│       └── chapitre/
│           ├── show.html.twig              # Boutons PDF ajoutés
│           └── index.html.twig             # Bouton PDF dans liste
│
├── public/
│   └── frontoffice/
│       └── images/
│           └── auto.png                    # Logo Autolearn
│
└── composer.json                           # Dompdf ajouté
```

---

## 🔄 Flux de Données Détaillé

### 1. Requête HTTP

```
GET /chapitre/front/1/pdf
```

### 2. Routing Symfony

```php
#[Route('/chapitre/front/{id}/pdf', name: 'app_chapitre_pdf_preview')]
public function pdfPreview(Chapitre $chapitre, PdfGeneratorService $pdfGenerator)
```

### 3. Récupération des Données

```php
// Symfony récupère automatiquement le chapitre via ParamConverter
// $chapitre contient :
// - id
// - titre
// - contenu (HTML)
// - ordre
// - cours (relation)
```

### 4. Génération du PDF

```php
// Appel du service
$dompdf = $pdfGenerator->generateChapterPdf($chapitre);

// Le service :
// 1. Configure Dompdf
// 2. Rend le template Twig avec les données
// 3. Convertit HTML → PDF
// 4. Retourne l'objet Dompdf
```

### 5. Rendu du Template

```twig
{# templates/pdf/chapitre.html.twig #}

<!-- Header -->
<img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}">
<h1>{{ chapitre.titre }}</h1>

<!-- Métadonnées -->
<p>Chapitre: {{ chapitre.ordre }}</p>
<p>Cours: {{ chapitre.cours.titre }}</p>

<!-- Contenu -->
{{ chapitre.contenu|raw }}

<!-- Footer -->
<div class="page-number"></div>
```

### 6. Conversion HTML → PDF

```php
// Dompdf traite :
// - Les styles CSS inline
// - Les images (via absolute_url)
// - La pagination automatique
// - Les polices
```

### 7. Réponse HTTP

```php
return new Response(
    $dompdf->output(),              // Contenu binaire du PDF
    Response::HTTP_OK,
    [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="chapitre-1-intro.pdf"'
    ]
);
```

---

## 🎨 Composants du Template PDF

### Header Fixe
```css
.header {
    position: fixed;
    top: -80px;
    border-bottom: 3px solid #4A90E2;
}
```
- Logo Autolearn
- Titre du chapitre
- Ligne de séparation

### Métadonnées
```twig
<div class="chapter-meta">
    <p><strong>Chapitre:</strong> {{ chapitre.ordre }}</p>
    <p><strong>Cours:</strong> {{ chapitre.cours.titre }}</p>
    <p><strong>Matière:</strong> {{ chapitre.cours.matiere }}</p>
    <p><strong>Niveau:</strong> {{ chapitre.cours.niveau }}</p>
    <p><strong>Date:</strong> {{ "now"|date("d/m/Y") }}</p>
</div>
```

### Contenu Formaté
```css
.content h2 { color: #4A90E2; }
.content pre { background: #f5f5f5; border-left: 4px solid #4A90E2; }
.content code { font-family: 'Courier New'; }
```

### Footer Fixe
```css
.footer {
    position: fixed;
    bottom: -60px;
    border-top: 2px solid #4A90E2;
}
.page-number:before {
    content: "Page " counter(page);
}
```

---

## 🔧 Configuration Dompdf

### Options Principales

```php
$pdfOptions = new Options();

// Police par défaut
$pdfOptions->set('defaultFont', 'DejaVu Sans');

// Charger images externes
$pdfOptions->set('isRemoteEnabled', true);

// Support HTML5
$pdfOptions->set('isHtml5ParserEnabled', true);

// Optimiser polices
$pdfOptions->set('isFontSubsettingEnabled', true);
```

### Format du Papier

```php
// A4 Portrait (par défaut)
$dompdf->setPaper('A4', 'portrait');

// A4 Paysage
$dompdf->setPaper('A4', 'landscape');

// US Letter
$dompdf->setPaper('Letter', 'portrait');
```

---

## 🎯 Points Clés de l'Architecture

### 1. Séparation des Responsabilités

```
Controller     → Gère les routes et les requêtes HTTP
Service        → Logique métier de génération PDF
Template       → Présentation et style du PDF
Entity         → Données du chapitre
```

### 2. Réutilisabilité

Le service `PdfGeneratorService` peut être utilisé pour :
- Chapitres
- Cours complets
- Certificats
- Rapports
- Factures
- Etc.

```php
// Générer un PDF pour n'importe quel template
$pdf = $pdfGenerator->generatePdf('pdf/certificat.html.twig', [
    'etudiant' => $etudiant,
    'cours' => $cours
]);
```

### 3. Flexibilité

Le template Twig permet :
- Personnalisation facile du style
- Ajout de données dynamiques
- Conditions et boucles
- Traductions

```twig
{% if app.user %}
    <p>Généré pour : {{ app.user.prenom }} {{ app.user.nom }}</p>
{% endif %}
```

### 4. Performance

- Génération à la demande (pas de stockage)
- Pas de fichiers PDF statiques à gérer
- Cache possible si nécessaire

---

## 🔐 Sécurité

### Contrôle d'Accès

```php
// Ajouter une vérification d'accès
#[Route('/chapitre/front/{id}/pdf')]
public function pdfPreview(Chapitre $chapitre, PdfGeneratorService $pdfGenerator)
{
    // Vérifier que l'utilisateur a accès au chapitre
    $this->denyAccessUnlessGranted('VIEW', $chapitre);
    
    // Générer le PDF
    $dompdf = $pdfGenerator->generateChapterPdf($chapitre);
    
    // ...
}
```

### Validation des Entrées

```php
// Symfony valide automatiquement l'ID
// Si l'ID n'existe pas → 404
public function pdfPreview(Chapitre $chapitre, ...)
```

---

## 📊 Avantages de cette Architecture

### ✅ Dynamique
- Contenu toujours à jour
- Pas de désynchronisation

### ✅ Maintenable
- Code organisé et séparé
- Facile à modifier

### ✅ Scalable
- Service réutilisable
- Peut générer des PDF pour d'autres entités

### ✅ Performant
- Génération rapide
- Pas de stockage inutile

### ✅ Professionnel
- Branding cohérent
- Design soigné

---

## 🚀 Extensions Possibles

### 1. Cache des PDF

```php
// Stocker le PDF en cache pour éviter de régénérer
$cacheKey = 'pdf_chapitre_' . $chapitre->getId();
if ($cache->has($cacheKey)) {
    return $cache->get($cacheKey);
}
$pdf = $pdfGenerator->generateChapterPdf($chapitre);
$cache->set($cacheKey, $pdf, 3600); // 1 heure
```

### 2. PDF Multi-Chapitres

```php
#[Route('/cours/{id}/pdf')]
public function coursPdf(Cours $cours, PdfGeneratorService $pdfGenerator)
{
    return $pdfGenerator->generatePdf('pdf/cours-complet.html.twig', [
        'cours' => $cours,
        'chapitres' => $cours->getChapitres()
    ]);
}
```

### 3. Watermark Personnalisé

```php
$canvas = $dompdf->getCanvas();
$canvas->text(400, 750, "Confidentiel - " . $user->getNom(), null, 10, [0.5, 0.5, 0.5]);
```

### 4. Envoi par Email

```php
$email = (new Email())
    ->to($user->getEmail())
    ->subject('Votre chapitre PDF')
    ->attach($dompdf->output(), 'chapitre.pdf', 'application/pdf');
    
$mailer->send($email);
```

### 5. Statistiques

```php
// Enregistrer chaque téléchargement
$stat = new PdfDownload();
$stat->setUser($this->getUser());
$stat->setChapitre($chapitre);
$stat->setDownloadedAt(new \DateTime());
$entityManager->persist($stat);
$entityManager->flush();
```

---

## 📈 Diagramme de Séquence

```
Étudiant          Controller          Service          Dompdf          BDD
   |                  |                  |                |              |
   |-- Clic PDF ----->|                  |                |              |
   |                  |                  |                |              |
   |                  |-- Get Chapitre ----------------------->|         |
   |                  |<--------------------- Chapitre --------|         |
   |                  |                  |                |              |
   |                  |-- Generate PDF ->|                |              |
   |                  |                  |-- Render Twig -|              |
   |                  |                  |<- HTML --------|              |
   |                  |                  |                |              |
   |                  |                  |-- Convert ---->|              |
   |                  |                  |<- PDF ---------|              |
   |                  |<- Dompdf --------|                |              |
   |                  |                  |                |              |
   |<- Response PDF --|                  |                |              |
   |                  |                  |                |              |
```

---

## ✅ Checklist d'Implémentation

- [x] Installer Dompdf
- [x] Créer PdfGeneratorService
- [x] Créer template PDF
- [x] Ajouter routes dans Controller
- [x] Ajouter boutons dans l'interface
- [x] Tester prévisualisation
- [x] Tester téléchargement
- [x] Vérifier le style du PDF
- [x] Documenter l'architecture

---

**L'architecture PDF dynamique est complète et opérationnelle ! 🎉**
