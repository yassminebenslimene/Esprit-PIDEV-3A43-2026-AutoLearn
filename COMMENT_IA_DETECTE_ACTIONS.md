# 🤖 Comment l'IA Détecte et Exécute les Actions

## 🎯 Vue d'Ensemble

L'assistant IA peut maintenant comprendre quand vous lui demandez d'effectuer une action et l'exécuter automatiquement.

## 🔄 Flux Complet

```
1. Utilisateur: "Crée-moi un nouvel étudiant Jean Dupont"
   ↓
2. RAGService collecte le contexte (rôle: ADMIN, actions disponibles)
   ↓
3. OllamaService reçoit le prompt système avec:
   - Contexte utilisateur (nom, rôle, niveau)
   - Données de la BD (cours, événements, stats)
   - Liste des actions disponibles
   - Exemples de détection d'actions
   ↓
4. IA analyse la demande et détecte: "créer un étudiant"
   ↓
5. IA génère: "Je peux créer cet étudiant. ACTION:create_student|nom:Dupont|prenom:Jean|email:jean.dupont@autolearn.com|niveau:DEBUTANT"
   ↓
6. AIAssistantService détecte le pattern "ACTION:"
   ↓
7. Parse les paramètres: {nom: "Dupont", prenom: "Jean", email: "jean.dupont@autolearn.com"}
   ↓
8. ActionExecutorService vérifie les permissions (ADMIN? ✅)
   ↓
9. Exécute l'action: crée l'étudiant dans la BD
   ↓
10. Retourne le résultat: "✅ Étudiant créé avec succès: Jean Dupont..."
```

## 📝 Format des Actions

### Structure
```
ACTION:nom_action|param1:valeur1|param2:valeur2|param3:valeur3
```

### Exemples Réels

**Créer un étudiant:**
```
ACTION:create_student|nom:Dupont|prenom:Jean|email:jean@example.com|niveau:DEBUTANT
```

**Créer une équipe:**
```
ACTION:create_team|nom:Team Alpha|evenement_id:1
```

**Suspendre un utilisateur:**
```
ACTION:suspend_user|user_id:5|reason:Inactivité prolongée
```

**Lister les inactifs:**
```
ACTION:get_inactive_users|days:7
```

## 🧠 Comment l'IA Apprend à Détecter

### Dans le Prompt Système

Le prompt système contient des exemples explicites:

```
ACTIONS DISPONIBLES (ADMIN UNIQUEMENT):
Si l'utilisateur est ADMIN et demande une action, tu peux proposer:
- Créer un étudiant: "ACTION:create_student|nom:Dupont|prenom:Jean|email:jean@example.com|niveau:DEBUTANT"
- Créer une équipe: "ACTION:create_team|nom:Team Alpha|evenement_id:1"
- Suspendre un utilisateur: "ACTION:suspend_user|user_id:5|reason:Inactivité"
- Réactiver un utilisateur: "ACTION:unsuspend_user|user_id:5"
- Lister utilisateurs inactifs: "ACTION:get_inactive_users|days:7"

DÉTECTION D'ACTIONS:
Si l'utilisateur dit:
- "Crée-moi un étudiant" → Propose ACTION:create_student
- "Crée une équipe" → Propose ACTION:create_team
- "Suspends cet utilisateur" → Propose ACTION:suspend_user
- "Utilisateurs inactifs?" → Propose ACTION:get_inactive_users
```

### Exemples dans le Prompt

```
Question (ADMIN): "Crée-moi un nouvel étudiant Jean Dupont"
✅ BON: "Je peux créer cet étudiant. ACTION:create_student|nom:Dupont|prenom:Jean|email:jean.dupont@autolearn.com|niveau:DEBUTANT"
❌ MAUVAIS: "Je ne peux pas créer d'étudiant"

Question (ADMIN): "Utilisateurs inactifs depuis 7 jours?"
✅ BON: "ACTION:get_inactive_users|days:7"
❌ MAUVAIS: "Je n'ai pas cette information"
```

