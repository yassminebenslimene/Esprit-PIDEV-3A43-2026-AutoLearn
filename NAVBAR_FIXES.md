# Navbar Navigation Fixes

## Issues Fixed

### 1. Section ID Mismatch
**Problem**: Navbar link pointed to `#services` but the actual section ID was `#cours`
**Solution**: Updated navbar link from `#services` to `#cours`

### 2. Non-existent Sections
**Problem**: Navbar had links to sections that don't exist:
- `#courses` (doesn't exist)
- `#team` (doesn't exist)

**Solution**: Removed these links and kept only existing sections:
- `#top` (Home)
- `#cours` (Cours section)
- `#events` (Events section)
- `#challenge` (Challenge section)
- `#contact` (Contact section)

### 3. External Links with scroll-to-section Class
**Problem**: External route links (Communauté, Login, Register, My Participations) had `scroll-to-section` class, causing JavaScript to try scrolling to non-existent sections

**Solution**: Removed `scroll-to-section` class from external links:
- Communauté
- My Participations
- Login
- Register
- Mon profil
- Déconnexion

### 4. JavaScript Active State Errors
**Problem**: The `onScroll()` function tried to process all links including external ones, causing JavaScript errors

**Solution**: Updated `onScroll()` function to skip external links:
```javascript
function onScroll(event){
    var scrollPos = $(document).scrollTop();
    $('.nav a').each(function () {
        var currLink = $(this);
        var href = currLink.attr("href");
        
        // Skip external links (those without # or with full URLs)
        if (!href || href.indexOf('#') !== 0) {
            return;
        }
        
        // ... rest of the code
    });
}
```

### 5. Missing Variable in ChallengeController
**Problem**: `ChallengeController` was rendering `frontoffice/index.html.twig` but not passing the `cours` variable, causing "Variable 'cours' does not exist" error

**Solution**: Added `CoursRepository` injection and passed `cours` variable to the template:
```php
public function index(
    ChallengeRepository $challengeRepository,
    EvenementRepository $evenementRepository,
    EquipeRepository $equipeRepository,
    \App\Repository\Cours\CoursRepository $coursRepository
): Response{
    $challenges = $challengeRepository->findAll();
    $evenements = $evenementRepository->findAll();
    $equipes = $equipeRepository->findAll();
    $cours = $coursRepository->findAll();
    
    return $this->render('frontoffice/index.html.twig', [
        'cours' => $cours,
        'challenges' => $challenges,
        'evenements' => $evenements,
        'equipes' => $equipes,
    ]);
}
```

## Files Modified

1. **autolearn/templates/frontoffice/index.html.twig**
   - Fixed navbar links to match actual section IDs
   - Removed `scroll-to-section` class from external links

2. **autolearn/templates/base_front.html.twig**
   - Fixed navbar links to point to correct sections
   - Removed `scroll-to-section` class from external links
   - Updated links to use full path with anchors (e.g., `{{ path('app_frontoffice') }}#cours`)

3. **autolearn/public/frontoffice/js/custom.js**
   - Updated `onScroll()` function to skip external links
   - Added safety checks for element existence

4. **autolearn/src/Controller/ChallengeController.php**
   - Added `CoursRepository` injection
   - Added `cours` variable to template rendering

## Current Navbar Structure

### On Homepage (index.html.twig)
- Home → `#top` (scroll to top)
- Cours → `#cours` (scroll to cours section)
- Events → `#events` (scroll to events section)
- Challenge → `#challenge` (scroll to challenge section)
- Contact → `#contact` (scroll to contact section)
- Communauté → External route (no scroll)
- My Participations → External route (no scroll)
- Profile Dropdown → External routes (no scroll)

### On Other Pages (base_front.html.twig)
- Home → `{{ path('app_frontoffice') }}#top`
- Cours → `{{ path('app_frontoffice') }}#cours`
- Events → `{{ path('app_frontoffice') }}#events`
- Challenge → `{{ path('app_frontoffice') }}#challenge`
- Contact → `{{ path('app_frontoffice') }}#contact`
- Communauté → External route
- Profile/Login links → External routes

## Testing

1. **On Homepage**:
   - Click "Cours" → Should scroll to cours section
   - Click "Events" → Should scroll to events section
   - Click "Challenge" → Should scroll to challenge section
   - Click "Contact" → Should scroll to contact section
   - Click "Communauté" → Should navigate to communauté page

2. **On Challenges Page** (`/challenges` route):
   - Page should load without errors
   - All navbar links should work correctly
   - Cours section should display properly

3. **On Other Pages**:
   - Click "Cours" → Should navigate to homepage and scroll to cours section
   - Click "Events" → Should navigate to homepage and scroll to events section
   - All section links should work correctly

4. **Active State**:
   - As you scroll down the homepage, the navbar should highlight the current section
   - External links should not interfere with active state

## Notes

- The scroll-to-section functionality only works on the homepage where the sections exist
- From other pages, clicking section links will navigate to homepage first, then scroll
- External links (Communauté, Profile, etc.) work as normal page navigation
- The JavaScript now safely handles both scroll links and external links
- All controllers rendering `frontoffice/index.html.twig` must pass the `cours` variable
