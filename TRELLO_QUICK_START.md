# 🚀 Trello Quick Start - Guide Rapide

## 📋 Configuration en 5 Minutes

### Étape 1: Créer le Board
1. Aller sur Trello.com
2. Créer nouveau board: "AutoLearn - Module Gestion Utilisateur"
3. Choisir couleur de fond (bleu recommandé)

### Étape 2: Créer les Listes (5 colonnes)
1. **📋 Product Backlog**
2. **🎯 Sprint 1 - CRUD**
3. **🎯 Sprint 2 - Bundles**
4. **🎯 Sprint 3 - IA**
5. **✅ Terminé**

### Étape 3: Créer les Étiquettes (10 essentielles)

| Nom | Couleur | Raccourci |
|-----|---------|-----------|
| Backend | Bleu | 1 |
| Frontend | Vert | 2 |
| Database | Violet | 3 |
| Config | Jaune | 4 |
| Documentation | Blanc | 5 |
| Sprint 1 | Bleu clair | 6 |
| Sprint 2 | Violet clair | 7 |
| Sprint 3 | Vert clair | 8 |
| Bug | Rouge | 9 |
| IA | Violet foncé | 0 |

### Étape 4: Créer les 20 Cartes

Ouvrir le fichier `CONTENU_CARTES_TRELLO.md` et pour chaque User Story:

1. Créer nouvelle carte
2. Copier-coller le titre
3. Copier-coller la description
4. Créer checklist avec les tâches
5. Ajouter les étiquettes
6. Placer dans la bonne liste

### Étape 5: Organiser

**Product Backlog**: Toutes les 20 cartes

**Sprint 1**: Déplacer ces cartes
- US-1.1 - Inscription
- US-1.2 - Connexion
- US-1.3 - Déconnexion
- US-1.4 - Consulter profil
- US-1.5 - Modifier informations
- US-1.6 - Rechercher étudiant
- US-1.7 - Profil détaillé
- US-1.8 - Désactiver compte

**Sprint 2**: Déplacer ces cartes
- US-1.9 - Réinitialisation mot de passe
- US-1.10 - Historique modifications
- US-1.11 - Suivi activité
- US-1.12 - Suspension automatique
- US-1.13 - Sidebar fixe

**Sprint 3**: Déplacer ces cartes
- US-1.14 - Assistant IA
- US-1.15 - IA avec contexte (RAG)
- US-1.16 - IA agent actif
- US-1.17 - Interface chat
- US-1.18 - Sécurité avancée
- US-1.19 - Documentation
- US-1.20 - Corrections merges

**Terminé**: Déplacer toutes les cartes (travail déjà fait!)

---

## 📊 Mapping Étiquettes par Carte

### Sprint 1
| Carte | Étiquettes |
|-------|-----------|
| US-1.1 | Backend, Database, Sprint 1 |
| US-1.2 | Backend, Frontend, Sprint 1 |
| US-1.3 | Backend, Frontend, Config, Sprint 1 |
| US-1.4 | Backend, Frontend, Sprint 1 |
| US-1.5 | Backend, Frontend, Sprint 1 |
| US-1.6 | Backend, Frontend, Sprint 1 |
| US-1.7 | Backend, Frontend, Sprint 1 |
| US-1.8 | Backend, Frontend, Sprint 1 |

### Sprint 2
| Carte | Étiquettes |
|-------|-----------|
| US-1.9 | Backend, Frontend, Config, Sprint 2 |
| US-1.10 | Backend, Database, Frontend, Sprint 2 |
| US-1.11 | Backend, Database, Frontend, Sprint 2 |
| US-1.12 | Backend, Config, Documentation, Sprint 2 |
| US-1.13 | Frontend, Sprint 2 |

