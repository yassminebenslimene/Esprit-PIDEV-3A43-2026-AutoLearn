# Refactor Navbar/Sidebar Before Merging

## Problem
- Navbar and sidebar code is duplicated in every .twig file
- Makes merging friend's branches very difficult
- Any navbar/sidebar fix requires updating 20+ files

## Solution
Extract navbar/sidebar into reusable components

## Step 1: Create Component Files

Create these 2 new files:

### File 1: `templates/backoffice/_navbar.html.twig`
```twig
{# Navbar component - include this in all pages #}
<nav class="navbar">
    <!-- Your fixed navbar HTML here -->
</nav>
```

### File 2: `templates/backoffice/_sidebar.html.twig`
```twig
{# Sidebar component - include this in all pages #}
<aside class="sidebar">
    <!-- Your fixed sidebar HTML here -->
</aside>
```

## Step 2: Update All Your Pages

Replace duplicated navbar/sidebar code with:

```twig
{% extends 'backoffice/base.html.twig' %}

{% block body %}
    {% include 'backoffice/_navbar.html.twig' %}
    {% include 'backoffice/_sidebar.html.twig' %}
    
    <main class="content">
        <!-- Page content here -->
    </main>
{% endblock %}
```

## Step 3: List of Files to Update

Run this command to find all files with navbar/sidebar:

```bash
cd autolearn
findstr /S /I "navbar" templates\backoffice\*.twig > navbar_files.txt
findstr /S /I "sidebar" templates\backoffice\*.twig > sidebar_files.txt
```

## Step 4: After Refactoring, Merge is Easy

When merging friend's branches:

```bash
# Their pages will have old duplicated code
git merge origin/challenge

# Conflicts will show in their pages
# Just replace their navbar/sidebar with:
{% include 'backoffice/_navbar.html.twig' %}
{% include 'backoffice/_sidebar.html.twig' %}
```

## Benefits
✅ Navbar/sidebar code in ONE place only
✅ Easy to merge - just update include statements
✅ Future fixes only need 1 file change
✅ Consistent navbar/sidebar across all pages

## Want me to do this automatically?

I can create a script that:
1. Extracts your navbar/sidebar from one of your pages
2. Creates _navbar.html.twig and _sidebar.html.twig
3. Updates all your backoffice pages to use includes
4. Creates a merge guide for friend's branches

Say "yes" and I'll do it!
