# 🚀 AI Assistant Complete Expansion Plan

## Current Status
- ✅ Admin AI: Can create/update/suspend students only
- ❌ Admin AI: Cannot manage courses, events, challenges, communities
- ❌ Student AI: Not working at all
- ❌ Both: Limited database access

## Goal
Make BOTH assistants 100% functional with COMPLETE database access and ALL actions.

---

## PHASE 1: Expand Admin AI (Priority 1)

### Add to AIAssistantService::getAllDatabaseData()

Currently sends only:
- User statistics
- User list (for admin)
- Current user info

**EXPAND TO SEND:**
```php
$data = [
    // USERS (existing)
    'stats' => [...],
    'all_users' => [...],
    'current_user' => [...],
    
    // COURSES - NEW
    'courses' => [
        'total' => count,
        'list' => [id, titre, niveau, duree, chapitres_count]
    ],
    
    // EVENTS - NEW
    'events' => [
        'total' => count,
        'upcoming' => [...],
        'past' => [...]
    ],
    
    // CHALLENGES - NEW
    'challenges' => [
        'total' => count,
        'active' => [...]
    ],
    
    // COMMUNITIES - NEW
    'communities' => [
        'total' => count,
        'list' => [id, nom, membres_count, posts_count]
    ],
    
    // QUIZZES - NEW
    'quizzes' => [
        'total' => count,
        'by_course' => [...]
    ]
];
```

### Add to ActionExecutorService

**NEW ACTIONS FOR ADMIN:**
```php
// COURSE MANAGEMENT
- create_course
- update_course
- delete_course
- add_chapter
- add_resource

// EVENT MANAGEMENT
- create_event
- update_event
- delete_event
- list_participants

// CHALLENGE MANAGEMENT
- create_challenge
- update_challenge
- delete_challenge

// COMMUNITY MANAGEMENT
- create_community
- update_community
- delete_community
- moderate_post

// QUIZ MANAGEMENT
- create_quiz
- add_question
- update_quiz
```

---

## PHASE 2: Fix Student AI (Priority 2)

### Expand Student Database Access

**SEND TO STUDENT AI:**
```php
$data = [
    // CURRENT STUDENT INFO
    'current_student' => [
        'id', 'nom', 'prenom', 'niveau',
        'courses_enrolled' => [...],
        'challenges_completed' => [...],
        'communities_joined' => [...]
    ],
    
    // AVAILABLE COURSES
    'available_courses' => [
        'all' => [...],
        'by_level' => [
            'DEBUTANT' => [...],
            'INTERMEDIAIRE' => [...],
            'AVANCE' => [...]
        ],
        'recommended' => [...] // Based on student level
    ],
    
    // EVENTS
    'events' => [
        'upcoming' => [...],
        'can_register' => [...]
    ],
    
    // CHALLENGES
    'challenges' => [
        'available' => [...],
        'completed' => [...],
        'in_progress' => [...]
    ],
    
    // COMMUNITIES
    'communities' => [
        'joined' => [...],
        'available' => [...],
        'popular' => [...]
    ],
    
    // STATISTICS
    'my_stats' => [
        'courses_completed' => count,
        'challenges_done' => count,
        'points' => total,
        'rank' => position
    ]
];
```

### Add Student Actions

**NEW ACTIONS FOR STUDENTS:**
```php
// COURSE ACTIONS
- view_course_details
- enroll_in_course
- view_my_courses
- view_chapter
- complete_chapter

// CHALLENGE ACTIONS
- view_challenge
- start_challenge
- submit_challenge
- view_my_challenges

// EVENT ACTIONS
- view_event_details
- register_for_event
- cancel_registration
- view_my_events

// COMMUNITY ACTIONS
- view_community
- join_community
- leave_community
- create_post
- comment_on_post
- view_posts

// QUIZ ACTIONS
- start_quiz
- submit_answer
- view_quiz_results

// STATISTICS
- view_my_progress
- view_leaderboard
```

---

## PHASE 3: Update System Prompts