## 🔍 Détection dans le Code

### AIAssistantService.php

```php
private function detectAndExecuteAction(string $response, array $context): ?string
{
    // Chercher le pattern ACTION:action_name|param1:value1|param2:value2
    if (!preg_match('/ACTION:([a-z_]+)(\|[^|]+)*/', $response, $matches)) {
        return null; // Pas d'action détectée
    }

    $actionString = $matches[0]; // Ex: "ACTION:create_student|nom:Dupont|prenom:Jean"
    $parts = explode('|', $actionString);
    $actionName = str_replace('ACTION:', '', $parts[0]); // "create_student"
    
    // Parser les paramètres
    $params = [];
    for ($i = 1; $i < count($parts); $i++) {
        if (strpos($parts[$i], ':') !== false) {
            list($key, $value) = explode(':', $parts[$i], 2);
            $params[$key] = $value; // ["nom" => "Dupont", "prenom" => "Jean", ...]
        }
    }

    // Exécuter l'action
    $result = $this->actionExecutor->executeAction($actionName, $params, $user);
    
    // Remplacer ACTION:... par le résultat
    if ($result['success']) {
        return str_replace($actionString, "✅ " . $result['message'], $response);
    } else {
        return str_replace($actionString, "❌ " . $result['error'], $response);
    }
}
```

## 🎭 Exemples de Conversations

### Exemple 1: Créer un Étudiant

**Utilisateur (Admin):**
```
Crée-moi un nouvel étudiant Jean Dupont avec l'email jean.dupont@autolearn.com
```

**IA génère (interne):**
```
Je peux créer cet étudiant. ACTION:create_student|nom:Dupont|prenom:Jean|email:jean.dupont@autolearn.com|niveau:DEBUTANT
```

**Détection:**
- Pattern trouvé: `ACTION:create_student|...`
- Action: `create_student`
- Params: `{nom: "Dupont", prenom: "Jean", email: "jean.dupont@autolearn.com", niveau: "DEBUTANT"}`

**Exécution:**
```php
$actionExecutor->executeAction('create_student', [
    'nom' => 'Dupont',
    'prenom' => 'Jean',
    'email' => 'jean.dupont@autolearn.com',
    'niveau' => 'DEBUTANT'
], $user);
```

**Résultat affiché:**
```
✅ Étudiant créé avec succès: Jean Dupont
📋 ID: 42
📧 Email: jean.dupont@autolearn.com
🔑 Mot de passe par défaut: AutoLearn2026!
```

### Exemple 2: Utilisateurs Inactifs

**Utilisateur (Admin):**
```
Utilisateurs inactifs depuis 7 jours?
```

**IA génère (interne):**
```
ACTION:get_inactive_users|days:7
```

**Détection:**
- Pattern trouvé: `ACTION:get_inactive_users|days:7`
- Action: `get_inactive_users`
- Params: `{days: "7"}`

**Exécution:**
```php
$actionExecutor->executeAction('get_inactive_users', [
    'days' => '7'
], $user);
```

**Résultat affiché:**
```
✅ 5 utilisateur(s) inactif(s) trouvé(s):
• Marie Martin (marie@example.com) - Dernière connexion: 10/02/2026
• Paul Durand (paul@example.com) - Dernière connexion: 12/02/2026
...
```

### Exemple 3: Créer une Équipe

**Utilisateur (Admin):**
```
Crée une équipe Team Alpha pour l'événement Hackaton IA
```

**IA génère (interne):**
```
Je crée l'équipe. ACTION:create_team|nom:Team Alpha|evenement_id:1
```

**Détection:**
- Pattern trouvé: `ACTION:create_team|nom:Team Alpha|evenement_id:1`
- Action: `create_team`
- Params: `{nom: "Team Alpha", evenement_id: "1"}`

**Exécution:**
```php
$actionExecutor->executeAction('create_team', [
    'nom' => 'Team Alpha',
    'evenement_id' => '1'
], $user);
```

