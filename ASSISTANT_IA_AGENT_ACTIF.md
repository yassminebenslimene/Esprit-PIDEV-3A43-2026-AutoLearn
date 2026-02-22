# 🤖 Assistant IA - Agent Actif avec Actions

## 🎯 Objectif

Créer un assistant qui:
1. Répond différemment selon le rôle (Admin vs Étudiant)
2. Suggère des actions avec boutons cliquables
3. Peut exécuter des actions (créer, modifier, supprimer)
4. Plus rapide (modèle llama3.2:1b)

## 📋 Fonctionnalités par Rôle

### Pour ADMIN

#### Questions Supportées
- "Utilisateurs inactifs depuis 7 jours?"
- "Combien d'utilisateurs actifs?"
- "Statistiques de la plateforme?"
- "Cours les plus populaires?"
- "Créer un nouvel étudiant"
- "Créer une équipe"
- "Suspendre les utilisateurs inactifs"

#### Actions Disponibles
1. **Gestion Utilisateurs**
   - Créer étudiant → Ouvre formulaire
   - Voir utilisateurs inactifs → Liste avec actions
   - Suspendre automatiquement → Confirmation + exécution

2. **Gestion Équipes**
   - Créer équipe → Formulaire pré-rempli
   - Voir équipes → Liste avec détails
   - Modifier équipe → Formulaire édition

3. **Statistiques**
   - Voir stats détaillées → Dashboard
   - Exporter données → CSV/PDF
   - Analyser activités → Graphiques

### Pour ÉTUDIANT

#### Questions Supportées
- "Recommande-moi un cours"
- "Événements cette semaine?"
- "Mes progrès?"
- "Je veux rejoindre une équipe"
- "Créer une équipe"

#### Actions Disponibles
1. **Cours**
   - S'inscrire à un cours → Inscription directe
   - Voir détails cours → Page cours

2. **Équipes**
   - Créer équipe → Formulaire
   - Rejoindre équipe → Liste équipes disponibles
   - Voir mon équipe → Détails

3. **Événements**
   - S'inscrire événement → Inscription
   - Voir détails → Page événement

## 🏗️ Architecture

### 1. RAGService - Contexte par Rôle

```php
// Nouveau: getUserManagementContext() pour admin
return [
    'total_users' => 50,
    'active_users' => 45,
    'inactive_users_7days' => 5,
    'inactive_users_list' => [...], // Détails des inactifs
    'suspended_users' => 2,
    'actions_disponibles' => [
        'create_student' => '/backoffice/user/new',
        'view_inactive' => '/backoffice/users?filter=inactive',
        'suspend_inactive' => '/backoffice/users/suspend-inactive'
    ]
];
```

### 2. AIAssistantController - Nouvelles Routes

```php
// Route pour exécuter des actions
#[Route('/ai-assistant/action', methods: ['POST'])]
public function executeAction(Request $request): JsonResponse
{
    $action = $request->request->get('action');
    $params = $request->request->get('params');
    
    // Vérifier permissions
    // Exécuter action
    // Retourner résultat
}
```

### 3. Chat Widget - Boutons d'Action

```javascript
// Afficher boutons d'action dans la réponse
if (response.actions) {
    response.actions.forEach(action => {
        addActionButton(action.label, action.url, action.method);
    });
}
```

## 📊 Exemples de Conversations

### Admin: Utilisateurs Inactifs

```
👤 Admin: "Utilisateurs inactifs depuis 7 jours?"

🤖 IA: "5 utilisateurs inactifs depuis 7+ jours:
       • Jean Dupont (12 jours)
       • Marie Martin (9 jours)
       • ...
       
       [Voir la liste complète] [Suspendre automatiquement] [Envoyer rappel]"

👤 Admin: *Clique sur "Suspendre automatiquement"*

🤖 IA: "Confirmer la suspension de 5 utilisateurs?
       [Oui, suspendre] [Non, annuler]"

👤 Admin: *Clique sur "Oui, suspendre"*

🤖 IA: "✅ 5 utilisateurs suspendus avec succès. Emails de notification envoyés."
```

