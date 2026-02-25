# 🔧 Update Database Schema for Expanded Audit

## ⚠️ IMPORTANT: Run This Command

After updating the audit configuration, you MUST run this command to create the new audit tables:

```bash
php bin/console doctrine:schema:update --force
```

## 📊 New Tables That Will Be Created

This will create audit tables for all tracked entities:

1. **User Management:**
   - `etudiant_audit` (already exists)
   - `admin_audit` (new)

2. **Course Management:**
   - `cours_audit` (new)
   - `chapitre_audit` (new)
   - `ressource_audit` (new)

3. **Exercise & Challenge:**
   - `exercice_audit` (new)
   - `challenge_audit` (new)
   - `quiz_audit` (new)

4. **Events:**
   - `evenement_audit` (new)

5. **Community:**
   - `communaute_audit` (new)
   - `post_audit` (new)
   - `commentaire_audit` (new)

6. **Teams:**
   - `equipe_audit` (new)

## ✅ Verification

After running the command, verify the tables were created:

```sql
SHOW TABLES LIKE '%_audit';
```

You should see all the audit tables listed.

## 🚀 What Happens Next

Once the tables are created, the Audit Bundle will automatically start tracking changes to these entities whenever an admin:
- Creates a new course, challenge, event, etc.
- Updates existing content
- Deletes content

All changes will be logged in the `revisions` table with details in the respective `*_audit` tables.
