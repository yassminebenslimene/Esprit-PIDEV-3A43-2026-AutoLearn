# Quiz Management in Chapitre - Requirements

## Feature Overview
Integrate quiz management into the chapter (Chapitre) section of the backoffice, allowing administrators to manage quizzes for each chapter. This follows the same pattern as the chapter management integration into courses.

## User Stories

### US-1: View Quizzes for a Chapter
**As an** administrator  
**I want to** view all quizzes associated with a specific chapter  
**So that** I can manage the quizzes for that chapter

**Acceptance Criteria:**
- 1.1: A "Voir Quiz" button appears next to "Modifier" and "Supprimer" buttons in the chapter list
- 1.2: Clicking "Voir Quiz" navigates to a page showing all quizzes for that chapter
- 1.3: The quiz list displays: titre, description, etat (status), and action buttons
- 1.4: The page shows the parent chapter information (titre, cours)
- 1.5: A "Retour aux chapitres" button allows navigation back to the chapter list
- 1.6: If no quizzes exist, an empty state message is displayed with a link to create the first quiz

### US-2: Create a New Quiz for a Chapter
**As an** administrator  
**I want to** create a new quiz for a specific chapter  
**So that** I can add assessment content to the chapter

**Acceptance Criteria:**
- 2.1: A "Nouveau Quiz" button is available on the quiz list page
- 2.2: Clicking the button opens a form to create a new quiz
- 2.3: The form includes fields: titre, description, etat
- 2.4: The quiz is automatically associated with the current chapter
- 2.5: Form validation follows existing Quiz entity constraints
- 2.6: After successful creation, user is redirected to the quiz list for that chapter
- 2.7: A success message confirms the quiz was created

### US-3: View Quiz Details
**As an** administrator  
**I want to** view the details of a specific quiz  
**So that** I can review its content and properties

**Acceptance Criteria:**
- 3.1: A "Voir" button is available for each quiz in the list
- 3.2: Clicking the button displays the quiz details page
- 3.3: The page shows: titre, description, etat, and parent chapter information
- 3.4: Navigation buttons allow returning to the quiz list or editing the quiz
- 3.5: The quiz must belong to the current chapter (validation check)

### US-4: Edit an Existing Quiz
**As an** administrator  
**I want to** edit a quiz's properties  
**So that** I can update its content or status

**Acceptance Criteria:**
- 4.1: A "Modifier" button is available for each quiz in the list
- 4.2: Clicking the button opens a form pre-filled with current quiz data
- 4.3: All quiz fields can be modified: titre, description, etat
- 4.4: Form validation follows existing Quiz entity constraints
- 4.5: After successful update, user is redirected to the quiz list
- 4.6: A success message confirms the quiz was updated
- 4.7: The quiz must belong to the current chapter (validation check)

### US-5: Delete a Quiz
**As an** administrator  
**I want to** delete a quiz from a chapter  
**So that** I can remove outdated or incorrect assessment content

**Acceptance Criteria:**
- 5.1: A "Supprimer" button is available for each quiz in the list
- 5.2: Clicking the button shows a confirmation dialog
- 5.3: Confirming the deletion removes the quiz from the database
- 5.4: After deletion, user remains on the quiz list page
- 5.5: The quiz must belong to the current chapter (validation check)
- 5.6: CSRF token validation is required for the delete action

## Technical Requirements

### TR-1: Database Schema
- Add `OneToMany` relation in `Chapitre` entity to `Quiz` (collection: `quizzes`)
- Add `ManyToOne` relation in `Quiz` entity to `Chapitre` (property: `chapitre`)
- The `chapitre` field in `Quiz` should be nullable to maintain backward compatibility
- Create a database migration to add the foreign key

### TR-2: Routing
- All quiz management routes should be nested under the chapter context
- Route pattern: `/cours/{coursId}/chapitres/{chapitreId}/quizzes/*`
- Routes should be added to `CoursController.php` to maintain consistency
- Route names should follow the pattern: `app_cours_chapitre_quiz_*`

### TR-3: Templates
- Create templates in `templates/backoffice/cours/` directory:
  - `quizzes.html.twig` - List of quizzes for a chapter
  - `quiz_new.html.twig` - Create new quiz form
  - `quiz_show.html.twig` - View quiz details
  - `quiz_edit.html.twig` - Edit quiz form
- Templates should extend `backoffice/base.html.twig`
- Use consistent styling with existing backoffice templates

### TR-4: Form Handling
- Reuse existing `QuizType` form class
- Pre-set the `chapitre` property when creating a new quiz
- Ensure form validation follows Quiz entity constraints

### TR-5: Security & Validation
- Verify that the quiz belongs to the specified chapter before any operation
- Implement CSRF token validation for delete operations
- Return 404 error if quiz doesn't belong to the chapter
- Maintain existing Quiz entity validation rules

## Implementation Pattern Reference
This feature follows the same implementation pattern as the Chapitre-in-Cours integration:
- Reference: `CoursController.php` methods for chapter management
- Reference: `templates/backoffice/cours/chapitres.html.twig` for UI pattern
- Reference: `templates/backoffice/cours/chapitre_*.html.twig` for form templates

## Out of Scope
- Question management within quizzes (existing functionality remains unchanged)
- Quiz statistics or analytics
- Quiz duplication or cloning features
- Bulk operations on quizzes
- Quiz preview or testing functionality
