# 🎨 Exemples de Personnalisation PDF

## 📝 Cas d'Usage Courants

---

## 1️⃣ Ajouter le Nom de l'Étudiant

### Dans le Controller

```php
#[Route('/chapitre/front/{id}/pdf', name: 'app_chapitre_pdf_preview')]
public function pdfPreview(Chapitre $chapitre, PdfGeneratorService $pdfGenerator): Response
{
    $user = $this->getUser();
    
    $dompdf = $pdfGenerator->generatePdf('pdf/chapitre.html.twig', [
        'chapitre' => $chapitre,
        'etudiant' => $user  // Ajouter l'utilisateur
    ]);
    
    // ...
}
```

### Dans le Template

```twig
<div class="chapter-meta">
    {% if etudiant %}
        <p><strong>Étudiant:</strong> {{ etudiant.prenom }} {{ etudiant.nom }}</p>
        <p><strong>Email:</strong> {{ etudiant.email }}</p>
    {% endif %}
    <p><strong>Chapitre:</strong> {{ chapitre.ordre }}</p>
    <!-- ... -->
</div>
```

---

## 2️⃣ Ajouter le Score du Quiz

### Dans le Controller

```php
#[Route('/chapitre/front/{id}/pdf', name: 'app_chapitre_pdf_preview')]
public function pdfPreview(
    Chapitre $chapitre, 
    PdfGeneratorService $pdfGenerator,
    QuizResultRepository $quizResultRepo
): Response
{
    $user = $this->getUser();
    
    // Récupérer le score du quiz
    $quizResult = $quizResultRepo->findOneBy([
        'user' => $user,
        'chapitre' => $chapitre
    ]);
    
    $dompdf = $pdfGenerator->generatePdf('pdf/chapitre.html.twig', [
        'chapitre' => $chapitre,
        'etudiant' => $user,
        'score' => $quizResult ? $quizResult->getScore() : null
    ]);
    
    // ...
}
```

### Dans le Template

```twig
{% if score is not null %}
    <div class="score-badge" style="background: {% if score >= 80 %}#4CAF50{% elseif score >= 60 %}#FF9800{% else %}#F44336{% endif %}; color: white; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0;">
        <h3 style="margin: 0;">Score obtenu</h3>
        <p style="font-size: 36pt; font-weight: bold; margin: 10px 0;">{{ score }}%</p>
        <p style="margin: 0;">
            {% if score >= 80 %}
                🎉 Excellent travail !
            {% elseif score >= 60 %}
                👍 Bon travail !
            {% else %}
                💪 Continue tes efforts !
            {% endif %}
        </p>
    </div>
{% endif %}
```

---

## 3️⃣ Ajouter un Certificat de Complétion

### Template Certificat

```twig
{# templates/pdf/certificat.html.twig #}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .certificat-border {
            border: 10px solid #4A90E2;
            padding: 40px;
            margin: 20px;
        }
        .logo {
            height: 80px;
        }
        h1 {
            font-size: 48pt;
            color: #4A90E2;
            margin: 30px 0;
        }
        .etudiant-nom {
            font-size: 36pt;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
        }
        .details {
            font-size: 14pt;
            margin: 30px 0;
        }
        .signature {
            margin-top: 60px;
            display: inline-block;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificat-border">
        <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" class="logo">
        
        <h1>🎓 Certificat de Réussite</h1>
        
        <p style="font-size: 16pt;">Ce certificat atteste que</p>
        
        <p class="etudiant-nom">{{ etudiant.prenom }} {{ etudiant.nom }}</p>
        
        <p style="font-size: 16pt;">a complété avec succès le cours</p>
        
        <p style="font-size: 24pt; font-weight: bold; color: #4A90E2;">{{ cours.titre }}</p>
        
        <div class="details">
            <p><strong>Matière:</strong> {{ cours.matiere }}</p>
            <p><strong>Niveau:</strong> {{ cours.niveau }}</p>
            <p><strong>Durée:</strong> {{ cours.duree }} heures</p>
            <p><strong>Score final:</strong> {{ score }}%</p>
            <p><strong>Date:</strong> {{ "now"|date("d/m/Y") }}</p>
        </div>
        
        <div class="signature">
            <p><strong>Autolearn</strong></p>
            <p>Plateforme d'apprentissage en ligne</p>
        </div>
    </div>
</body>
</html>
```

### Route Certificat

