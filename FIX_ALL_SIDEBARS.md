# Fix All Sidebars - Instructions

## Problem
Several pages have their own complete HTML with incomplete sidebars instead of extending `base.html.twig`.

## Pages to Fix
1. `analytics.html.twig` - Has own sidebar
2. `communaute/index.html.twig` - May have own sidebar  
3. `post/index.html.twig` - May have own sidebar
4. `commentaire/index.html.twig` - May have own sidebar

## Solution
Each page should:
1. Start with `{% extends 'backoffice/base.html.twig' %}`
2. Define `{% block title %}` and `{% block page_title %}`
3. Put content in `{% block body %}`
4. Remove all HTML structure (html, head, body tags)
5. Remove sidebar code

## Template Structure
```twig
{% extends 'backoffice/base.html.twig' %}

{% block title %}Page Title{% endblock %}
{% block page_title %}Page Title{% endblock %}

{% block body %}
    <!-- Page content here -->
{% endblock %}
```

This ensures ALL pages use the same complete sidebar from `base.html.twig`.
