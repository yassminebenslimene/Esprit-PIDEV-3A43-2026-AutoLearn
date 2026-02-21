# 📄 Guide de Génération PDF des Chapitres

## 🎯 Objectif

Transformer l'affichage des chapitres en PDF stylisé avec branding Autolearn, généré dynamiquement depuis la base de données.

---

## ✅ Ce qui a été implémenté

### 1. Installation de Dompdf
```bash
composer require dompdf/dompdf
```

### 2. Service de Génération PDF
**Fichier:** `src/Service/PdfGeneratorService.php`

Service réutilisable qui :
- Configure Dompdf avec les bonnes options
- Rend les templates Twig en HTML
- Génère le PDF
- Supporte les options personnalisées

### 3. Template PDF Stylisé
**Fichier:** `templates/pdf/chapitre.html.twig`

Template spécial avec :
- ✅ Header fixe avec logo Autolearn
- ✅ Footer avec pagination automatique
- ✅ Métadonnées du chapitre (ordre, cours, matière, niveau)
- ✅ Contenu HTML formaté
- ✅ Code Python avec coloration
- ✅ Branding Autolearn
- ✅ Design professionnel (marges, polices, couleurs)

### 4. Routes PDF
**Fichier:** `src/Controller/ChapitreController.php`

Deux routes ajoutées :

#### Route 1 : Prévisualisation PDF
```
GET /chapitre/front/{id}/pdf
```
- Affiche le PDF directement dans le navigateur
- Mode `inline` (pas de téléchargement)
- Nom du fichier : `chapitre-{ordre}-{titre-slug}.pdf`

#### Route 2 : Téléchargement PDF
```
GET /chapitre/front/{id}/pdf/download
```
- Force le téléchargement du PDF
- Mode `attachment`
- Nom du fichier : `chapitre-{ordre}-{titre-slug}.pdf`

### 5. Interface Utilisateur

#### Dans la page du chapitre (`show.html.twig`)
Section dédiée avec :
- Titre "Télécharger ce chapitre"
- Description
- 2 boutons :
  - **Prévisualiser PDF** (ouvre dans nouvel onglet)
  - **Télécharger PDF** (télécharge directement)

#### Dans la liste des chapitres (`index.html.twig`)
Bouton PDF ajouté dans chaque carte de chapitre :
- Icône PDF
- Style violet dégradé
- Téléchargement direct

---

## 🚀 Comment Tester

### Prérequis
1. ✅ MySQL démarré
2. ✅ Cours Python inséré en base (via `insert_python_course.sql`)
3. ✅ Serveur Symfony démarré

### Test 1 : Prévisualiser un PDF

1. Aller sur un chapitre :
```
http://localhost:8000/chapitre/front/[ID_CHAPITRE]
```

2. Scroller jusqu'à la section "Télécharger ce chapitre"

3. Cliquer sur **"Prévisualiser PDF"**

**Résultat attendu :**
- Nouvel onglet s'ouvre
- PDF s'affiche dans le navigateur
- Header avec logo Autolearn visible
- Contenu formaté correctement
- Footer avec pagination

---

### Test 2 : Télécharger un PDF

1. Sur la même page du chapitre

2. Cliquer sur **"Télécharger PDF"**

**Résultat attendu :**
- Téléchargement automatique
- Fichier nommé : `chapitre-1-introduction-a-python.pdf`
- PDF sauvegardé dans le dossier Téléchargements

---

### Test 3 : PDF depuis la liste des chapitres

1. Aller sur la liste des chapitres :
```
http://localhost:8000/chapitre/cours/[ID_COURS]
```

2. Sur une carte de chapitre, cliquer sur le bouton **"PDF"** (violet)

**Résultat attendu :**
- Téléchargement direct du PDF
- Pas besoin d'ouvrir le chapitre

---

### Test 4 : Accès direct via URL

**Prévisualisation :**
```
http://localhost:8000/chapitre/front/1/pdf
```

**Téléchargement :**
```
http://localhost:8000/chapitre/front/1/pdf/download
```

---

## 📋 Contenu du PDF Généré

### Structure du PDF

