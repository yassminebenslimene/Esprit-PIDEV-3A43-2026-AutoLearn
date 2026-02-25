# 🤖 Assistant IA - Séparation des Rôles

## ✅ Implémentation Complète

L'assistant IA est maintenant complètement séparé selon le rôle de l'utilisateur (Étudiant vs Admin).

---

## 👨‍🎓 Assistant pour ÉTUDIANTS

### Message de Bienvenue
```
Bonjour [Prénom]! 👋
Je suis votre assistant d'apprentissage. Je peux vous aider à:
📚 Trouver des cours adaptés à votre niveau
💪 Découvrir des exercices et challenges
📅 Voir les événements et workshops à venir
👥 Rejoindre des communautés et équipes
📊 Suivre vos progrès et obtenir des feedbacks
```

### Capacités

#### 1. 📚 Recommandations de Cours
- Recommander des cours selon le niveau (débutant, intermédiaire, avancé)
- Suggérer des cours par sujet (Python, Java, Web, etc.)
- Expliquer le contenu et les prérequis
- Montrer les chapitres et ressources

**Exemples de questions**:
- "Quels cours pour débuter en Python?"
- "Je veux apprendre le développement web"
- "Montre-moi les cours de niveau intermédiaire"

#### 2. 💪 Exercices & Challenges
- Suggérer des exercices selon le niveau
- Recommander des challenges pour améliorer les compétences
- Expliquer les concepts
- Fournir des conseils d'apprentissage

**Exemples de questions**:
- "Recommande-moi des exercices Python"
- "Quels challenges pour améliorer mes compétences?"
- "Je veux pratiquer JavaScript"

#### 3. 📅 Événements & Workshops
- Lister les événements à venir
- Afficher les détails (date, lieu, capacité)
- Aider à s'inscrire
- Suggérer des événements pertinents

**Exemples de questions**:
- "Montre-moi les événements à venir"
- "Quels workshops cette semaine?"
- "Je veux participer à un hackathon"

#### 4. 👥 Communautés & Équipes
- Lister les communautés disponibles
- Recommander selon les intérêts
- Aider à rejoindre des communautés
- Assister dans la création/rejoindre des équipes
- Montrer les membres et activités

**Exemples de questions**:
- "Quelles communautés puis-je rejoindre?"
- "Je veux rejoindre une communauté de développeurs web"
- "Comment créer une équipe pour l'événement?"
- "Montre-moi les communautés actives"

#### 5. 📊 Suivi des Progrès
- Afficher les progrès d'apprentissage
- Montrer les cours et exercices complétés
- Fournir des feedbacks personnalisés
- Suggérer les prochaines étapes

**Exemples de questions**:
- "Mes progrès d'apprentissage?"
- "Quels cours ai-je complétés?"
- "Que devrais-je apprendre ensuite?"

#### 6. 🔍 Recherche & Découverte
- Rechercher des cours, chapitres, ressources
- Trouver des communautés par sujet
- Découvrir de nouvelles opportunités

**Exemples de questions**:
- "Recherche des cours sur React"
- "Trouve-moi des ressources sur les algorithmes"
- "Quelles sont les nouvelles communautés?"

### Suggestions Prédéfinies
- "Quels cours pour débuter en Python?"
- "Montre-moi les événements à venir"
- "Recommande-moi des exercices"
- "Quelles communautés puis-je rejoindre?"
- "Mes progrès d'apprentissage?"

---

## 👨‍💼 Assistant pour ADMINS

### Message de Bienvenue
```
Bonjour [Prénom]! 👋
Je suis votre assistant administrateur. Je peux vous aider à:
👥 Gérer les étudiants (créer, modifier, rechercher)
📊 Voir les statistiques de la plateforme
🔍 Filtrer et rechercher des utilisateurs
📚 Gérer le contenu (cours, exercices, événements)
📈 Analyser les données et générer des rapports
```

### Capacités

#### 1. 👥 Gestion des Utilisateurs

**Créer un étudiant**:
```
"Crée un nouvel étudiant: nom Dupont, prénom Jean, email jean@test.com, niveau débutant"
```
Résultat: Étudiant créé avec confirmation des détails

