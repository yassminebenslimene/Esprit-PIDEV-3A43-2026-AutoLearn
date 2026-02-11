# 🧪 Complete Test Scenario for Event Management System

## Database State
- ✅ Users: 2 existing users (1 Admin + 1 Étudiant)
- ✅ Schema: Synced and validated
- ✅ Routes: 50+ routes configured correctly
- ✅ Templates: 50 Twig files validated

## Phase 1: Authentication & Navigation Tests
### 1.1 Frontoffice Access (Public - No Login Required)
- [ ] Navigate to `http://localhost:8000/`
- [ ] Should display event list (currently will be empty or show cancelled events filtered out)
- [ ] Should see "Login" link in navbar if not authenticated
- [ ] Should NOT be redirected to `/backoffice`

### 1.2 Admin Login & Role-Based Redirect
- [ ] Click "Login" → navigate to `http://localhost:8000/login`
- [ ] Login with existing admin credentials
- [ ] Should redirect to `/backoffice` (or display admin dashboard)
- [ ] Admin navbar should show: Dashboard, Events, Teams, Participations, Users links

### 1.3 Student Login & Role-Based Redirect
- [ ] Logout (click logout link or navigate to `/logout`)
- [ ] Login with existing student credentials
- [ ] Should redirect to `/` (frontoffice)
- [ ] Navbar should show: Events, Profile, Logout

### 1.4 Navigation Between Zones
- [ ] **From Frontoffice as Admin**: Should be able to navigate to `/backoffice` via navbar link (if added)
- [ ] **From Backoffice as Admin**: Should have links to all CRUD sections
- [ ] **From Profile**: Should have back button to previous page

---

## Phase 2: Admin CRUD Operations Test
### 2.1 Event Management (`/backoffice/evenement`)

#### Create Event:
- [ ] Click "Create Event" or navigate to `/backoffice/evenement/new`
- [ ] Form should display with fields:
  - Title (required text)
  - Description (textarea)
  - Type (dropdown: ATELIER, CONFERENCE, COMPETITION, HACKATHON)
  - Start Date (datetime)
  - End Date (datetime)
  - Location (text)
  - Capacity Max (number)
  - Is Cancelled (checkbox)
- [ ] Fill form with valid data (dates: start < end, capacity > 0)
- [ ] Submit → should create and redirect to event list
- [ ] Flash message: "Event created successfully"

#### List Events:
- [ ] Navigate to `/backoffice/evenement`
- [ ] Display table with columns: Title, Type, Dates, Status, Capacity, Actions
- [ ] Status should auto-evaluate:
  - If isCanceled=true → "ANNULÉ"
  - If dateEnd < now → "TERMINÉ"
  - If dateStart < now < dateEnd → "EN_COURS"
  - Otherwise → "PLANIFIÉ"
- [ ] Each row has Edit/Delete buttons

#### Edit Event:
- [ ] Click Edit on any event
- [ ] Form pre-populated with existing data
- [ ] Modify fields (e.g., change title, dates, capacity)
- [ ] Submit → should update and show flash message
- [ ] Return to list and verify changes

#### Delete Event:
- [ ] Click Delete button on an event
- [ ] Should show confirmation (if implemented)
- [ ] Event removed from list
- [ ] Side effect: All related Equipes and Participations should auto-delete (cascade)

### 2.2 Team Management (`/backoffice/equipe`)

#### Create Team (Validation Tests):
- [ ] Navigate to `/backoffice/equipe/new`
- [ ] Form fields:
  - Name (required text)
  - Event (required select dropdown)
  - Members (multi-select, should show list of students)
- [ ] **Test 1 - Less than 4 members**: Select 3 students → Submit → Should error: "Team must have 4-6 members"
- [ ] **Test 2 - More than 6 members**: Select 7 students → Submit → Should error: "Team must have 4-6 members"
- [ ] **Test 3 - Valid team (4-6 members)**: Select 4-6 students → Submit → Should succeed
- [ ] **Test 4 - Duplicate student**: Select same student twice → Should error: "Each student can appear only once"
- [ ] **Test 5 - Students from different event**: Create 2 events, try to add student from event B to team for event A → Should validate correctly