```
┌─────────────────────────────────────────────────┐
│  [LOGO AUTOLEARN]                               │
│  Introduction à Python                          │
│─────────────────────────────────────────────────│
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ Chapitre: 1                               │ │
│  │ Cours: Python Programming                 │ │
│  │ Matière: Informatique                     │ │
│  │ Niveau: Débutant                          │ │
│  │ Date de génération: 20/02/2026 à 14:30   │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  Bienvenue dans le monde de Python              │
│                                                 │
│  Python est un langage de programmation...      │
│                                                 │
│  Pourquoi apprendre Python ?                    │
│  • Syntaxe claire et lisible                    │
│  • Polyvalent                                   │
│  • Grande communauté                            │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ print("Hello, World!")                    │ │
│  │ print("Bienvenue dans Python!")           │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  [... suite du contenu ...]                     │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │       🎓 Autolearn                        │ │
│  │  Votre plateforme d'apprentissage         │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│─────────────────────────────────────────────────│
│  Autolearn - Plateforme d'apprentissage        │
│  Page 1                                         │
└─────────────────────────────────────────────────┘
```

### Éléments Inclus

✅ **Header fixe** (sur chaque page)
- Logo Autolearn
- Titre du chapitre
- Ligne de séparation bleue

✅ **Métadonnées**
- Numéro du chapitre
- Nom du cours
- Matière
- Niveau
- Date de génération

✅ **Contenu formaté**
- Titres H2, H3 stylisés
- Paragraphes justifiés
- Listes à puces
- Code Python avec fond gris et bordure bleue
- Liens cliquables

✅ **Footer fixe** (sur chaque page)
- Nom de la plateforme
- Numéro de page automatique

✅ **Branding**
- Section finale avec logo et message Autolearn

---

## 🎨 Personnalisation du PDF

### Modifier le Style

Éditer le fichier : `templates/pdf/chapitre.html.twig`

#### Changer les couleurs
```css
/* Couleur principale (actuellement bleu) */
border-bottom: 3px solid #4A90E2;  /* Changer #4A90E2 */

/* Couleur des titres */
.content h2 {
    color: #4A90E2;  /* Changer ici aussi */
}
```

#### Changer la police
```css
body {
    font-family: 'DejaVu Sans', Arial, sans-serif;  /* Modifier ici */
}
```

#### Ajuster les marges
```css
@page {
    margin: 100px 50px 80px 50px;  /* haut droite bas gauche */
}
```

---

### Ajouter des Informations

#### Ajouter le nom de l'étudiant

Dans `templates/pdf/chapitre.html.twig`, ajouter :
```twig
<div class="chapter-meta">
    <p><strong>Étudiant:</strong> {{ app.user.prenom }} {{ app.user.nom }}</p>
    <!-- ... autres métadonnées ... -->
</div>
```

#### Ajouter le score du quiz

Passer le score depuis le contrôleur :
```php
$dompdf = $pdfGenerator->generatePdf('pdf/chapitre.html.twig', [
    'chapitre' => $chapitre,
    'score' => $userScore  // Ajouter le score
]);
```

Dans le template :
```twig
{% if score is defined %}
    <p><strong>Score obtenu:</strong> {{ score }}%</p>
{% endif %}
```

---

## 🔧 Configuration Avancée

### Options Dompdf

Dans `src/Service/PdfGeneratorService.php` :

```php
$pdfOptions = new Options();
$pdfOptions->set('defaultFont', 'DejaVu Sans');
$pdfOptions->set('isRemoteEnabled', true);  // Charger images externes
$pdfOptions->set('isHtml5ParserEnabled', true);  // Support HTML5
$pdfOptions->set('isFontSubsettingEnabled', true);  // Optimiser polices
```

### Changer l'orientation

```php
$dompdf->setPaper('A4', 'landscape');  // Paysage au lieu de portrait
```

### Changer la taille du papier

```php
$dompdf->setPaper('Letter', 'portrait');  // Format US Letter
```

---

## 🐛 Problèmes Courants

### Le logo ne s'affiche pas

**Cause :** Chemin du logo incorrect

**Solution :** Vérifier que le fichier existe :
```
public/frontoffice/images/auto.png
```

Si le fichier est ailleurs, modifier dans le template :
```twig
<img src="{{ absolute_url(asset('chemin/vers/logo.png')) }}">
```

---

### Le contenu HTML n'est pas formaté

**Cause :** Le contenu n'est pas marqué comme `raw`

