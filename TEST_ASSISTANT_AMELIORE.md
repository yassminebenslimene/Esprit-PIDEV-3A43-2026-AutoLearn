# 🧪 Test de l'Assistant IA Amélioré

## ⚡ TESTS RAPIDES

### Test 1: Disponibilité Frontend (Étudiant)
1. Se connecter en tant qu'étudiant
2. Aller sur la page d'accueil `/`
3. ✅ Le bouton violet doit être visible en bas à droite
4. Cliquer dessus
5. ✅ Le chat s'ouvre avec animation
6. ✅ Message de bienvenue pour étudiant visible
7. ✅ Suggestions chargées

### Test 2: Disponibilité Backoffice (Admin)
1. Se connecter en tant qu'admin
2. Aller sur le dashboard `/backoffice`
3. ✅ Le bouton violet doit être visible en bas à droite
4. Cliquer dessus
5. ✅ Le chat s'ouvre avec animation
6. ✅ Message de bienvenue pour admin visible
7. ✅ Suggestions chargées

### Test 3: Exclusion Pages Quiz
1. Se connecter (étudiant ou admin)
2. Aller sur une page de quiz (ex: `/quiz/1`)
3. ✅ Le bouton violet NE DOIT PAS être visible
4. Aller sur une autre page
5. ✅ Le bouton réapparaît

### Test 4: Envoi de Message
1. Ouvrir le chat
2. Taper "Bonjour"
3. ✅ Le compteur affiche "7/500"
4. ✅ Le bouton d'envoi est actif (pas grisé)
5. Appuyer sur Enter
6. ✅ Message utilisateur affiché à droite (violet)
7. ✅ Indicateur de frappe (3 points) visible
8. ✅ Réponse de l'IA affichée à gauche (blanc)

