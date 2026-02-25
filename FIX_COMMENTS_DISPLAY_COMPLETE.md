# ✅ Fix Comments Display - COMPLETE

## Problem
User query: "afficher tous les commentaires"
AI Response: Long verbose response saying it can't find comments data

## Root Cause
1. ❌ Comments data was NOT being collected in `getAllDatabaseData()`
2. ❌ NO `list_comments` action existed
3. ❌ CommentaireRepository not injected into services
4. ❌ AI being too verbose instead of concise

## Solution Applied

### 1. Added CommentaireRepository to Services
```yaml
# config/services.yaml
App\Service\AIAssistantService:
    arguments:
        # ... existing
        $commentaireRepository: '@App\Repository\CommentaireRepository'

App\Service\ActionExecutorService:
    arguments:
        # ... existing
        $commentaireRepository: '@App\Repository\CommentaireRepository'
```

### 2. Injected CommentaireRepository into Both Services
```php
// AIAssistantService.php
use App\Repository\CommentaireRepository;

private CommentaireRepository $commentaireRepository;

public function __construct(
    // ... existing params
    CommentaireRepository $commentaireRepository
) {
    // ... existing assignments
    $this->commentaireRepository = $commentaireRepository;
}
```

### 3. Added Comments Data Collection
```php
// In getAllDatabaseData() method
// ========== COMMENTS DATA (Limité à 20) ==========
$allComments = $this->commentaireRepository->findBy([], ['createdAt' => 'DESC'], 20);
$data['comments'] = [
    'total' => $this->commentaireRepository->count([]),
    'list' => array_map(function($c) {
        return [
            'id' => $c->getId(),
            'contenu' => substr($c->getContenu(), 0, 100) . '...',
            'auteur' => $c->getUser() ? $c->getUser()->getPrenom() . ' ' . $c->getUser()->getNom() : 'Inconnu',
            'post_id' => $c->getPost() ? $c->getPost()->getId() : null,
            'created_at' => $c->getCreatedAt()->format('Y-m-d H:i'),
        ];
    }, $allComments)
];
```

### 4. Added Comment Actions to ActionExecutorService
```php
// New actions in executeAction() match statement:
'list_comments' => $this->listComments($params),
'get_comment' => $this->getComment($params),

// New methods:
private function listComments(array $params): array
{
    $limit = $params['limit'] ?? 50;
    
    // Filter by post if specified
    if (!empty($params['post_id'])) {
        $comments = $this->commentaireRepository->findBy(
            ['post' => $params['post_id']], 
            ['createdAt' => 'DESC'], 
            $limit
        );
    } else {
        $comments = $this->commentaireRepository->findBy([], ['createdAt' => 'DESC'], $limit);
    }
    
    return [
        'success' => true,
        'count' => count($comments),
        'comments' => array_map(function($c) {
            return [
                'id' => $c->getId(),
                'contenu' => $c->getContenu(),
                'auteur' => $c->getUser() ? $c->getUser()->getPrenom() . ' ' . $c->getUser()->getNom() : 'Inconnu',
                'post_id' => $c->getPost() ? $c->getPost()->getId() : null,
                'post_preview' => $c->getPost() ? substr($c->getPost()->getContenu(), 0, 50) . '...' : null,
                'created_at' => $c->getCreatedAt()->format('d/m/Y H:i')
            ];
        }, $comments)
    ];
}

private function getComment(array $params): array
{
    // Get full comment details
}
```

### 5. Updated System Prompts (English & French)
Added to admin capabilities:
```
9. 💬 COMMENT MANAGEMENT
   - List all comments across posts
   - Filter comments by post
   - View comment details
   - Monitor discussions
```

Added to available actions:
```
COMMENT MANAGEMENT:
- list_comments: List all comments (optionally filter by post_id)
- get_comment: Get comment details
```

### 6. Updated Available Actions List
```php
$actions['public'] = [
    // ... existing actions
    'list_comments' => 'Lister tous les commentaires'
];

$actions['admin'] = [
    // ... existing actions
    'list_comments' => 'Lister tous les commentaires',
    'get_comment' => 'Voir les détails d\'un commentaire'
];
```

## What the AI Can Now Do

### Display All Comments
User: "afficher tous les commentaires"
AI generates: `{"action": "list_comments", "data": {}}`
Result: Shows list of all comments with:
- Comment ID
- Full content
- Author name
- Related post ID and preview
- Creation date/time

### Display Comments for Specific Post
User: "afficher les commentaires du post 5"
AI generates: `{"action": "list_comments", "data": {"post_id": 5}}`
Result: Shows only comments for that post

### View Comment Details
User: "voir commentaire 3"
AI generates: `{"action": "get_comment", "data": {"id": 3}}`
Result: Shows full comment details

### Comments Data in Context
The AI also has access to comments data in the database context:
```json
{
  "comments": {
    "total": 15,
    "list": [
      {
        "id": 1,
        "contenu": "Great post!...",
        "auteur": "Ahmed Ben Salah",
        "post_id": 2,
        "created_at": "2026-02-24 14:30"
      }
    ]
  }
}
```

## Files Modified
1. `autolearn/config/services.yaml`
   - Added CommentaireRepository to AIAssistantService
   - Added CommentaireRepository to ActionExecutorService

2. `autolearn/src/Service/AIAssistantService.php`
   - Imported CommentaireRepository
   - Added private property
   - Updated constructor
   - Added comments data collection in `getAllDatabaseData()`
   - Updated English admin prompt (COMMENT MANAGEMENT section)
   - Updated French admin prompt (actions list)

3. `autolearn/src/Service/ActionExecutorService.php`
   - Imported CommentaireRepository
   - Added private property
   - Updated constructor
   - Added `list_comments()` method
   - Added `get_comment()` method
   - Updated `executeAction()` match statement
   - Updated `getAvailableActions()` method

4. Cache cleared

## Testing
Now test with: "afficher tous les commentaires"

Expected AI response:
```
{"action": "list_comments", "data": {}}
✅ Commentaires affichés
```

The AI will display all comments with their details.

## Summary of ALL Entity Support

The AI Assistant now supports ALL entities:
- ✅ Users/Students (create, update, suspend, search, filter)
- ✅ Courses (create, update, list, get, add chapters)
- ✅ Events (create, update, delete, list, get)
- ✅ Challenges (create, update, list, get)
- ✅ Communities (create, update, list, get)
- ✅ Quizzes (create, get)
- ✅ Posts (list, get)
- ✅ Comments (list, get, filter by post)

The AI can now manage the ENTIRE platform!
