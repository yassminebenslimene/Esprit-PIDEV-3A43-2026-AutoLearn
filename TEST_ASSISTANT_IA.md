# 🧪 Guide de Test - Assistant IA Explainer

## ✅ Prérequis

1. **Clé API Groq configurée**
   - Vérifier dans `.env.local`:
   ```env
   GROQ_API_KEY=votre_cle_api_groq_ici
   ```
   ✅ À configurer !

2. **Cache vidé**
   ```bash
   php bin/console cache:clear
   ```
   ✅ Fait !

3. **Serveur lancé**
   ```bash
   symfony serve
   ```

## 🚀 Test Rapide (5 minutes)

### Étape 1: Accéder à un chapitre
1. Lancer le serveur: `symfony serve`
2. Ouvrir: `http://localhost:8000`
3. Se connecter (si nécessaire)
4. Aller dans un cours
5. Cliquer sur un chapitre

### Étape 2: Tester l'Assistant IA
1. Sur la page du chapitre, cliquer sur le bouton **"🤖 Assistant IA"**
2. Vous devriez voir la page de l'assistant

### Étape 3: Générer une explication (Débutant)
1. Sélectionner **"Débutant"** dans le menu déroulant
2. Cliquer sur **"Générer l'explication"**
3. Attendre 5-10 secondes
4. Vérifier que vous voyez:
   - ✅ Un résumé
   - ✅ Une explication détaillée
   - ✅ 5 points clés

### Étape 4: Tester la synthèse vocale
1. Cliquer sur le bouton **"Lire"** (vert)
2. Vérifier que la voix commence à lire
3. Tester le bouton **"Pause"** (orange)
4. Tester le bouton **"Stop"** (rouge)
5. Ajuster la vitesse avec le slider

### Étape 5: Tester le niveau Avancé
1. Sélectionner **"Avancé"**
2. Cliquer sur **"Générer l'explication"**
3. Comparer avec l'explication débutant
4. L'explication devrait être plus technique

## 🎯 Scénarios de Test

### Test 1: Chapitre court
**Objectif:** Vérifier que l'IA gère les contenus courts

1. Trouver un chapitre avec peu de contenu
2. Générer l'explication
3. **Résultat attendu:** Explication cohérente même avec peu de contenu

### Test 2: Chapitre long
**Objectif:** Vérifier que l'IA gère les contenus longs

1. Trouver un chapitre avec beaucoup de contenu
2. Générer l'explication
3. **Résultat attendu:** Résumé concis, explication structurée

### Test 3: Différents niveaux
**Objectif:** Vérifier l'adaptation au niveau

1. Générer en mode "Débutant"
2. Noter le vocabulaire utilisé
3. Générer en mode "Avancé"
4. **Résultat attendu:** Vocabulaire plus technique en mode avancé

### Test 4: Synthèse vocale
**Objectif:** Vérifier tous les contrôles audio

1. Générer une explication
2. Cliquer sur "Lire"
3. Attendre 5 secondes
4. Cliquer sur "Pause"
5. Cliquer sur "Lire" (reprendre)
6. Cliquer sur "Stop"
7. Ajuster la vitesse à 1.5x
8. Cliquer sur "Lire"
9. **Résultat attendu:** Tous les contrôles fonctionnent

### Test 5: Responsive
**Objectif:** Vérifier sur mobile

1. Ouvrir les DevTools (F12)
2. Activer le mode mobile
3. Tester toutes les fonctionnalités
4. **Résultat attendu:** Interface adaptée, tout fonctionne

## 🐛 Dépannage

### Problème: "Erreur lors de la génération"

**Causes possibles:**
1. Clé API invalide
2. Pas de connexion internet
3. API Groq indisponible

**Solutions:**
```bash
# Vérifier la clé API
php bin/console debug:container --env-vars | grep GROQ

# Vérifier les logs
tail -f var/log/dev.log

# Tester la connexion à Groq
curl -H "Authorization: Bearer VOTRE_CLE" https://api.groq.com/openai/v1/models
```