#### List Teams:
- [ ] Navigate to `/backoffice/equipe`
- [ ] Display table: Number of members should match form input (4-6)
- [ ] Show related Event name
- [ ] Verify cascade deletion: Delete event → Teams should also be deleted

#### Edit Team:
- [ ] Modify member count (test adding/removing students)
- [ ] Verify validation rules still apply

### 2.3 Participation Management (`/backoffice/participation`)

#### Create Participation (Status Logic):
- [ ] Navigate to `/backoffice/participation/new`
- [ ] Form fields:
  - Event (required select)
  - Team (required select - should filter by selected event)
  - Status (required select: EN_ATTENTE, ACCEPTÉE, REJETÉE)
  - Comment (optional textarea)
  - Date (should auto-set to now)
- [ ] **Test 1 - Auto-status EN_ATTENTE**: Create participation with status EN_ATTENTE
  - Controller should auto-evaluate if capacity allows
  - If event capacity < team members → auto-reject
  - If event capacity >= team members → auto-accept
- [ ] **Test 2 - Explicit ACCEPTÉE**: Set status to ACCEPTÉE
  - Should check capacity before persisting
- [ ] **Test 3 - REJETÉE**: Admin can freely set this status
- [ ] **Test 4 - Unique constraint**: Try to create 2 participations for same (event, team) pair → Should error: "This team already participates in this event"

#### List Participations:
- [ ] Navigate to `/backoffice/participation`
- [ ] Display table: Event, Team, Status, Admin Comment, Date
- [ ] Verify capacity calculation works correctly

#### Edit Participation:
- [ ] Change status from ACCEPTÉE → REJETÉE
- [ ] Verify update persists

### 2.4 User Management (`/backoffice/user`)

#### Create User:
- [ ] Navigate to `/backoffice/user/new`
- [ ] Form should show:
  - First Name (required)
  - Last Name (required)
  - Email (required, unique)
  - Password (required)
  - Role (select: ADMIN or ETUDIANT)
  - Niveau field (DEBUTANT, INTERMEDIAIRE, AVANCE) - **only visible if ETUDIANT selected**
- [ ] Create 1 new Admin user
- [ ] Create 2 new Student users (with different levels)
- [ ] Submit → should create users and show success message

#### List Users:
- [ ] Navigate to `/backoffice/user`
- [ ] Display table: Name, Email, Role, Created Date
- [ ] Filter by role if available

#### Edit User:
- [ ] Edit an admin user's email
- [ ] Edit a student's niveau
- [ ] Verify changes persist

---

## Phase 3: Frontoffice & Profile Tests

### 3.1 Event Browsing (Public)
- [ ] Access `/` without login
- [ ] Should show list of all non-cancelled events
- [ ] Display: Title, Type, Dates, Location, Capacity
- [ ] Link to event detail if available

### 3.2 Event Detail (Frontoffice)
- [ ] Click on an event
- [ ] Should show full details (title, description, type, dates, location, current participations count vs capacity)
- [ ] If authenticated as student: Could show "View Teams" or registration option

### 3.3 User Profile (`/profile`)
- [ ] Access `/profile` as authenticated user
- [ ] Should display user info in edit form:
  - First Name
  - Last Name
  - Email
  - Niveau (for students)
  - Password change option
- [ ] Edit and submit → should persist changes
- [ ] Flash message: "Profile updated successfully"

---

## Phase 4: Security & Access Control Tests

### 4.1 Admin-Only Access
- [ ] Logout completely
- [ ] Try to access `/backoffice` directly → Should redirect to `/login`
- [ ] Try to access `/backoffice/evenement/new` → Should redirect to `/login`
- [ ] Login as student
- [ ] Try to access `/backoffice` → Should show 403 Forbidden (or redirect to home)
- [ ] Try to access `/backoffice/user/new` → Should show 403 Forbidden

