# Selective Merge Plan: Keep Only Events & Participations from Amira

## Current Situation
- Full merge of Amira into ilef was done (commit 1e8a618)
- This brought in many changes beyond events/participations
- Need to keep ONLY events & participations changes from Amira
- Preserve ALL other work from ilef

## Files to Keep from Amira (Events & Participations)

### Entity & Enum
- `src/Entity/Participation.php` - Feedbacks feature
- `src/Enum/SentimentFeedback.php` - Feedback sentiment types

### Controllers
- `src/Controller/FeedbackController.php` - Feedback submission
- `src/Controller/EvenementController.php` - Events management (if changed)

### Services
- `src/Service/FeedbackAnalyticsService.php` - Feedback analytics
- `src/Service/AIReportService.php` - AI reports from feedback

### Templates
- `templates/frontoffice/feedback/form.html.twig` - Feedback form
- `templates/frontoffice/participation/mes_participations.html.twig` - Participations list
- `templates/backoffice/evenement/index.html.twig` - Events backoffice
- `templates/backoffice/ai_dashboard/index.html.twig` - AI dashboard

### Migrations
- `migrations/Version20260220211749.php` - Adds feedbacks column to participation

### Configuration (if needed)
- Check `config/services.yaml` for feedback services

## Files to REVERT to ilef State (Before Merge)

### All AI Assistant Files (keep ilef version)
- `src/Controller/AIAssistantController.php`
- `src/Service/AIAssistantService.php`
- `src/Service/OllamaService.php`
- `src/Service/RAGService.php`
- `src/Service/ActionExecutorService.php`
- `templates/ai_assistant/chat_widget.html.twig`

### All Audit Bundle Files (keep ilef version)
- `src/Controller/AuditController.php`
- `src/Entity/Etudiant.php`
- `config/packages/simple_things_entity_audit.yaml`
- `config/routes/sonata_entity_audit.yaml`
- `templates/backoffice/audit/*`

### All Backoffice Templates (keep ilef version with sidebar fixes)
- `templates/backoffice/base.html.twig`
- `templates/backoffice/analytics.html.twig`
- `templates/backoffice/challenge.html.twig`
- `templates/backoffice/challenge_form.html.twig`
- `templates/backoffice/exercice.html.twig`
- `templates/backoffice/exercice_form.html.twig`
- `templates/backoffice/communaute/index.html.twig`
- `templates/backoffice/post/index.html.twig`
- `templates/backoffice/commentaire/index.html.twig`
- `templates/backoffice/users/users.html.twig`
- `templates/backoffice/users/settings.html.twig`
- `templates/backoffice/index.html.twig`

### Dependencies (keep ilef version)
- `composer.json`
- `composer.lock`
- `symfony.lock`
- `config/bundles.php`

## Execution Plan

### Option 1: Manual Selective Checkout (RECOMMENDED)
```bash
# 1. Checkout specific files from Amira branch
git checkout origin/Amira -- src/Entity/Participation.php
git checkout origin/Amira -- src/Enum/SentimentFeedback.php
git checkout origin/Amira -- src/Controller/FeedbackController.php
git checkout origin/Amira -- src/Service/FeedbackAnalyticsService.php
git checkout origin/Amira -- src/Service/AIReportService.php
git checkout origin/Amira -- templates/frontoffice/feedback/
git checkout origin/Amira -- templates/frontoffice/participation/
git checkout origin/Amira -- templates/backoffice/evenement/
git checkout origin/Amira -- templates/backoffice/ai_dashboard/
git checkout origin/Amira -- migrations/Version20260220211749.php

# 2. Checkout all other files from ilef before merge (427c8ad)
git checkout 427c8ad -- src/Controller/AIAssistantController.php
git checkout 427c8ad -- src/Service/AIAssistantService.php
git checkout 427c8ad -- src/Service/OllamaService.php
git checkout 427c8ad -- src/Service/RAGService.php
git checkout 427c8ad -- src/Service/ActionExecutorService.php
git checkout 427c8ad -- templates/ai_assistant/
git checkout 427c8ad -- src/Controller/AuditController.php
git checkout 427c8ad -- src/Entity/Etudiant.php
git checkout 427c8ad -- templates/backoffice/audit/
git checkout 427c8ad -- templates/backoffice/base.html.twig
git checkout 427c8ad -- templates/backoffice/analytics.html.twig
# ... etc for all backoffice templates

# 3. Commit the selective merge
git add .
git commit -m "Selective merge: Keep only Events & Participations from Amira, preserve all ilef work"
```

### Option 2: Reset and Cherry-pick
```bash
# 1. Reset to before merge
git reset --hard 427c8ad

# 2. Create a temporary branch with Amira changes
git checkout -b temp-amira-events origin/Amira

# 3. Go back to ilef
git checkout ilef

# 4. Cherry-pick only events/participations files
# (manually copy files)

# 5. Commit
git add <events-files>
git commit -m "Add Events & Participations features from Amira"
```

## What User Wants to Keep

### From Amira:
✅ Events management enhancements
✅ Participation feedback system
✅ Feedback analytics
✅ AI dashboard for feedback
✅ Migration for feedbacks column

### From ilef (DO NOT CHANGE):
✅ Audit bundle configuration (Etudiant only)
✅ AI Assistant with Ollama
✅ RAG Service
✅ Action Executor
✅ All sidebar fixes
✅ User activity bundle
✅ Suspension system
✅ All backoffice templates with fixed sidebars

## Next Steps

1. Confirm with user which approach to use
2. Execute the selective merge
3. Test that events/participations work
4. Verify all ilef features still work
5. Clear caches and validate schema
