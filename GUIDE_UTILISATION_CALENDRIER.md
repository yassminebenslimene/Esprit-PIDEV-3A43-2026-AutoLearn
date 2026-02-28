# 📅 Guide d'Utilisation du Calendrier des Événements

## 🎯 Pour les Étudiants

### Comment accéder au calendrier?

**Méthode 1: Depuis la page des événements**
1. Allez sur `/events`
2. Cliquez sur le bouton **"📅 View Calendar"** en haut de la page
3. Le calendrier s'affiche avec tous les événements

**Méthode 2: Accès direct**
- URL directe: `/events/calendar`

---

## 🖱️ Navigation dans le Calendrier

### Boutons de navigation:

#### 1. **Prev / Next** (Précédent / Suivant)
- Navigue entre les mois/semaines/jours selon la vue active
- Exemple: En vue Mois, passe au mois précédent/suivant

#### 2. **Today** (Aujourd'hui)
- Retourne à la date actuelle
- Utile après avoir navigué dans le futur/passé

#### 3. **Boutons de vue** (en haut à droite)
- **Mois**: Vue mensuelle (par défaut)
- **Semaine**: Vue hebdomadaire avec heures
- **Jour**: Vue journalière détaillée
- **Liste**: Liste chronologique des événements

---

## 🎨 Comprendre les Couleurs

### Types d'événements:
- 🟣 **Violet** (#667eea) = Workshop
- 🩷 **Rose** (#f093fb) = Conference
- 🔵 **Bleu** (#4facfe) = Hackathon
- 🟢 **Vert** (#43e97b) = Seminar
- 🔴 **Rouge** (#f5576c) = Meetup
- 🩵 **Cyan** (#38f9d7) = Training

### Statuts spéciaux:
- ⚪ **Gris** (#95a5a6) = Événement Annulé
- 🟢 **Vert pâle** (#7fb77e) = Événement Passé

**Astuce:** La légende des couleurs est affichée en haut du calendrier!

---

## 👆 Cliquer sur un Événement

### Que se passe-t-il?
Une **modal** (fenêtre popup) s'ouvre avec toutes les informations:

#### Informations affichées:
1. **🏷️ Type**: Workshop, Conference, Hackathon, etc.
2. **📍 Lieu**: Adresse de l'événement
3. **📅 Début**: Date et heure de début
4. **🏁 Fin**: Date et heure de fin
5. **📊 Statut**: Planifié, En cours, Passé, ou Annulé (avec badge coloré)
6. **👥 Participations**: Nombre d'équipes inscrites / Places totales
7. **📝 Description**: Aperçu de l'événement

#### Actions disponibles:
- **Bouton "Fermer"**: Ferme la modal
- **Bouton "Voir les détails"**: Redirige vers la page complète de l'événement

#### Comment fermer la modal?
- Cliquer sur le bouton "Fermer"
- Cliquer en dehors de la modal (sur le fond gris)
- Appuyer sur la touche **Escape** (Échap)

---

## 📱 Utilisation Mobile

Le calendrier est **responsive** et s'adapte aux petits écrans:
- Les boutons sont tactiles
- La modal s'affiche en plein écran sur mobile
- Navigation fluide avec le doigt

---

## 🔄 Retour à la Vue Liste

Pour revenir à la liste des événements:
- Cliquez sur le bouton **"📋 Vue Liste"** en haut à droite du calendrier
- Vous serez redirigé vers `/events`

---

## 💡 Cas d'Usage

### Scénario 1: Planifier sa semaine
1. Ouvrir le calendrier
2. Cliquer sur **"Semaine"**
3. Voir tous les événements de la semaine avec leurs horaires
4. Cliquer sur un événement pour voir les détails
5. Cliquer sur "Voir les détails" pour participer

### Scénario 2: Vérifier les événements du mois
1. Ouvrir le calendrier (vue Mois par défaut)
2. Identifier visuellement les types d'événements par couleur
3. Cliquer sur un événement pour voir s'il reste des places
4. Naviguer entre les mois avec Prev/Next

### Scénario 3: Trouver un événement spécifique
1. Ouvrir le calendrier
2. Cliquer sur **"Liste"**
3. Voir tous les événements dans l'ordre chronologique
4. Cliquer sur l'événement souhaité

---

## 🎓 Pour les Administrateurs

### Mise à jour automatique
- Les événements sont chargés **automatiquement** depuis la base de données
- Le statut est mis à jour en temps réel (Planifié → En cours → Passé)
- Aucune action manuelle nécessaire

### Performance
- Les événements sont chargés **par période** (optimisation)
- Seuls les événements visibles dans la vue actuelle sont chargés
- Chargement AJAX rapide

### Maintenance
- Aucune maintenance requise
- Le calendrier se synchronise automatiquement avec la base de données
- Les couleurs et statuts sont gérés par le CalendarSubscriber

---

## ❓ FAQ

### Q: Pourquoi certains événements sont gris?
**R:** Ce sont des événements **annulés**. Vous ne pouvez pas y participer.

### Q: Pourquoi certains événements sont vert pâle?
**R:** Ce sont des événements **passés**. Ils sont terminés.

### Q: Comment participer à un événement depuis le calendrier?
**R:** 
1. Cliquez sur l'événement
2. Dans la modal, cliquez sur "Voir les détails"
3. Sur la page de l'événement, cliquez sur "🎯 Participate in This Event"

### Q: Le calendrier se met-il à jour automatiquement?
**R:** Oui! Rafraîchissez la page pour voir les dernières modifications.

### Q: Puis-je voir les événements de l'année prochaine?
**R:** Oui! Utilisez les boutons Prev/Next pour naviguer dans le futur.

### Q: Comment savoir s'il reste des places?
**R:** Cliquez sur l'événement. La modal affiche "X / Y équipes" (places occupées / places totales).

---

## 🚀 Fonctionnalités Avancées

### Raccourcis clavier:
- **Escape**: Fermer la modal
- **Flèches gauche/droite**: Naviguer entre les périodes (selon le navigateur)

### Interactions:
- **Hover sur événement**: Effet de survol (légère élévation)
- **Clic sur événement**: Ouvre la modal
- **Clic sur fond de modal**: Ferme la modal

### Personnalisation:
- Le calendrier est en **français** (jours, mois, boutons)
- La semaine commence le **lundi**
- Format d'heure: **24h** (14:00 au lieu de 2:00 PM)

---

## 📊 Statistiques Visibles

Dans la modal, vous pouvez voir:
- **Taux de remplissage**: Combien d'équipes ont rejoint l'événement
- **Places restantes**: Calculé automatiquement (nbMax - nbParticipations)
- **Statut actuel**: Badge coloré selon le statut

---

## 🎉 Conclusion

Le calendrier des événements est un outil **puissant et intuitif** pour:
✅ Visualiser tous les événements d'un coup d'œil  
✅ Planifier sa participation  
✅ Identifier rapidement les types d'événements  
✅ Vérifier les places disponibles  
✅ Accéder rapidement aux détails  

**Profitez-en pour ne manquer aucun événement!** 🚀

---

**Besoin d'aide?** Contactez l'administrateur du système.