### Test 5: Fonctionnalités UX
1. Ouvrir le chat
2. ✅ Textarea vide → bouton d'envoi grisé
3. Taper du texte
4. ✅ Textarea s'agrandit automatiquement
5. ✅ Compteur de caractères mis à jour
6. ✅ Bouton d'envoi devient actif
7. Appuyer sur Shift+Enter
8. ✅ Nouvelle ligne créée (pas d'envoi)
9. Appuyer sur Enter
10. ✅ Message envoyé

### Test 6: Responsive Mobile
1. Ouvrir le navigateur en mode mobile (F12 → Toggle device)
2. ✅ Bouton: 56x56px
3. ✅ Chat: largeur = 100vw - 32px
4. ✅ Chat: hauteur = 100vh - 100px
5. ✅ Messages: max-width 85%
6. ✅ Tout fonctionne correctement

### Test 7: Suggestions
1. Ouvrir le chat
2. ✅ Suggestions affichées sous le message de bienvenue
3. Cliquer sur une suggestion
4. ✅ Suggestion copiée dans le textarea
5. ✅ Message envoyé automatiquement

### Test 8: Scrollbar Personnalisée
1. Ouvrir le chat
2. Envoyer plusieurs messages (10+)
3. ✅ Scrollbar personnalisée visible (6px, gris)
4. Hover sur la scrollbar
5. ✅ Couleur change au hover

### Test 9: Indicateur de Statut
1. Ouvrir le chat
2. ✅ "En ligne" avec point vert visible dans le header
3. ✅ Point vert pulse (animation)

### Test 10: Gestion des Erreurs
1. Ouvrir le chat
2. Désactiver la connexion internet
3. Envoyer un message
4. ✅ Message d'erreur affiché avec emoji ❌
5. ✅ "Erreur de connexion. Vérifiez votre connexion internet."

## 🎯 TESTS DÉTAILLÉS

### Test A: Navigation Entre Pages
**Objectif:** Vérifier que le widget reste disponible

1. Page d'accueil → ✅ Widget visible
2. Page cours → ✅ Widget visible
3. Page événements → ✅ Widget visible
4. Page communauté → ✅ Widget visible
5. Page profil → ✅ Widget visible
6. Page quiz → ❌ Widget caché
7. Retour page d'accueil → ✅ Widget visible

### Test B: Différents Rôles
**Objectif:** Vérifier les messages personnalisés

**Étudiant:**
- ✅ "Je suis votre assistant d'apprentissage"
- ✅ Suggestions: cours, exercices, événements, communautés, progrès

**Admin:**
- ✅ "Je suis votre assistant administrateur"
- ✅ Suggestions: étudiants, statistiques, filtrage, contenu, rapports

### Test C: Animations
**Objectif:** Vérifier la fluidité

1. Clic sur bouton flottant
   - ✅ Rotation icône (chat → X)
   - ✅ Chat slide up + scale
   - ✅ Durée: 0.4s

2. Hover bouton flottant
   - ✅ Scale 1.1
   - ✅ Shadow augmente

3. Nouveau message
   - ✅ Fade in + translateY
   - ✅ Durée: 0.3s

4. Indicateur de frappe
   - ✅ 3 points qui bougent
   - ✅ Animation infinie

### Test D: Accessibilité
**Objectif:** Vérifier les attributs ARIA

1. Inspecter le bouton flottant
   - ✅ `aria-label="Ouvrir l'assistant IA"`
   - ✅ `title="Assistant IA"`

2. Inspecter le bouton fermer
   - ✅ `aria-label="Fermer le chat"`
   - ✅ `title="Fermer"`

3. Inspecter le textarea
   - ✅ `aria-label="Message pour l'assistant"`

4. Inspecter le bouton d'envoi
   - ✅ `aria-label="Envoyer le message"`
   - ✅ `title="Envoyer"`

### Test E: Performance
**Objectif:** Vérifier la rapidité

1. Ouvrir le chat
   - ✅ Ouverture instantanée (<100ms)

2. Charger les suggestions
   - ✅ Chargement rapide (<500ms)

3. Envoyer un message
   - ✅ Affichage immédiat du message utilisateur
   - ✅ Réponse IA en ~1-2 secondes

4. Scroll dans les messages
   - ✅ Scroll smooth et fluide

## 📊 CHECKLIST COMPLÈTE

### Disponibilité
- [ ] Widget visible sur page d'accueil frontend
- [ ] Widget visible sur page cours
- [ ] Widget visible sur page événements
- [ ] Widget visible sur page communauté
- [ ] Widget visible sur page profil
- [ ] Widget visible sur dashboard backoffice
- [ ] Widget visible sur page utilisateurs backoffice
- [ ] Widget visible sur page analytics backoffice
- [ ] Widget CACHÉ sur pages de quiz
- [ ] Widget CACHÉ quand utilisateur non connecté

### Design
- [ ] Bouton flottant: 60x60px (desktop)
- [ ] Bouton flottant: 56x56px (mobile)
- [ ] Gradient violet/bleu correct
- [ ] Animation de pulsation visible
- [ ] Chat: 400px largeur (desktop)
- [ ] Chat: responsive sur mobile
- [ ] Messages bot: fond blanc, texte gris foncé
- [ ] Messages user: gradient violet, texte blanc
- [ ] Scrollbar personnalisée visible

### Fonctionnalités
- [ ] Bouton d'envoi désactivé si textarea vide
- [ ] Compteur de caractères fonctionne
- [ ] Auto-resize du textarea
- [ ] Enter envoie le message
- [ ] Shift+Enter crée nouvelle ligne
- [ ] Suggestions cliquables
- [ ] Indicateur de frappe visible
- [ ] Scroll automatique vers dernier message
- [ ] Fermeture du chat fonctionne

### Messages
- [ ] Message de bienvenue étudiant correct
- [ ] Message de bienvenue admin correct
- [ ] Suggestions étudiant correctes
- [ ] Suggestions admin correctes
- [ ] Messages d'erreur clairs
- [ ] Formatage HTML fonctionne (liens, listes)

### Accessibilité
- [ ] Tous les boutons ont aria-label
- [ ] Tous les boutons ont title
- [ ] Textarea a aria-label
- [ ] Contraste de couleurs suffisant
- [ ] Navigation clavier fonctionne

### Performance
- [ ] Ouverture chat < 100ms
- [ ] Chargement suggestions < 500ms
- [ ] Envoi message instantané
- [ ] Réponse IA < 2 secondes
- [ ] Animations fluides (60fps)
- [ ] Pas de lag au scroll

## 🐛 PROBLÈMES CONNUS

### Problème 1: Widget ne s'affiche pas
**Solution:**
1. Vérifier que l'utilisateur est connecté
2. Vérifier que ce n'est pas une page de quiz
3. Vider le cache: `php bin/console cache:clear`

### Problème 2: Suggestions ne se chargent pas
**Solution:**
1. Vérifier la route `/ai-assistant/suggestions`
2. Vérifier les logs: `var/log/dev.log`
3. Vérifier la connexion réseau

### Problème 3: Messages ne s'envoient pas
**Solution:**
1. Vérifier la route `/ai-assistant/ask`
2. Vérifier la configuration Groq dans `.env`
3. Vérifier les logs d'erreur

### Problème 4: Design cassé
**Solution:**
1. Vérifier que les styles CSS sont chargés
2. Vérifier la console pour les erreurs
3. Vider le cache navigateur (Ctrl+Shift+R)

## ✅ VALIDATION FINALE

Après tous les tests, vous devriez avoir:
- ✅ Widget disponible sur TOUTES les pages (sauf quiz)
- ✅ Design moderne et responsive
- ✅ Toutes les fonctionnalités opérationnelles
- ✅ Aucune erreur dans la console
- ✅ Performance optimale
- ✅ Accessibilité complète

## 🎉 RÉSULTAT

Si tous les tests passent, l'assistant IA est:
- **Fonctionnel** sur toute la plateforme
- **Optimisé** pour la performance
- **Accessible** à tous les utilisateurs
- **Moderne** et agréable à utiliser
- **Intelligent** et utile

**L'assistant est prêt pour la production! 🚀**