### Sprint 3
| Carte | Étiquettes |
|-------|-----------|
| US-1.14 | Backend, IA, Config, Sprint 3 |
| US-1.15 | Backend, IA, Database, Sprint 3 |
| US-1.16 | Backend, IA, Database, Sprint 3 |
| US-1.17 | Backend, Frontend, IA, Sprint 3 |
| US-1.18 | Backend, Config, Sprint 3 |
| US-1.19 | Documentation, Sprint 3 |
| US-1.20 | Backend, Database, Frontend, Bug, Sprint 3 |

---

## 🎯 Checklist Complète

### Configuration Board
- [ ] Board créé: "AutoLearn - Module Gestion Utilisateur"
- [ ] 5 listes créées
- [ ] 10 étiquettes créées

### Cartes Sprint 1 (8 cartes)
- [ ] US-1.1 - Inscription
- [ ] US-1.2 - Connexion
- [ ] US-1.3 - Déconnexion
- [ ] US-1.4 - Consulter profil
- [ ] US-1.5 - Modifier informations
- [ ] US-1.6 - Rechercher étudiant
- [ ] US-1.7 - Profil détaillé
- [ ] US-1.8 - Désactiver compte

### Cartes Sprint 2 (5 cartes)
- [ ] US-1.9 - Réinitialisation mot de passe
- [ ] US-1.10 - Historique modifications
- [ ] US-1.11 - Suivi activité
- [ ] US-1.12 - Suspension automatique
- [ ] US-1.13 - Sidebar fixe

### Cartes Sprint 3 (7 cartes)
- [ ] US-1.14 - Assistant IA
- [ ] US-1.15 - IA avec contexte
- [ ] US-1.16 - IA agent actif
- [ ] US-1.17 - Interface chat
- [ ] US-1.18 - Sécurité avancée
- [ ] US-1.19 - Documentation
- [ ] US-1.20 - Corrections merges

### Finalisation
- [ ] Toutes les checklists cochées
- [ ] Toutes les cartes dans "Terminé"
- [ ] Dates ajoutées (optionnel)
- [ ] Screenshots ajoutés (optionnel)

---

## 💡 Astuces Trello

### Raccourcis Clavier
- `N` - Nouvelle carte
- `E` - Éditer carte
- `L` - Ajouter étiquette
- `M` - Ajouter membre
- `D` - Ajouter date
- `C` - Archiver carte
- `Space` - Assigner à moi

### Power-Ups Recommandés
1. **Calendar** - Vue calendrier des dates
2. **Card Aging** - Vieillissement visuel des cartes
3. **Custom Fields** - Champs personnalisés (Estimation, Points)
4. **Butler** - Automatisation (déplacer cartes automatiquement)

### Automatisations Butler

**Exemple 1**: Quand checklist complète → Déplacer vers "Terminé"
```
when all items in a checklist are complete, move the card to list "Terminé"
```

**Exemple 2**: Quand carte ajoutée à Sprint 1 → Ajouter étiquette Sprint 1
```
when a card is added to list "Sprint 1", add the "Sprint 1" label to the card
```

---

## 📱 Trello Mobile

L'application mobile Trello est disponible:
- iOS (App Store)
- Android (Google Play)

Tu peux gérer ton board depuis ton téléphone!

---

## 🔗 Liens Utiles

- **Trello**: https://trello.com
- **Guide Trello**: https://trello.com/guide
- **Raccourcis**: https://trello.com/shortcuts
- **Power-Ups**: https://trello.com/power-ups

---

## 📊 Statistiques Finales

Une fois terminé, ton board aura:
- ✅ 20 cartes (User Stories)
- ✅ 116 tâches (checklist items)
- ✅ 10 étiquettes
- ✅ 5 listes
- ✅ ~110h de travail documenté

---

**Temps estimé pour setup complet**: 30-45 minutes

**Fichiers à consulter**:
1. `CONTENU_CARTES_TRELLO.md` - Contenu détaillé des 20 cartes
2. `GUIDE_TRELLO_ETIQUETTES.md` - Guide complet étiquettes
3. `SPRINT_BACKLOG_REEL.xlsx` - Données source
4. `TRELLO_QUICK_START.md` - Ce guide

**Bonne chance!** 🚀