```php
#[Route('/cours/{id}/certificat', name: 'app_cours_certificat')]
public function certificat(
    Cours $cours, 
    PdfGeneratorService $pdfGenerator
): Response
{
    $user = $this->getUser();
    
    // Vérifier que l'étudiant a complété le cours
    // ...
    
    $dompdf = $pdfGenerator->generatePdf('pdf/certificat.html.twig', [
        'cours' => $cours,
        'etudiant' => $user,
        'score' => 85 // Score moyen du cours
    ]);
    
    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="certificat-' . $this->slugify($cours->getTitre()) . '.pdf"'
        ]
    );
}
```

---

## 4️⃣ PDF Multi-Chapitres (Cours Complet)

### Template Cours Complet

```twig
{# templates/pdf/cours-complet.html.twig #}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Styles similaires au template chapitre */
        .table-of-contents {
            page-break-after: always;
            padding: 20px;
        }
        .toc-item {
            margin: 10px 0;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .chapter-section {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Page de garde -->
    <div style="text-align: center; padding: 100px 0;">
        <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" style="height: 100px;">
        <h1 style="font-size: 36pt; color: #4A90E2;">{{ cours.titre }}</h1>
        <p style="font-size: 18pt;">{{ cours.description }}</p>
        <p style="margin-top: 50px;">
            <strong>Matière:</strong> {{ cours.matiere }}<br>
            <strong>Niveau:</strong> {{ cours.niveau }}<br>
            <strong>Durée:</strong> {{ cours.duree }} heures
        </p>
    </div>
    
    <!-- Table des matières -->
    <div class="table-of-contents">
        <h2>📑 Table des Matières</h2>
        {% for chapitre in cours.chapitres %}
            <div class="toc-item">
                <strong>Chapitre {{ chapitre.ordre }}:</strong> {{ chapitre.titre }}
            </div>
        {% endfor %}
    </div>
    
    <!-- Chapitres -->
    {% for chapitre in cours.chapitres %}
        <div class="chapter-section">
            <h1 style="color: #4A90E2;">Chapitre {{ chapitre.ordre }}: {{ chapitre.titre }}</h1>
            <div class="content">
                {{ chapitre.contenu|raw }}
            </div>
        </div>
    {% endfor %}
</body>
</html>
```

### Route Cours Complet

```php
#[Route('/cours/{id}/pdf', name: 'app_cours_pdf_complet')]
public function coursPdfComplet(
    Cours $cours, 
    PdfGeneratorService $pdfGenerator
): Response
{
    $dompdf = $pdfGenerator->generatePdf('pdf/cours-complet.html.twig', [
        'cours' => $cours
    ]);
    
    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="cours-complet-' . $this->slugify($cours->getTitre()) . '.pdf"'
        ]
    );
}
```

---

## 5️⃣ Ajouter un Watermark

### Dans le Controller

```php
#[Route('/chapitre/front/{id}/pdf', name: 'app_chapitre_pdf_preview')]
public function pdfPreview(Chapitre $chapitre, PdfGeneratorService $pdfGenerator): Response
{
    $dompdf = $pdfGenerator->generateChapterPdf($chapitre);
    
    // Ajouter un watermark
    $canvas = $dompdf->getCanvas();
    $canvas->page_text(
        300, 400,  // Position (x, y)
        "CONFIDENTIEL",
        null,
        40,  // Taille de police
        [0.8, 0.8, 0.8],  // Couleur RGB (gris clair)
        2,  // Angle de rotation
        -1  // Opacité
    );
    
    return new Response($dompdf->output(), Response::HTTP_OK, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="chapitre.pdf"'
    ]);
}
```

---

## 6️⃣ Changer le Style selon le Niveau

### Dans le Template

```twig
{% set niveau_color = {
    'Débutant': '#4CAF50',
    'Intermédiaire': '#FF9800',
    'Avancé': '#F44336'
} %}

<style>
    .header {
        border-bottom: 3px solid {{ niveau_color[chapitre.cours.niveau] ?? '#4A90E2' }};
    }
    .content h2 {
        color: {{ niveau_color[chapitre.cours.niveau] ?? '#4A90E2' }};
    }
</style>
```

---

## 7️⃣ Ajouter des Notes de l'Étudiant

### Dans le Controller

```php
#[Route('/chapitre/front/{id}/pdf', name: 'app_chapitre_pdf_preview')]
public function pdfPreview(
    Chapitre $chapitre, 
    PdfGeneratorService $pdfGenerator,
    NoteRepository $noteRepo
): Response
{
    $user = $this->getUser();
    
    // Récupérer les notes de l'étudiant pour ce chapitre
    $notes = $noteRepo->findBy([
        'user' => $user,
        'chapitre' => $chapitre
    ]);
    
    $dompdf = $pdfGenerator->generatePdf('pdf/chapitre.html.twig', [
        'chapitre' => $chapitre,
        'notes' => $notes
    ]);
    
    // ...
}
```

