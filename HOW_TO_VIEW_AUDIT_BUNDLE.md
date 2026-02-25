# How to View the Audit Bundle in Backoffice

## 🎯 Quick Access

The audit bundle is now accessible in your backoffice with a beautiful UI!

### Access Points:

1. **Main Audit Page**
   - URL: `http://localhost:8000/backoffice/audit/`
   - Sidebar: Click "Audit Bundle" under System section
   - Shows all revisions with timestamps and user attribution

2. **Statistics Dashboard**
   - URL: `http://localhost:8000/backoffice/audit/stats`
   - Shows comprehensive statistics:
     - Total revisions and changes
     - Changes by type (INSERT/UPDATE/DELETE)
     - Recent activity (last 7 days)
     - Most active users

3. **Revision Details**
   - Click on any revision to see complete details
   - Shows all changes made in that revision
   - Displays full entity state at that moment

4. **User History**
   - View complete history for any specific user
   - Timeline view of all changes
   - Easy navigation between revisions

## 📍 Where to Find It

### In the Sidebar:
```
System Section
├── Users
├── Settings
├── Activity Log (Custom Bundle)
└── Audit Bundle ← NEW! (Professional Bundle)
```

## 🎨 What You'll See

### Main Audit Page Features:
- ✅ Info cards showing total revisions and tracking status
- ✅ Complete revision history table
- ✅ Timestamps and user attribution
- ✅ Quick links to view details
- ✅ Beautiful glassmorphism design matching your backoffice theme

### Statistics Page Features:
- ✅ Total revisions and changes count
- ✅ Visual breakdown by operation type (INSERT/UPDATE/DELETE)
- ✅ Activity chart for last 7 days
- ✅ Most active users leaderboard

### Revision Details Features:
- ✅ Complete revision information
- ✅ All changes in that revision
- ✅ Full entity state display
- ✅ Links to user history

### User History Features:
- ✅ Timeline view of all user changes
- ✅ Visual indicators for operation types
- ✅ Complete entity state at each revision
- ✅ Easy navigation between revisions

## 🧪 How to Test It

### Step 1: Generate Some Audit Data
Go to User Management and perform some operations:

1. **Create a new user** (generates INSERT revision)
   - Go to `/backoffice/users`
   - Click "Add New User"
   - Fill in the form and save
   - ✅ This creates an INSERT (INS) revision

2. **Update a user** (generates UPDATE revision)
   - Edit any user's information
   - Change name, email, or level
   - Save the changes
   - ✅ This creates an UPDATE (UPD) revision

3. **Suspend/Reactivate a user** (generates UPDATE revision)
   - Suspend or reactivate a user
   - ✅ This creates an UPDATE (UPD) revision

4. **Delete a user** (generates DELETE revision)
   - Delete a user (if you want to test)
   - ✅ This creates a DELETE (DEL) revision

### Step 2: View the Audit Trail
1. Go to `/backoffice/audit/`
2. You'll see all the revisions you just created
3. Click on any revision to see details
4. Click "View Statistics" to see the stats dashboard

### Step 3: Explore User History
1. From the revision details page
2. Click "View Full User History"
3. See the complete timeline of changes for that user

## 📊 What Gets Tracked

The audit bundle automatically tracks:

### Tracked Entities:
- ✅ User (base entity)
- ✅ Etudiant (student)
- ✅ Admin (administrator)

### Tracked Operations:
- ✅ **INSERT (INS)**: User creation
- ✅ **UPDATE (UPD)**: Any field modification
- ✅ **DELETE (DEL)**: User deletion

### Tracked Fields:
- ✅ nom (last name)
- ✅ prenom (first name)
- ✅ email
- ✅ role
- ✅ niveau (for students)
- ✅ is_suspended
- ✅ suspended_at
- ✅ suspension_reason
- ✅ suspended_by
- ✅ last_login_at
- ✅ discr (discriminator - entity type)

### Ignored Fields:
- ❌ createdAt (configured to ignore)
- ❌ updatedAt (configured to ignore)
- ❌ password (not stored in audit for security)

## 🎓 For Your Professor

### What to Show:

1. **Bundle Installation**
   - Show `composer.json` with the bundle dependency
   - Show `config/bundles.php` with bundle registration

2. **YAML Configuration**
   - Show `config/packages/simple_things_entity_audit.yaml`
   - Explain the configuration options

3. **Entity Annotation**
   - Show `src/Entity/User.php` with `#[Audit\Auditable]` annotation

4. **Database Tables**
   - Show the `revisions` table structure
   - Show the `user_audit` table structure
   - Demonstrate data in both tables

5. **Live Demonstration**
   - Create a user → Show INSERT revision
   - Update the user → Show UPDATE revision
   - View the audit trail in the UI
   - Show statistics dashboard

6. **Explain the Value**
   - Complete audit trail for compliance
   - Time-travel capability to see historical states
   - User attribution for accountability
   - Automatic tracking with zero manual code

## 🔍 Database Queries (For Verification)

### View All Revisions:
```sql
SELECT * FROM revisions ORDER BY timestamp DESC LIMIT 10;
```

### View All User Audit Records:
```sql
SELECT * FROM user_audit ORDER BY rev DESC LIMIT 10;
```

### View Specific User History:
```sql
SELECT ua.*, r.timestamp, r.username
FROM user_audit ua
JOIN revisions r ON ua.rev = r.id
WHERE ua.userId = 1
ORDER BY r.timestamp DESC;
```

### Count Changes by Type:
```sql
SELECT revtype, COUNT(*) as count
FROM user_audit
GROUP BY revtype;
```

## 🎨 UI Features

### Design Elements:
- ✅ Glassmorphism design matching backoffice theme
- ✅ Gradient colors (emerald & gold)
- ✅ Smooth animations and transitions
- ✅ Responsive layout
- ✅ Icon-based navigation
- ✅ Color-coded operation types:
  - 🟢 Green for INSERT
  - 🔵 Blue for UPDATE
  - 🔴 Red for DELETE

### User Experience:
- ✅ Easy navigation between pages
- ✅ Clear visual hierarchy
- ✅ Intuitive information display
- ✅ Quick access to details
- ✅ Beautiful empty states
- ✅ Helpful tooltips and labels

## 📝 Routes Summary

| Route | URL | Description |
|-------|-----|-------------|
| `backoffice_audit_index` | `/backoffice/audit/` | Main audit trail page |
| `backoffice_audit_stats` | `/backoffice/audit/stats` | Statistics dashboard |
| `backoffice_audit_revision_details` | `/backoffice/audit/revision/{id}` | Revision details |
| `backoffice_audit_user_history` | `/backoffice/audit/user/{id}` | User history timeline |

## 🚀 Next Steps

1. **Test the UI**: Navigate to `/backoffice/audit/` and explore
2. **Generate Data**: Create/update/delete users to see audit data
3. **Show Professor**: Demonstrate the complete functionality
4. **Explain Benefits**: Highlight automatic tracking and compliance features

## ✅ Summary

You now have:
- ✅ Professional audit bundle installed and configured
- ✅ Beautiful UI to view audit data
- ✅ Complete tracking of user management operations
- ✅ Statistics and analytics dashboard
- ✅ User history timeline view
- ✅ Integration with your existing backoffice design

The audit bundle is working automatically in the background. Every time you create, update, or delete a user, it's being tracked and stored in the audit tables!

---

**Created**: February 22, 2026  
**Status**: ✅ Fully Functional  
**Access**: `/backoffice/audit/`
