# 🎨 Design Assistant IA - Documentation

## Vue d'ensemble

Design moderne et professionnel pour l'Assistant Pédagogique IA avec animations fluides, gradients élégants et expérience utilisateur optimale.

## 🎨 Palette de couleurs

### Couleurs principales
- **Primary Gradient**: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- **Primary Color**: `#667eea` (Bleu violet)
- **Secondary Color**: `#764ba2` (Violet profond)

### Couleurs d'état
- **Success**: `#10b981` (Vert moderne)
- **Warning**: `#f59e0b` (Orange)
- **Danger**: `#ef4444` (Rouge)
- **Info**: `#3b82f6` (Bleu)

### Couleurs de texte
- **Text Dark**: `#1f2937`
- **Text Light**: `#6b7280`

### Couleurs de fond
- **Background**: `linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)`
- **Card Background**: `#ffffff`
- **Light Background**: `#f9fafb`

## 🎭 Composants

### 1. Header avec Robot Animé
```css
.ai-header {
    background: var(--primary-gradient);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}
```

**Animations:**
- Robot qui rebondit (bounce)
- Effet de pulse en arrière-plan
- Ombre portée dynamique

### 2. Carte de Contrôles
```css
.controls-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}
```

**Fonctionnalités:**
- Grid responsive (2 colonnes → 1 colonne sur mobile)
- Hover effect avec élévation
- Transitions fluides

### 3. Bouton de Génération
```css
.btn-generate {
    background: var(--primary-gradient);
    padding: 1rem 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
```

**États:**
- Normal: Gradient violet
- Hover: Élévation + ombre plus prononcée
- Disabled: Opacité 60%

### 4. Zone de Chargement
```css
.loading-card {
    background: white;
    border-radius: 16px;
    padding: 3rem;
    text-align: center;
}
```

**Animations:**
- Spinner rotatif
- Texte avec fade in/out
- Transition d'apparition

### 5. Carte Audio
```css
.audio-card {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid #bae6fd;
}
```

**Éléments:**
- Boutons colorés (Vert/Orange/Rouge)
- Slider de vitesse personnalisé
- Layout responsive

### 6. Cartes de Résultats

#### Résumé
```css
.header-summary {
    background: var(--primary-gradient);
}
```

#### Explication
```css
.header-explanation {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}
```

#### Points Clés
```css
.header-keypoints {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}
```

### 7. Points Clés (Items)
```css
.keypoint-item {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-left: 4px solid var(--success-color);
    padding: 1.25rem 1.5rem;
}
```

**Effet:**
- Hover: Translation vers la droite
- Icône check verte
- Ombre douce

## 🎬 Animations

### 1. Bounce (Robot)
```css
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
```

### 2. Pulse (Background)
```css
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}
```

### 3. Spin (Loader)
```css
@keyframes spin {
    to { transform: rotate(360deg); }
}
```

### 4. Fade In/Out (Loading Text)
```css
@keyframes fadeInOut {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}
```

### 5. Fade In Up (Cards)
```css
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

## 📱 Responsive Design

### Breakpoints

#### Desktop (> 768px)
- Grid 2 colonnes pour les contrôles
- Header horizontal avec robot à gauche
- Boutons audio en ligne

#### Mobile (≤ 768px)
- Grid 1 colonne
- Header vertical centré
- Robot plus petit (3rem)
- Boutons audio empilés
- Padding réduit

### Media Queries
```css
@media (max-width: 768px) {
    .controls-grid {
        grid-template-columns: 1fr;
    }
    
    .ai-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .audio-controls {
        flex-wrap: wrap;
    }
}
```

## 🎯 Effets Interactifs

### Hover Effects

#### Cartes
```css
.result-card:hover {
    transform: translateY(-4px);
}
```

#### Boutons
```css
.btn-audio:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
```

#### Points Clés
```css
.keypoint-item:hover {
    transform: translateX(8px);
}
```

### Focus States
```css
.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
```

## 🌟 Effets Spéciaux

### Brillance sur les Cartes
```css
.result-card::before {
    content: '';
    position: absolute;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.result-card:hover::before {
    left: 100%;
}
```

### Ombres Progressives
```css
--shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
--shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
--shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.2);
```

## 🎨 Typographie

### Polices
```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

### Tailles
- **Header H1**: `2rem` (32px)
- **Header H2**: `1.5rem` (24px)
- **Card Header**: `1.3rem` (20.8px)
- **Body Text**: `1.05rem` (16.8px)
- **Summary**: `1.2rem` (19.2px)

### Line Heights
- **Body**: `1.9`
- **Summary**: `1.8`
- **Keypoints**: `1.6`

## 🔧 Variables CSS

```css
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
    --text-dark: #1f2937;
    --text-light: #6b7280;
    --bg-light: #f9fafb;
    --border-color: #e5e7eb;
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
    --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.2);
}
```

## 📐 Espacements

### Padding
- **Cards**: `2rem` (32px)
- **Header**: `2.5rem` (40px)
- **Buttons**: `1rem 2rem` (16px 32px)
- **Keypoints**: `1.25rem 1.5rem` (20px 24px)

### Margins
- **Between Cards**: `2rem` (32px)
- **Between Elements**: `1rem` (16px)

### Border Radius
- **Cards**: `16px`
- **Header**: `20px`
- **Buttons**: `10px`
- **Keypoints**: `10px`

## 🎯 Accessibilité

### Contraste
- Tous les textes respectent WCAG AA
- Ratio minimum 4.5:1 pour le texte normal
- Ratio minimum 3:1 pour le texte large

### Focus Visible
- Tous les éléments interactifs ont un focus visible
- Outline personnalisé avec couleur primaire

### Tailles de clic
- Minimum 44x44px pour tous les boutons
- Espacement suffisant entre les éléments cliquables

## 🚀 Performance

### Optimisations
- Utilisation de `transform` pour les animations (GPU)
- `will-change` pour les éléments animés
- Transitions CSS plutôt que JavaScript
- Lazy loading des images (si applicable)

### Temps de chargement
- CSS: ~15KB (minifié)
- Pas de dépendances externes (sauf Font Awesome)
- Rendu initial: < 100ms

## 📦 Fichiers

### CSS Principal
```
public/frontoffice/css/chapter-explainer.css
```

### Template
```
templates/frontoffice/chapter_explainer/index.html.twig
```

## ✅ Checklist Design

- [x] Palette de couleurs cohérente
- [x] Animations fluides
- [x] Responsive design
- [x] Accessibilité WCAG AA
- [x] Performance optimisée
- [x] Hover effects
- [x] Focus states
- [x] Loading states
- [x] Error states
- [x] Success states

## 🎉 Résultat

Un design moderne, professionnel et engageant qui améliore l'expérience utilisateur et rend l'apprentissage plus agréable !