### Problème: La synthèse vocale ne fonctionne pas

**Causes possibles:**
1. Navigateur non compatible
2. Permissions audio bloquées

**Solutions:**
- Utiliser Chrome ou Edge
- Vérifier les permissions du site
- Tester dans un autre navigateur

### Problème: Page 404

**Solutions:**
```bash
# Vider le cache
php bin/console cache:clear

# Vérifier les routes
php bin/console debug:router | grep chapter_explainer
```

## 📊 Checklist de Test Complet

### Fonctionnalités
- [ ] Accès à la page de l'assistant
- [ ] Sélection du niveau (débutant/avancé)
- [ ] Génération de l'explication
- [ ] Affichage du résumé
- [ ] Affichage de l'explication
- [ ] Affichage des points clés
- [ ] Bouton "Lire" fonctionne
- [ ] Bouton "Pause" fonctionne
- [ ] Bouton "Stop" fonctionne
- [ ] Slider de vitesse fonctionne
- [ ] Retour au chapitre fonctionne

### Interface
- [ ] Design moderne et attrayant
- [ ] Animations fluides
- [ ] Responsive sur mobile
- [ ] Icônes visibles
- [ ] Couleurs cohérentes
- [ ] Texte lisible

### Performance
- [ ] Génération en moins de 15 secondes
- [ ] Pas de lag dans l'interface
- [ ] Synthèse vocale fluide
- [ ] Pas d'erreurs dans la console

### Navigateurs
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (si disponible)

## 🎬 Démo pour l'encadrant

### Préparation (5 min)
1. Choisir 2-3 chapitres intéressants
2. Tester une fois chaque fonctionnalité
3. Préparer un discours de 2 minutes

### Déroulement de la démo (10 min)

**Minute 1-2: Introduction**
> "J'ai développé un assistant pédagogique IA qui génère des explications personnalisées de chapitres avec synthèse vocale."

**Minute 3-5: Démonstration niveau Débutant**
1. Montrer la page d'un chapitre
2. Cliquer sur "Assistant IA"
3. Sélectionner "Débutant"
4. Générer l'explication
5. Montrer le résumé, l'explication, les points clés

**Minute 6-7: Démonstration synthèse vocale**
1. Cliquer sur "Lire"
2. Laisser lire 10 secondes
3. Montrer Pause/Stop
4. Ajuster la vitesse

**Minute 8-9: Démonstration niveau Avancé**
1. Sélectionner "Avancé"
2. Générer l'explication
3. Comparer avec le niveau débutant

**Minute 10: Conclusion**
> "L'assistant utilise l'API Groq avec le modèle Llama 4 Scout. Il est accessible, adaptatif et améliore l'expérience d'apprentissage."

### Points à souligner
- ✅ Développé en 1 journée
- ✅ Utilise l'IA de pointe (Llama 4)
- ✅ Accessible (synthèse vocale)
- ✅ Adaptatif (2 niveaux)
- ✅ Interface moderne
- ✅ Prêt pour la production

## 📝 Notes de Test

### Test effectué le: ___________

**Chapitres testés:**
1. _______________________
2. _______________________
3. _______________________

**Résultats:**
- Génération IA: ⭐⭐⭐⭐⭐
- Synthèse vocale: ⭐⭐⭐⭐⭐
- Interface: ⭐⭐⭐⭐⭐
- Performance: ⭐⭐⭐⭐⭐

**Problèmes rencontrés:**
_________________________________
_________________________________

**Améliorations suggérées:**
_________________________________
_________________________________

## ✅ Validation finale

Avant de présenter:
- [ ] Tous les tests passent
- [ ] Aucune erreur dans les logs
- [ ] Interface impeccable
- [ ] Démo répétée 2 fois
- [ ] Documentation à jour
- [ ] Code commenté
- [ ] Prêt à répondre aux questions

## 🎉 Succès !

Si tous les tests passent, vous avez un assistant IA complet et fonctionnel !

**Temps total de test:** ~30 minutes
**Temps de démo:** ~10 minutes
**Impact:** Élevé
