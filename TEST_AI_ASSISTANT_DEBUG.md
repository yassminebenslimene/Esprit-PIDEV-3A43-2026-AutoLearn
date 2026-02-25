# 🔍 Debug AI Assistant Connection Issue

## Problem

User is getting "❌ Erreur de connexion" when trying to use the AI assistant.

## Tests to Run

### 1. Test Groq API Directly
```bash
php bin/console app:test-groq
```
**Expected:** All tests pass ✅

### 2. Check if Server is Running
```bash
symfony server:status
```
**Expected:** Server running on http://127.0.0.1:8000

### 3. Test AI Assistant Route Directly

Open browser console and run:
```javascript
fetch('/ai-assistant/ask', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
        question: 'test'
    })
})
.then(res => res.json())
.then(data => console.log(data))
.catch(err => console.error(err));
```

### 4. Check Symfony Logs
```bash
tail -f var/log/dev.log
```

Look for errors when sending a message.

### 5. Check Browser Console

Open browser DevTools (F12) → Console tab
Look for JavaScript errors or failed network requests.

### 6. Check Network Tab

Open browser DevTools (F12) → Network tab
Send a message and check:
- Is the request being sent?
- What's the response status code?
- What's the response body?

## Possible Causes

### 1. Token Limit Exceeded
The expanded AI assistant now sends ~4,000-6,000 tokens per request.
If Groq rate limit is exceeded, it will fail.

**Solution:** Reduce data sent in `getAllDatabaseData()`

### 2. Timeout
Request takes longer than 30 seconds.

**Solution:** Increase timeout or reduce data

### 3. CSRF Token Issue
Symfony might be blocking the request.

**Solution:** Check if CSRF protection is enabled

### 4. Session Expired
User session might have expired.

**Solution:** Refresh the page and try again

### 5. JavaScript Error
There might be a JavaScript error preventing the request.

**Solution:** Check browser console

## Quick Fix: Reduce Data Sent

If the issue is token limit, we can reduce the data sent:

Edit `src/Service/AIAssistantService.php` in `getAllDatabaseData()`:

```php
// Instead of sending ALL courses, send only first 10
'list' => array_map(function($c) {
    return [
        'id' => $c->getId(),
        'titre' => $c->getTitre(),
        'niveau' => $c->getNiveau(),
    ];
}, array_slice($allCourses, 0, 10)) // Limit to 10
```

Do the same for events, challenges, communities.

## Test Again

After making changes:
1. Clear cache: `php bin/console cache:clear`
2. Refresh the page
3. Try sending a message again

## If Still Not Working

Check if the issue is:
1. **Frontend:** JavaScript error → Check browser console
2. **Backend:** PHP error → Check `var/log/dev.log`
3. **Groq API:** Rate limit → Check Groq dashboard
4. **Network:** Connection issue → Check internet connection

## Expected Behavior

When you send: "créer event Workshop IA"

The AI should:
1. Receive the message
2. Generate JSON: `{"action": "create_event", "data": {...}}`
3. Execute the action
4. Respond: "✅ Événement créé"

## Debug Steps

1. ✅ Groq API is working (tested with `app:test-groq`)
2. ✅ Routes are configured correctly
3. ✅ Controller is handling requests
4. ❓ Is the request reaching the controller?
5. ❓ Is Groq responding?
6. ❓ Is the response being sent back?

## Next Steps

1. Check browser console for errors
2. Check network tab for failed requests
3. Check Symfony logs for errors
4. If token limit exceeded, reduce data sent
5. If timeout, increase timeout or reduce data