### Admin Prompt Updates

Add to `buildAdminPrompt()`:
```
YOU CAN MANAGE:

📚 COURSES:
- Create/update/delete courses
- Add chapters and resources
- View course statistics

📅 EVENTS:
- Create/update/delete events
- Manage registrations
- View participants

💪 CHALLENGES:
- Create/update/delete challenges
- View submissions
- Grade challenges

👥 COMMUNITIES:
- Create/update/delete communities
- Moderate posts and comments
- Manage members

📝 QUIZZES:
- Create/update quizzes
- Add questions
- View results
```

### Student Prompt Updates

Add to `buildStudentPrompt()`:
```
YOU CAN HELP STUDENTS:

📚 DISCOVER COURSES:
- Recommend courses by level
- Show course details
- Enroll in courses
- Track progress

💪 TAKE CHALLENGES:
- Find challenges by difficulty
- Start challenges
- Submit solutions
- View results

📅 JOIN EVENTS:
- List upcoming events
- Register for events
- View event details

👥 PARTICIPATE IN COMMUNITIES:
- Find communities by interest
- Join/leave communities
- Create posts
- Comment on posts

📊 TRACK PROGRESS:
- View completed courses
- See challenge results
- Check leaderboard position
```

---

## PHASE 4: Add Missing Repositories

Need to inject these repositories:
```php
// In AIAssistantService constructor
private ChallengeRepository $challengeRepository;
private QuizRepository $quizRepository;
private ChapitreRepository $chapitreRepository;
private PostRepository $postRepository;
private CommentaireRepository $commentaireRepository;
```

---

## PHASE 5: Error Handling

Add clear error messages:
```php
// When data is missing
"❌ No courses found"
"❌ Event not found"
"❌ You're not enrolled in this course"

// When action fails
"❌ Already enrolled"
"❌ Event is full"
"❌ Challenge already completed"

// When permission denied
"❌ Admin only action"
"❌ Must be enrolled first"
```

---

## Implementation Order

1. ✅ **Day 1**: Expand `getAllDatabaseData()` for Admin (add courses, events, challenges, communities)
2. ✅ **Day 2**: Add Admin actions to `ActionExecutorService` (create/update/delete for all entities)
3. ✅ **Day 3**: Update Admin system prompt with new capabilities
4. ✅ **Day 4**: Expand `getAllDatabaseData()` for Students (add all student-relevant data)
5. ✅ **Day 5**: Add Student actions to `ActionExecutorService`
6. ✅ **Day 6**: Update Student system prompt
7. ✅ **Day 7**: Test everything and fix bugs

---

## Testing Checklist

### Admin AI Tests
- [ ] Create a course
- [ ] Add chapter to course
- [ ] Create an event
- [ ] Create a challenge
- [ ] Create a community
- [ ] View statistics for each entity
- [ ] Update/delete entities

### Student AI Tests
- [ ] View available courses
- [ ] Enroll in a course
- [ ] View course details
- [ ] Register for an event
- [ ] Start a challenge
- [ ] Join a community
- [ ] Create a post
- [ ] View my progress

---

## Files to Modify

1. `src/Service/AIAssistantService.php`
   - Expand `getAllDatabaseData()`
   - Update `buildAdminPrompt()`
   - Update `buildStudentPrompt()`

2. `src/Service/ActionExecutorService.php`
   - Add all new actions
   - Add permission checks
   - Add error handling

3. `config/services.yaml`
   - Inject new repositories

---

## Estimated Token Usage

This expansion will increase token usage per request:
- Current: ~1,700-2,700 tokens
- After expansion: ~4,000-6,000 tokens
- Still within Groq limits (8,000 tokens context)

---

## Next Steps

1. Read this plan
2. Confirm you want to proceed
3. I'll implement Phase 1 first (Admin expansion)
4. Test Phase 1
5. Move to Phase 2 (Student AI)
6. Test Phase 2
7. Final testing and optimization

**Ready to start? Say "yes" and I'll begin with Phase 1!**
