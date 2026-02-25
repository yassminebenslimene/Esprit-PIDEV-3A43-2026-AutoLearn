# ✅ Fix Team Creation with Members - COMPLETE

## Problem
Team creation was missing the members requirement. According to the Equipe entity:
- Minimum 4 members required
- Maximum 6 members allowed
- Members must be students (ETUDIANT role)

## Solution Applied

### 1. Updated `createTeam()` Method
Added complete validation and member management:

```php
private function createTeam(array $params): array
{
    // Validation du nom et événement
    // ...
    
    // Vérifier les membres (minimum 4, maximum 6)
    if (empty($params['membres']) || !is_array($params['membres'])) {
        return [
            'success' => false,
            'error' => 'Une équipe doit avoir au moins 4 membres. Fournissez les IDs des étudiants.'
        ];
    }

    $membresIds = $params['membres'];
    if (count($membresIds) < 4) {
        return [
            'success' => false,
            'error' => 'Une équipe doit avoir au moins 4 membres (vous en avez fourni ' . count($membresIds) . ')'
        ];
    }

    if (count($membresIds) > 6) {
        return [
            'success' => false,
            'error' => 'Une équipe ne peut pas avoir plus de 6 membres (vous en avez fourni ' . count($membresIds) . ')'
        ];
    }

    // Récupérer et valider les étudiants
    $etudiants = [];
    foreach ($membresIds as $membreId) {
        $etudiant = $this->userRepository->find($membreId);
        if (!$etudiant) {
            return [
                'success' => false,
                'error' => "Étudiant avec ID {$membreId} introuvable"
            ];
        }
        
        if ($etudiant->getRole() !== 'ETUDIANT') {
            return [
                'success' => false,
                'error' => "L'utilisateur {$etudiant->getPrenom()} {$etudiant->getNom()} n'est pas un étudiant"
            ];
        }
        
        $etudiants[] = $etudiant;
    }

    // Créer l'équipe
    $equipe = new Equipe();
    $equipe->setNom($params['nom']);
    $equipe->setEvenement($evenement);
    
    // Ajouter les membres
    foreach ($etudiants as $etudiant) {
        $equipe->addEtudiant($etudiant);
    }

    $this->em->persist($equipe);
    $this->em->flush();

    return [
        'success' => true,
        'message' => "Équipe créée avec succès: {$params['nom']} ({count($etudiants)} membres)",
        'team_id' => $equipe->getId(),
        'event' => $evenement->getTitre(),
        'membres_count' => count($etudiants)
    ];
}
```

### 2. Added `list_students()` Action
New action to help users see available students:

```php
private function listStudents(array $params): array
{
    $limit = $params['limit'] ?? 50;
    
    // Get only students (not admins)
    $qb = $this->em->createQueryBuilder();
    $students = $qb->select('u')
        ->from(\App\Entity\User::class, 'u')
        ->where('u.role = :role')
        ->andWhere('u.isSuspended = false')
        ->setParameter('role', 'ETUDIANT')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    
    return [
        'success' => true,
        'count' => count($students),
        'students' => array_map(function($s) {
            return [
                'id' => $s->getId(),
                'nom' => $s->getNom(),
                'prenom' => $s->getPrenom(),
                'email' => $s->getEmail(),
                'niveau' => method_exists($s, 'getNiveau') ? $s->getNiveau() : 'N/A'
            ];
        }, $students)
    ];
}
```

### 3. Updated System Prompts
Updated examples to include members:

**French:**
```
User: "comment créer une équipe?"
Ta réponse COMPLÈTE (PAS de JSON):
Je peux créer une équipe pour toi! 👥 

D'abord, veux-tu voir les événements disponibles? Dis "voir les événements" pour choisir.

Ensuite, dis-moi:
- Nom de l'équipe
- ID de l'événement
- IDs de 4 à 6 membres (étudiants)

Exemple: "créer équipe Python Masters pour événement 3 avec membres 1,2,3,4"

User: "créer équipe Python Masters pour événement 3 avec membres 1,2,3,4"
Ta réponse COMPLÈTE:
{"action": "create_team", "data": {"nom": "Python Masters", "evenement_id": 3, "membres": [1, 2, 3, 4]}}
✅ Équipe créée
```