### Admin: Créer Étudiant

```
👤 Admin: "Créer un nouvel étudiant"

🤖 IA: "Je vais t'aider à créer un étudiant.
       
       [Formulaire rapide] [Formulaire complet]"

👤 Admin: *Clique sur "Formulaire rapide"*

🤖 IA: *Affiche formulaire inline*
       "Nom: [____]
        Prénom: [____]
        Email: [____]
        Niveau: [Débutant ▼]
        
        [Créer] [Annuler]"
```

### Étudiant: Rejoindre Équipe

```
👤 Étudiant: "Je veux rejoindre une équipe pour le hackaton"

🤖 IA: "3 équipes pour Hackaton IA peuvent encore recruter:
       
       • Team Alpha (5/6 membres) [Rejoindre]
       • Code Warriors (4/6 membres) [Rejoindre]
       • Dev Masters (5/6 membres) [Rejoindre]
       
       Ou [Créer ma propre équipe]"
```

### Étudiant: Créer Équipe

```
👤 Étudiant: "Créer une équipe"

🤖 IA: "Pour quel événement?
       
       • Hackaton IA (21/02) [Choisir]
       • Conférence IA (28/02) [Choisir]"

👤 Étudiant: *Clique sur "Hackaton IA"*

🤖 IA: *Affiche formulaire*
       "Nom de l'équipe: [____]
        
        Rappel: 4-6 membres requis
        
        [Créer l'équipe] [Annuler]"
```

## 🚀 Modèle Plus Rapide

### Changement de Modèle

```bash
# Télécharger modèle plus léger
ollama pull llama3.2:1b

# Modifier .env
OLLAMA_MODEL=llama3.2:1b
```

### Comparaison

| Modèle | Taille | Vitesse | Qualité |
|--------|--------|---------|---------|
| llama3.2:3b | 2 GB | 3-5s | Excellente |
| llama3.2:1b | 1 GB | 1-2s | Très bonne |

**Recommandation:** llama3.2:1b pour la vitesse

## 🔒 Sécurité

### Vérifications

1. **Permissions**
   ```php
   if ($action === 'create_student' && !$this->isGranted('ROLE_ADMIN')) {
       throw new AccessDeniedException();
   }
   ```

2. **Validation**
   ```php
   $validator->validate($data);
   ```

3. **Confirmation**
   - Actions critiques nécessitent confirmation
   - Afficher aperçu avant exécution

4. **Logs**
   - Toutes les actions sont loggées
   - Traçabilité complète

## 📝 Implémentation

### Phase 1: Contexte Admin (RAGService)
- Ajouter getUserManagementContext() détaillé
- Ajouter getCoursPopularityContext()
- Ajouter actions disponibles

### Phase 2: Actions (Controller)
- Route /ai-assistant/action
- Gestion permissions
- Exécution actions

### Phase 3: UI (Chat Widget)
- Boutons d'action
- Formulaires inline
- Confirmations

### Phase 4: Modèle Rapide
- Changer vers llama3.2:1b
- Optimiser prompts
- Tester vitesse

## ✅ Résultat Final

### Admin
- ✅ Stats en temps réel
- ✅ Actions rapides (créer, suspendre, etc.)
- ✅ Suggestions intelligentes
- ✅ Boutons cliquables

### Étudiant
- ✅ Recommandations personnalisées
- ✅ Actions rapides (s'inscrire, rejoindre)
- ✅ Création équipe facilitée
- ✅ Navigation intuitive

### Performance
- ⚡ 1-2 secondes (au lieu de 3-5)
- 🧠 Intelligence maintenue
- 🎯 Précision améliorée
- 💪 Actions exécutables

---

**Version:** 6.0.0
**Statut:** 📋 Spécification
**Prochaine étape:** Implémentation