**Modifier un étudiant**:
```
"Modifie l'étudiant ID 123, change son niveau à intermédiaire"
"Modifie l'email de l'étudiant jean@test.com en jean.dupont@test.com"
```
Résultat: Étudiant modifié avec confirmation

**Rechercher des étudiants**:
```
"Recherche l'étudiant avec email jean@test.com"
"Trouve les étudiants dont le nom contient 'Dupont'"
```
Résultat: Liste des étudiants trouvés avec détails

**Filtrer des étudiants**:
```
"Filtre les étudiants de niveau débutant"
"Montre-moi les étudiants inscrits ce mois"
"Liste les étudiants suspendus"
"Filtre les étudiants par niveau avancé, limite 10"
```
Résultat: Liste filtrée affichée dans le chat avec:
- ID, Nom, Prénom, Email
- Niveau, Date d'inscription
- Statut (actif/suspendu)
- Dernière connexion

**Suspendre/Réactiver**:
```
"Suspends l'utilisateur ID 123 pour inactivité"
"Réactive l'utilisateur ID 123"
```

#### 2. 📊 Statistiques & Analyses

**Statistiques générales**:
```
"Combien d'étudiants actifs?"
"Statistiques de la plateforme"
"Nombre total d'utilisateurs"
```

**Utilisateurs inactifs**:
```
"Montre-moi les utilisateurs inactifs depuis 7 jours"
"Liste les étudiants qui ne se sont pas connectés depuis 30 jours"
```

**Cours populaires**:
```
"Quels sont les cours les plus populaires?"
"Montre-moi les 5 cours les plus suivis"
```

#### 3. 📚 Gestion du Contenu
- Créer des cours, chapitres, ressources
- Gérer les exercices et challenges
- Organiser le contenu

#### 4. 📅 Gestion des Événements
- Créer des événements
- Gérer les inscriptions
- Voir les statistiques de participation

#### 5. 🔍 Recherche & Filtrage Avancés
- Filtres multiples combinés
- Recherche dans toutes les données
- Rapports personnalisés

### Actions Exécutables

L'admin peut demander à l'IA d'exécuter des actions directement:

| Action | Format JSON | Description |
|--------|-------------|-------------|
| create_student | `{"action": "create_student", "data": {"nom": "...", "prenom": "...", "email": "...", "niveau": "..."}}` | Créer un étudiant |
| update_student | `{"action": "update_student", "data": {"user_id": 123, "niveau": "INTERMEDIAIRE"}}` | Modifier un étudiant |
| filter_students | `{"action": "filter_students", "data": {"niveau": "DEBUTANT", "limit": 10}}` | Filtrer les étudiants |
| suspend_user | `{"action": "suspend_user", "data": {"user_id": 123, "reason": "..."}}` | Suspendre un utilisateur |
| unsuspend_user | `{"action": "unsuspend_user", "data": {"user_id": 123}}` | Réactiver un utilisateur |
| get_inactive_users | `{"action": "get_inactive_users", "data": {"days": 7}}` | Lister les inactifs |
| get_popular_courses | `{"action": "get_popular_courses", "data": {"limit": 5}}` | Cours populaires |

### Suggestions Prédéfinies
- "Combien d'étudiants actifs?"
- "Montre-moi les utilisateurs inactifs depuis 7 jours"
- "Crée un nouvel étudiant"
- "Filtre les étudiants de niveau débutant"
- "Statistiques de la plateforme?"

---

## 🎨 Format des Réponses

### Pour les Étudiants
- Ton encourageant et positif
- Emojis: 🎓 📚 💪 📅 👥 📊
- Listes à puces claires
- Liens cliquables vers les pages
- Suggestions de prochaines étapes

**Exemple de réponse**:
```
🎓 Super! Voici des cours Python pour débutants:

1. **Python Fondamentaux** (Débutant)
   - Durée: 4 semaines
   - 12 chapitres, 45 ressources
   - [Voir le cours](/cours/1)

2. **Python Pratique** (Débutant)
   - Durée: 6 semaines
   - 15 chapitres, 60 ressources
   - [Voir le cours](/cours/2)

💡 Je te recommande de commencer par "Python Fondamentaux"!

Tu veux t'inscrire à l'un de ces cours?
```

