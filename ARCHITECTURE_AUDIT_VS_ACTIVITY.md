# 📊 Architecture: Audit Bundle vs UserActivity Bundle

## 🎯 Clear Separation of Concerns

### 1. **Audit Bundle** (Simple Things Entity Audit)
**Purpose:** Track ADMIN actions on STUDENTS only

**What it tracks:**
- ✅ Admin creates a student
- ✅ Admin updates student info (email, name, level)
- ✅ Admin suspends a student
- ✅ Admin deletes a student (if allowed)

**What it DOES NOT track:**
- ❌ Student login/logout
- ❌ Student viewing courses
- ❌ Student doing quizzes
- ❌ Student posting comments
- ❌ Student self-modifying their profile

**Tables:**
- `revisions` - Who (admin) did what and when
- `etudiant_audit` - What changed on the student entity

**Current Configuration:**
```yaml
# config/packages/simple_things_entity_audit.yaml
simple_things_entity_audit:
    audited_entities:
        - App\Entity\Etudiant  # Only track Etudiant entity
```

---

### 2. **UserActivity Bundle** (Custom Bundle)
**Purpose:** Track ALL STUDENT activities on the platform

**What it currently tracks:**
- ✅ user.login - Student logs in
- ✅ user.logout - Student logs out
- ✅ user.suspended - Admin suspends student
- ✅ user.reactivated - Admin reactivates student
- ✅ user.created - Admin creates student
- ✅ user.updated - Admin updates student
- ✅ user.viewed - Admin views student profile

**What it SHOULD ALSO track (to implement):**
- 📚 **Course Activities:**
  - `course.viewed` - Student views a course
  - `course.enrolled` - Student enrolls in a course
  - `chapter.viewed` - Student views a chapter
  - `chapter.completed` - Student completes a chapter
  
- 📝 **Quiz & Exercise Activities:**
  - `quiz.started` - Student starts a quiz
  - `quiz.completed` - Student completes a quiz
  - `quiz.score` - Student's quiz score
  - `exercise.attempted` - Student attempts an exercise
  - `exercise.completed` - Student completes an exercise
  
- 🏆 **Challenge Activities:**
  - `challenge.joined` - Student joins a challenge
  - `challenge.completed` - Student completes a challenge
  - `challenge.won` - Student wins a challenge
  
- 📅 **Event Activities:**
  - `event.viewed` - Student views an event
  - `event.registered` - Student registers for an event
  - `event.attended` - Student attends an event
  - `event.cancelled` - Student cancels event registration
  
- 👥 **Community Activities:**
  - `community.joined` - Student joins a community
  - `community.left` - Student leaves a community
  - `post.created` - Student creates a post
  - `post.edited` - Student edits their post
  - `post.deleted` - Student deletes their post
  - `comment.created` - Student comments on a post
  - `comment.edited` - Student edits their comment
  - `comment.deleted` - Student deletes their comment
  
- 👤 **Profile Activities:**
  - `profile.viewed` - Student views their own profile
  - `profile.updated` - Student updates their own profile
  - `password.changed` - Student changes their password
  - `email.changed` - Student changes their email

**Tables:**
- `user_activity` - All student activities with metadata

**Service:**
- `ActivityLogger` - Service to log activities

---

## 🔧 How to Use

### For Admin Actions (Audit Bundle)
The audit bundle works automatically when you modify Etudiant entities:

```php
// This is automatically tracked by Audit Bundle
$student = new Etudiant();
$student->setNom('Dupont');
$student->setPrenom('Jean');
$em->persist($student);
$em->flush(); // ✅ Automatically logged in etudiant_audit
```

### For Student Activities (UserActivity Bundle)
You must manually log activities using ActivityLogger:

```php
// In your controller
public function viewCourse(Cours $cours, ActivityLogger $activityLogger): Response
{
    // Log the activity
    $activityLogger->log('course.viewed', [
        'course_id' => $cours->getId(),
        'course_title' => $cours->getTitre(),
        'course_level' => $cours->getNiveau(),
    ]);
    
    return $this->render('frontoffice/cours/show.html.twig', [
        'cours' => $cours
    ]);
}
```

---

## 📈 Benefits of This Architecture

1. **Clear Separation:**
   - Audit = Admin actions on students
   - Activity = Student actions on platform

2. **Better Analytics:**
   - Track student engagement
   - Measure course popularity
   - Identify inactive students
   - Analyze learning patterns

3. **Compliance:**
   - Audit trail for admin actions (GDPR, security)
   - Activity logs for student behavior (analytics, recommendations)

4. **Performance:**
   - Audit bundle only tracks critical entity changes
   - Activity bundle tracks everything else without entity overhead

---

## 🚀 Next Steps

### 1. Expand ActivityLogger Service
Add new methods for each activity type:

```php
// In ActivityLogger.php
public function logCourseView(Cours $cours): void
{
    $metadata = [
        'course_id' => $cours->getId(),
        'course_title' => $cours->getTitre(),
        'course_level' => $cours->getNiveau(),
    ];
    $this->log('course.viewed', $metadata);
}

public function logQuizComplete(Quiz $quiz, int $score): void
{
    $metadata = [
        'quiz_id' => $quiz->getId(),
        'quiz_title' => $quiz->getTitre(),
        'score' => $score,
        'max_score' => $quiz->getMaxScore(),
        'percentage' => ($score / $quiz->getMaxScore()) * 100,
    ];
    $this->log('quiz.completed', $metadata);
}

// ... etc for all activities
```

### 2. Add Activity Logging to Controllers
In each controller action, add activity logging:

```php
// CoursController.php
public function show(Cours $cours, ActivityLogger $activityLogger): Response
{
    $activityLogger->logCourseView($cours);
    // ... rest of controller logic
}

// QuizController.php
public function submit(Quiz $quiz, Request $request, ActivityLogger $activityLogger): Response
{
    $score = $this->calculateScore($request);
    $activityLogger->logQuizComplete($quiz, $score);
    // ... rest of controller logic
}
```

### 3. Create Activity Dashboard
Create a new page to visualize student activities:
- Most viewed courses
- Most active students
- Quiz completion rates
- Community engagement
- Event participation

---

## 📊 Example Queries

### Get student's course viewing history:
```sql
SELECT * FROM user_activity 
WHERE user_id = ? AND action = 'course.viewed'
ORDER BY created_at DESC;
```

### Get most popular courses:
```sql
SELECT metadata->>'$.course_id' as course_id, 
       metadata->>'$.course_title' as course_title,
       COUNT(*) as views
FROM user_activity 
WHERE action = 'course.viewed'
GROUP BY course_id
ORDER BY views DESC
LIMIT 10;
```

### Get student engagement score:
```sql
SELECT user_id, COUNT(*) as activity_count
FROM user_activity
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY user_id
ORDER BY activity_count DESC;
```

---

## ✅ Summary

| Feature | Audit Bundle | UserActivity Bundle |
|---------|-------------|---------------------|
| **Purpose** | Admin actions on students | Student activities |
| **Who** | Admins only | Students only |
| **What** | Create, Update, Suspend, Delete students | Login, View, Complete, Post, Comment, etc. |
| **Automatic** | Yes (entity changes) | No (manual logging) |
| **Tables** | `revisions`, `etudiant_audit` | `user_activity` |
| **Use Case** | Compliance, Security, Audit trail | Analytics, Engagement, Recommendations |