### 4.2 Authenticated-Only Access
- [ ] Logout
- [ ] Try to access `/profile` → Should redirect to `/login`
- [ ] Login as any user
- [ ] Access `/profile` → Should display user form

### 4.3 Public Access
- [ ] Logout
- [ ] Access `/` → Should still display event list
- [ ] Access `/login` → Should display login form
- [ ] Access `/register` → Should display registration form

---

## Phase 5: Validation & Business Logic Tests

### 5.1 Form Validation
- [ ] **Event**: Try to create with endDate before startDate → Should error
- [ ] **Event**: Try to create with capacity = 0 → Should error
- [ ] **Event**: Try to create with empty title → Should error (required)
- [ ] **Team**: Try to create with < 4 members → Should error
- [ ] **User**: Try to create with duplicate email → Should error (unique constraint)
- [ ] **User**: Try to create with empty password → Should error

### 5.2 Event Status Auto-Evaluation
- [ ] Create event with:
  - Start = tomorrow, End = in 2 days, isCanceled = false
  - Expected status: "PLANIFIÉ"
- [ ] Create event with:
  - Start = yesterday, End = tomorrow, isCanceled = false
  - Expected status: "EN_COURS"
- [ ] Create event with:
  - Start = 3 days ago, End = 2 days ago, isCanceled = false
  - Expected status: "TERMINÉ"
- [ ] Create event with:
  - Start = tomorrow, End = in 2 days, isCanceled = true
  - Expected status: "ANNULÉ"

### 5.3 Team Capacity Validation
- [ ] Create event with capacity = 8
- [ ] Create team A with 5 members
- [ ] Try to create team B with 5 members for same event
  - Team A accepted → 5 spots taken, 3 remaining
  - Team B participation with 5 members → Should fail if capacity <5 OR accept if logic allows oversubscription
  - Check business logic: Is oversubscription allowed? If not, error expected
  
### 5.4 Cascade Deletion
- [ ] Create event E1 with team T1 (4 students) and participation P1
- [ ] Delete event E1
- [ ] Go to `/backoffice/equipe` → Team T1 should be gone
- [ ] Go to `/backoffice/participation` → Participation P1 should be gone
- [ ] Verify cascade is working

---

## Phase 6: Error Handling & Edge Cases

### 6.1 404 Errors
- [ ] Try to access `/backoffice/evenement/999` (non-existent ID)
- [ ] Should show 404 or friendly error message
- [ ] Should not crash application

### 6.2 Concurrent Edits
- [ ] Open 2 browser windows (or private + regular)
- [ ] Edit same event in both
- [ ] Last save wins (or show conflict if implemented)
- [ ] No data corruption

### 6.3 Large Data Sets
- [ ] Create 100 users
- [ ] Create 50 events
- [ ] Check if list pages load without timeout
- [ ] Check pagination (if implemented)

---

## Quick Command Reference

```bash
# Clear cache
php bin\console cache:clear

# Create test user
php bin\console make:user

# Run migrations
php bin\console doctrine:migrations:migrate

# Check schema
php bin\console doctrine:schema:validate

# View routes
php bin\console debug:router

# Validate templates
php bin\console lint:twig templates

# Start dev server
php -S localhost:8000 -t public
```

---

## Expected Results Summary

| Feature | Status | Notes |
|---------|--------|-------|
| Routes defined | ✅ | 50+ routes, all prefixed correctly |
| Database schema | ✅ | Synced and validated |
| Admin CRUD | To Test | Should all work after route fixes |
| Frontoffice display | To Test | Should show events without redirect |
| Auth/Security | To Test | IsGranted should enforce access |
| Validations | To Test | Entity validators should catch errors |
| Cascade delete | To Test | Deleting event should delete teams/participations |
| Status auto-eval | To Test | Event status should auto-evaluate |

---

## 🎯 Success Criteria
- ✅ Admin can CRUD all entities without 404 errors
- ✅ Routes don't redirect unexpectedly
- ✅ Security enforces role-based access
- ✅ Forms validate correctly
- ✅ Database stays in sync
- ✅ No PHP errors in logs/console