**Solution :** Vérifier dans le template :
```twig
{{ chapitre.contenu|raw }}  <!-- raw est important ! -->
```

---

### Les caractères spéciaux sont mal affichés

**Cause :** Problème d'encodage

**Solution :** Vérifier dans le template :
```html
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
```

---

### Le PDF est trop lent à générer

**Cause :** Contenu très long ou images lourdes

**Solutions :**
1. Activer le cache :
```php
$pdfOptions->set('isPhpEnabled', false);
```

2. Optimiser les images avant insertion

3. Paginer le contenu si trop long

---

### Erreur "Class Dompdf not found"

**Cause :** Dompdf pas installé

**Solution :**
```bash
composer require dompdf/dompdf
```

---

## 📊 URLs de Test

### Avec le cours Python (ID = 5, par exemple)

**Liste des chapitres :**
```
http://localhost:8000/chapitre/cours/5
```

**Chapitre 1 - Introduction :**
```
http://localhost:8000/chapitre/front/[ID_CHAPITRE_1]
```

**PDF Chapitre 1 - Prévisualisation :**
```
http://localhost:8000/chapitre/front/[ID_CHAPITRE_1]/pdf
```

**PDF Chapitre 1 - Téléchargement :**
```
http://localhost:8000/chapitre/front/[ID_CHAPITRE_1]/pdf/download
```

---

## 🎯 Avantages de cette Approche

### ✅ Dynamique
- Le PDF se met à jour automatiquement si le contenu change
- Pas besoin de régénérer manuellement

### ✅ Personnalisable
- Peut inclure le nom de l'étudiant
- Peut inclure le score du quiz
- Peut inclure la date de consultation

### ✅ Cohérent
- Même contenu que sur le site
- Pas de désynchronisation

### ✅ Professionnel
- Branding Autolearn
- Design soigné
- Pagination automatique

### ✅ Flexible
- Facile à modifier le style
- Facile d'ajouter des informations
- Réutilisable pour d'autres entités

---

## 🚀 Prochaines Étapes Possibles

### 1. Ajouter un Watermark
```php
// Dans le service
$dompdf->getCanvas()->text(
    400, 750, 
    "Autolearn - Confidentiel", 
    null, 10, [0.5, 0.5, 0.5]
);
```

### 2. Générer un PDF pour tout le cours
Créer une route qui génère un PDF avec tous les chapitres :
```php
#[Route('/cours/{id}/pdf', name: 'app_cours_pdf')]
public function coursPdf(Cours $cours, PdfGeneratorService $pdfGenerator)
{
    // Générer PDF avec tous les chapitres
}
```

### 3. Envoyer le PDF par email
```php
use Symfony\Component\Mime\Email;

$email = (new Email())
    ->to($user->getEmail())
    ->subject('Votre chapitre PDF')
    ->attach($dompdf->output(), 'chapitre.pdf', 'application/pdf');
```

### 4. Ajouter une table des matières
Pour les PDF multi-chapitres, générer automatiquement une table des matières.

### 5. Statistiques de téléchargement
Enregistrer qui télécharge quoi et quand.

---

## ✅ Checklist de Vérification

- [ ] Dompdf installé (`composer require dompdf/dompdf`)
- [ ] Service `PdfGeneratorService` créé
- [ ] Template `templates/pdf/chapitre.html.twig` créé
- [ ] Routes PDF ajoutées dans `ChapitreController`
- [ ] Boutons PDF ajoutés dans `show.html.twig`
- [ ] Bouton PDF ajouté dans `index.html.twig`
- [ ] Logo Autolearn présent dans `public/frontoffice/images/`
- [ ] Test prévisualisation fonctionne
- [ ] Test téléchargement fonctionne
- [ ] PDF contient le bon contenu
- [ ] PDF est bien formaté
- [ ] Pagination fonctionne

---

## 🎉 Résultat Final

Les étudiants peuvent maintenant :

✅ **Prévisualiser** les chapitres en PDF dans le navigateur  
✅ **Télécharger** les chapitres en PDF pour lecture hors ligne  
✅ **Accéder** au contenu avec le branding Autolearn  
✅ **Bénéficier** d'un format professionnel et lisible  

Le PDF est généré **dynamiquement** depuis la base de données, garantissant que le contenu est toujours à jour !

---

**Le système de génération PDF est maintenant opérationnel ! 🚀**