### Dans le Template

```twig
{% if notes is not empty %}
    <div class="notes-section" style="background: #FFF9C4; padding: 20px; border-left: 4px solid #FBC02D; margin: 20px 0;">
        <h3 style="color: #F57F17;">📝 Mes Notes</h3>
        {% for note in notes %}
            <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 4px;">
                <p style="font-style: italic;">"{{ note.contenu }}"</p>
                <p style="font-size: 10pt; color: #666;">{{ note.createdAt|date('d/m/Y H:i') }}</p>
            </div>
        {% endfor %}
    </div>
{% endif %}
```

---

## 8️⃣ Générer un Rapport de Progression

### Template Rapport

```twig
{# templates/pdf/rapport-progression.html.twig #}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e0e0e0;
            border-radius: 15px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
            text-align: center;
            line-height: 30px;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>📊 Rapport de Progression</h1>
    
    <h2>Étudiant: {{ etudiant.prenom }} {{ etudiant.nom }}</h2>
    
    <h3>Cours: {{ cours.titre }}</h3>
    
    <div class="progress-bar">
        <div class="progress-fill" style="width: {{ progression }}%;">
            {{ progression }}%
        </div>
    </div>
    
    <h3>Détails par Chapitre</h3>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #4A90E2; color: white;">
                <th style="padding: 10px; text-align: left;">Chapitre</th>
                <th style="padding: 10px;">Statut</th>
                <th style="padding: 10px;">Score Quiz</th>
            </tr>
        </thead>
        <tbody>
            {% for chapitre in cours.chapitres %}
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">{{ chapitre.titre }}</td>
                    <td style="padding: 10px; text-align: center;">
                        {% if chapitre.completed %}
                            ✅ Complété
                        {% else %}
                            ⏳ En cours
                        {% endif %}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        {{ chapitre.quizScore ?? 'N/A' }}
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</body>
</html>
```

---

## 9️⃣ PDF avec QR Code

### Installation

```bash
composer require endroid/qr-code
```

### Dans le Controller

```php
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

#[Route('/chapitre/front/{id}/pdf', name: 'app_chapitre_pdf_preview')]
public function pdfPreview(Chapitre $chapitre, PdfGeneratorService $pdfGenerator): Response
{
    // Générer QR Code
    $qrCode = QrCode::create($this->generateUrl('app_chapitre_show_front', [
        'id' => $chapitre->getId()
    ], UrlGeneratorInterface::ABSOLUTE_URL));
    
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    // Convertir en base64
    $qrCodeBase64 = base64_encode($result->getString());
    
    $dompdf = $pdfGenerator->generatePdf('pdf/chapitre.html.twig', [
        'chapitre' => $chapitre,
        'qrCode' => $qrCodeBase64
    ]);
    
    // ...
}
```

### Dans le Template

```twig
{% if qrCode %}
    <div style="text-align: center; margin: 30px 0;">
        <h3>📱 Accès Rapide</h3>
        <p>Scannez ce QR code pour accéder au chapitre en ligne</p>
        <img src="data:image/png;base64,{{ qrCode }}" style="width: 150px; height: 150px;">
    </div>
{% endif %}
```

---

## 🔟 Statistiques de Lecture

### Dans le Template

```twig
<div class="stats" style="background: #E3F2FD; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="color: #1976D2;">📈 Statistiques</h3>
    <div style="display: flex; justify-content: space-around;">
        <div style="text-align: center;">
            <p style="font-size: 24pt; font-weight: bold; color: #1976D2;">{{ stats.lectures }}</p>
            <p>Lectures</p>
        </div>
        <div style="text-align: center;">
            <p style="font-size: 24pt; font-weight: bold; color: #1976D2;">{{ stats.temps_moyen }}</p>
            <p>Minutes (moy.)</p>
        </div>
        <div style="text-align: center;">
            <p style="font-size: 24pt; font-weight: bold; color: #1976D2;">{{ stats.taux_reussite }}%</p>
            <p>Taux de réussite</p>
        </div>
    </div>
</div>
```

---

## ✅ Checklist de Personnalisation

- [ ] Nom de l'étudiant ajouté
- [ ] Score du quiz affiché
- [ ] Certificat de complétion créé
- [ ] PDF multi-chapitres implémenté
- [ ] Watermark ajouté
- [ ] Style selon niveau
- [ ] Notes de l'étudiant incluses
- [ ] Rapport de progression créé
- [ ] QR Code intégré
- [ ] Statistiques affichées

---

**Toutes ces personnalisations sont modulaires et peuvent être combinées ! 🎨**
