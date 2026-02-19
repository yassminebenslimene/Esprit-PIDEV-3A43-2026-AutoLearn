# Security Fix Instructions - Remove API Keys from Git History

## Problem
Your `.env` file with real API keys was committed to Git and GitHub is blocking the push for security reasons.

## Solution Steps

### Step 1: Remove .env from Git tracking (keep local file)

```bash
cd autolearn
git rm --cached .env
```

This removes `.env` from Git but keeps your local file with the real API keys.

### Step 2: Verify .gitignore

The `.gitignore` file now includes `/.env` so it won't be tracked again.

### Step 3: Add and commit the changes

```bash
git add .gitignore
git add .env.example
git add CONTACT_FORM_IMPLEMENTATION.md
git commit -m "Security: Remove API keys from repository, add .env.example"
```

### Step 4: Remove API keys from Git history

You need to remove the API keys from previous commits. Use BFG Repo-Cleaner or git filter-branch:

#### Option A: Using BFG Repo-Cleaner (Recommended - Easier)

1. Download BFG: https://rtyley.github.io/bfg-repo-cleaner/
2. Create a file called `secrets.txt` with your API keys (the actual keys from your .env)
3. Run BFG:
```bash
java -jar bfg.jar --replace-text secrets.txt autolearn
cd autolearn
git reflog expire --expire=now --all && git gc --prune=now --aggressive
```

#### Option B: Using git filter-branch (More Complex)

```bash
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all
```

### Step 5: Force push to GitHub

⚠️ **WARNING**: This rewrites Git history. Coordinate with your team!

```bash
git push origin ilef --force
```

### Step 6: Revoke and regenerate API keys

**IMPORTANT**: Since your API keys were exposed, you should:

1. Go to Brevo dashboard: https://app.brevo.com/settings/keys/api
2. Delete the old API keys
3. Generate new API keys
4. Update your local `.env` file with new keys

### Step 7: Verify

```bash
# Check that .env is not tracked
git status

# Should show:
# On branch ilef
# nothing to commit, working tree clean
# (.env should NOT appear)
```

---

## Alternative: Start Fresh (If Above Doesn't Work)

If the above steps are too complex, you can:

1. **Revoke current API keys** in Brevo dashboard
2. **Generate new API keys**
3. **Update local `.env`** with new keys
4. **Allow the secrets on GitHub** using the URLs provided in the error:
   - https://github.com/yassminebenslimene/autolearn/security/secret-scanning/unblock-secret/39m50KxXCtGzce4yev9Urusrzwp
   - https://github.com/yassminebenslimene/autolearn/security/secret-scanning/unblock-secret/39m50IJT4IWaN5jAhlrfqEAhNXv

This allows the push but the old keys are now public (that's why you revoked them first).

---

## Prevention for Future

1. **Never commit `.env` files** - They're now in `.gitignore`
2. **Use `.env.example`** - For documentation with placeholder values
3. **Check before committing**:
   ```bash
   git status
   git diff
   ```
4. **Use pre-commit hooks** - To automatically check for secrets

---

## Quick Fix (Recommended)

Since this is a learning project and the keys are already exposed:

1. **Revoke old API keys** in Brevo:
   - Go to https://app.brevo.com/settings/keys/api
   - Delete both keys (API and SMTP)

2. **Generate new keys**:
   - Create new API key
   - Create new SMTP key

3. **Update local `.env`** with new keys

4. **Remove .env from Git**:
   ```bash
   git rm --cached .env
   git add .gitignore .env.example CONTACT_FORM_IMPLEMENTATION.md
   git commit -m "Security: Remove API keys, add .env.example"
   ```

5. **Push**:
   ```bash
   git push origin ilef
   ```

The old keys in history are now useless (revoked), and new keys are safe in your local `.env` file only.

---

## Files Changed

- ✅ `.gitignore` - Added `/.env`
- ✅ `.env.example` - Created with placeholder values
- ✅ `CONTACT_FORM_IMPLEMENTATION.md` - Removed real API keys
- ✅ `.env` - Removed from Git tracking (but kept locally)

---

## Summary

1. Remove `.env` from Git: `git rm --cached .env`
2. Revoke old API keys in Brevo dashboard
3. Generate new API keys
4. Update local `.env` with new keys
5. Commit changes: `git commit -m "Security fix"`
6. Push: `git push origin ilef`

Your API keys are now safe! 🔒