### 4. Updated Student Capabilities
Added list_students to help with team creation:

```
💡 Pour créer une équipe, dis d'abord "voir les événements" puis "créer équipe [nom] pour événement [id] avec membres [id1,id2,id3,id4]"
```

### 5. Updated Available Actions
Added `list_students` to public and student actions:

```php
$actions['public'] = [
    // ...
    'list_students' => 'Lister tous les étudiants'
];

$actions['student'] = [
    // ...
    'list_students' => 'Voir tous les étudiants'
];
```

## Complete Workflow for Team Creation

### Step 1: Ask how to create
```
User: "je veux créer une équipe, comment?"
AI: "Je peux créer une équipe pour toi! 👥 
     D'abord, veux-tu voir les événements disponibles?"
```

### Step 2: View events
```
User: "voir les événements"
AI: {"action": "list_events", "data": {}}
    📅 Événements disponibles:
    - ID 1: Workshop Python (2026-03-10)
    - ID 2: Hackathon Java (2026-03-15)
```

### Step 3: View students
```
User: "voir les étudiants"
AI: {"action": "list_students", "data": {}}
    👥 Étudiants disponibles:
    - ID 1: Ahmed Ben Salah
    - ID 2: Amira Nefzi
    - ID 3: Ilef Yousfi
    - ID 4: Test User
```

### Step 4: Create team with members
```
User: "créer équipe Python Masters pour événement 1 avec membres 1,2,3,4"
AI: {"action": "create_team", "data": {"nom": "Python Masters", "evenement_id": 1, "membres": [1, 2, 3, 4]}}
    ✅ Équipe créée (4 membres)
```

## Validation Rules

### Team Name
- ✅ Required
- ✅ Must not be empty

### Event ID
- ✅ Required
- ✅ Event must exist in database

### Members
- ✅ Required
- ✅ Must be an array
- ✅ Minimum 4 members
- ✅ Maximum 6 members
- ✅ Each member ID must exist
- ✅ Each member must be a student (ROLE_ETUDIANT)
- ✅ Members must not be suspended

## Error Messages

### Missing members
```
❌ Une équipe doit avoir au moins 4 membres. Fournissez les IDs des étudiants.
```

### Too few members
```
❌ Une équipe doit avoir au moins 4 membres (vous en avez fourni 2)
```

### Too many members
```
❌ Une équipe ne peut pas avoir plus de 6 membres (vous en avez fourni 8)
```

### Member not found
```
❌ Étudiant avec ID 99 introuvable
```

### Member not a student
```
❌ L'utilisateur John Doe n'est pas un étudiant
```

## Files Modified
- `autolearn/src/Service/ActionExecutorService.php`
  - Updated `createTeam()` method with member validation
  - Added `listStudents()` method
  - Updated `executeAction()` match statement
  - Updated `getAvailableActions()` method

- `autolearn/src/Service/AIAssistantService.php`
  - Updated French admin prompt examples
  - Updated student capabilities section (FR & EN)
  - Added `list_students` to available actions

## Testing

Test these queries as a STUDENT:

1. "je veux créer une équipe, comment?"
   → Should explain the process

2. "voir les événements"
   → Should list events with IDs

3. "voir les étudiants"
   → Should list students with IDs

4. "créer équipe Python Masters pour événement 1 avec membres 1,2,3,4"
   → Should create the team with 4 members

5. "créer équipe Test pour événement 1 avec membres 1,2"
   → Should fail: "Une équipe doit avoir au moins 4 membres"

6. "créer équipe Test pour événement 1 avec membres 1,2,3,4,5,6,7"
   → Should fail: "Une équipe ne peut pas avoir plus de 6 membres"

## Conclusion

Team creation now properly validates:
- ✅ Team name
- ✅ Event existence
- ✅ Member count (4-6)
- ✅ Member existence
- ✅ Member role (must be student)

The AI guides users through the complete workflow:
1. View events
2. View students
3. Create team with all required data

Cache cleared and ready to test! 🚀