**Résultat affiché:**
```
✅ Équipe créée avec succès: Team Alpha
📋 ID: 15
🎯 Événement: Hackaton IA 2026
```

## 🔒 Vérification des Permissions

### Dans ActionExecutorService.php

```php
private function hasPermission(User $user, string $action): bool
{
    $adminActions = [
        'create_student',
        'create_team',
        'suspend_user',
        'unsuspend_user',
        'get_inactive_users'
    ];

    // Actions admin uniquement
    if (in_array($action, $adminActions)) {
        return $user->getRole() === 'ADMIN';
    }

    // Actions publiques
    return true;
}
```

### Exemple de Refus

**Utilisateur (Étudiant):**
```
Crée-moi un nouvel étudiant Test User
```

**IA génère (interne):**
```
ACTION:create_student|nom:User|prenom:Test|email:test@autolearn.com
```

**Vérification des permissions:**
```php
if (!$this->hasPermission($user, 'create_student')) {
    return [
        'success' => false,
        'error' => 'Permission refusée. Action réservée aux administrateurs.'
    ];
}
```

**Résultat affiché:**
```
❌ Permission refusée. Action réservée aux administrateurs.
```

## 🎯 Pourquoi Ça Marche

### 1. Prompt Système Détaillé
Le prompt contient des exemples explicites de détection et de format d'actions.

### 2. Pattern Clair
Le format `ACTION:nom|param:valeur` est simple et facile à détecter avec regex.

### 3. Contexte Enrichi
L'IA reçoit le rôle de l'utilisateur et sait quelles actions sont disponibles.

### 4. Exemples Concrets
Le prompt contient des exemples de bonnes et mauvaises réponses.

### 5. Validation Robuste
Toutes les actions sont validées (permissions, paramètres, existence).

## 🚀 Ajouter de Nouvelles Actions

### Étape 1: Ajouter l'Action dans ActionExecutorService

```php
public function executeAction(string $action, array $params, User $requestingUser): array
{
    return match($action) {
        'create_student' => $this->createStudent($params),
        'create_team' => $this->createTeam($params),
        'nouvelle_action' => $this->nouvelleAction($params), // NOUVEAU
        default => [
            'success' => false,
            'error' => "Action inconnue: {$action}"
        ]
    };
}

private function nouvelleAction(array $params): array
{
    // Implémenter la logique
    return [
        'success' => true,
        'message' => 'Action exécutée avec succès'
    ];
}
```

### Étape 2: Ajouter dans le Prompt Système (OllamaService)

```php
ACTIONS DISPONIBLES (ADMIN UNIQUEMENT):
- Nouvelle action: "ACTION:nouvelle_action|param1:valeur1"

DÉTECTION D'ACTIONS:
- "Fais la nouvelle action" → Propose ACTION:nouvelle_action
```

### Étape 3: Ajouter dans getAvailableActions

```php
if ($user->getRole() === 'ADMIN') {
    $actions['admin'] = [
        'create_student' => 'Créer un nouvel étudiant',
        'nouvelle_action' => 'Description de la nouvelle action', // NOUVEAU
    ];
}
```

### Étape 4: Tester

```
Utilisateur: "Fais la nouvelle action"
IA: "ACTION:nouvelle_action|param1:valeur1"
Résultat: "✅ Action exécutée avec succès"
```

## 🎉 Conclusion

Le système de détection d'actions est:
- ✅ Simple (format clair)
- ✅ Robuste (validation complète)
- ✅ Extensible (facile d'ajouter des actions)
- ✅ Sécurisé (vérification des permissions)
- ✅ Intelligent (comprend le langage naturel)

L'IA apprend à détecter les actions grâce aux exemples dans le prompt système, et le code se charge de l'exécution et de la sécurité.

---

**Pour plus d'infos:** Consultez `IA_AGENT_ACTIF_COMPLET.md`