### Pour les Admins
- Ton professionnel et efficace
- Emojis: 👥 📊 📚 📅 🔍 ✅
- Tableaux ou listes structurées
- Détails complets (ID, statuts, dates)
- Confirmation des actions

**Exemple de réponse**:
```
👥 Étudiants de niveau DÉBUTANT: 15 étudiants

| ID  | Nom      | Prénom | Email              | Inscription | Dernière connexion |
|-----|----------|--------|--------------------| ------------|-------------------|
| 123 | Dupont   | Jean   | jean@test.com      | 01/02/2026  | 20/02/2026 14:30  |
| 124 | Martin   | Marie  | marie@test.com     | 05/02/2026  | 22/02/2026 10:15  |
| 125 | Bernard  | Paul   | paul@test.com      | 10/02/2026  | Jamais            |

✅ Filtres appliqués: niveau=DEBUTANT, limit=20

💡 Pour modifier un étudiant, demande-moi: "Modifie l'étudiant ID 123..."
```

---

## 🔧 Configuration Technique

### Prompts Système Séparés

**buildStudentPrompt()**: Prompt détaillé pour les étudiants
- Focus sur l'apprentissage et la découverte
- Recommandations personnalisées
- Encouragement et support

**buildAdminPrompt()**: Prompt détaillé pour les admins
- Focus sur la gestion et l'analyse
- Actions exécutables
- Statistiques et rapports

### Services Modifiés

**AIAssistantService.php**:
- `buildSystemPrompt()` - Sélectionne le bon prompt selon le rôle
- `buildStudentPrompt()` - Prompt pour étudiants
- `buildAdminPrompt()` - Prompt pour admins
- `getSuggestions()` - Suggestions selon le rôle

**ActionExecutorService.php**:
- `updateStudent()` - Nouvelle action pour modifier un étudiant
- `filterStudents()` - Nouvelle action pour filtrer les étudiants
- Permissions mises à jour

**Templates**:
- `chat_widget.html.twig` - Message de bienvenue selon le rôle

---

## 🧪 Tests

### Tester en tant qu'Étudiant
1. Connecte-toi avec un compte étudiant
2. Ouvre le chat widget
3. Vérifie le message de bienvenue (focus apprentissage)
4. Teste les questions:
   - "Quels cours pour débuter en Python?"
   - "Montre-moi les événements"
   - "Quelles communautés puis-je rejoindre?"

### Tester en tant qu'Admin
1. Connecte-toi avec un compte admin
2. Ouvre le chat widget
3. Vérifie le message de bienvenue (focus gestion)
4. Teste les actions:
   - "Combien d'étudiants actifs?"
   - "Filtre les étudiants de niveau débutant"
   - "Crée un étudiant: nom Test, prénom User, email test@test.com"
   - "Montre-moi les utilisateurs inactifs depuis 7 jours"

---

## ✅ Avantages

### Pour les Étudiants
- Interface adaptée à l'apprentissage
- Recommandations personnalisées
- Découverte facile du contenu
- Support et encouragement
- Navigation simplifiée

### Pour les Admins
- Gestion efficace des utilisateurs
- Actions directes depuis le chat
- Statistiques en temps réel
- Filtrage avancé
- Gain de temps considérable

---

## 🎯 Prochaines Améliorations Possibles

### Pour Étudiants
- Recommandations basées sur l'historique
- Notifications personnalisées
- Gamification (badges, points)
- Comparaison avec pairs
- Parcours d'apprentissage suggérés

### Pour Admins
- Export de données (CSV, Excel)
- Graphiques et visualisations
- Alertes automatiques
- Rapports programmés
- Intégration avec outils externes

---

**Créé**: 23 Février 2026  
**Statut**: ✅ Implémenté et Testé  
**Langues**: Français + Anglais  
**Rôles**: Étudiant + Admin
