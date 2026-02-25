# 🔥 Groq Rate Limit Issue - SOLUTION

## Problem Identified

From the logs:
```
Rate limit reached for model `llama-3.1-8b-instant` 
on tokens per minute (TPM): Limit 6000, Used 2997, Requested 3765. 
Please try again in 7.62s.
```

**Root Cause:** Groq free tier has a limit of **6,000 tokens per minute (TPM)**

## Current Token Usage

Per request: ~3,000-3,765 tokens
- System prompt: ~2,000 tokens
- User question: ~100 tokens
- Database context: ~1,000-1,500 tokens
- Response: ~500 tokens

**Result:** You can only make **1-2 requests per minute** before hitting the limit!

## Why This Happens

1. **First request:** Works fine (uses 3,000 tokens)
2. **Second request (within 1 minute):** Rate limit exceeded (3,000 + 3,765 = 6,765 > 6,000)
3. **Third request:** Still rate limited, uses fallback

## Solutions

### Solution 1: Wait Between Requests ⏱️

**Simplest solution:** Wait 10-15 seconds between AI requests.

The rate limit resets every minute, so spacing out requests avoids the limit.

**How to implement:**
- Add a cooldown message in the chat
- Disable send button for 10 seconds after each request
- Show "Please wait..." message

### Solution 2: Reduce Token Usage Further 📉

**Current:** ~3,000-3,765 tokens per request
**Target:** ~2,000-2,500 tokens per request

This would allow 2-3 requests per minute.

**How to reduce:**

1. **Limit users to 10 instead of 20:**
   ```php
   array_slice($allUsers, 0, 10)
   ```

2. **Limit courses to 5 instead of 10:**
   ```php
   array_slice($allCourses, 0, 5)
   ```

3. **Remove descriptions completely:**
   ```php
   // Don't send descriptions at all
   'list' => array_map(function($c) {
       return [
           'id' => $c->getId(),
           'titre' => $c->getTitre(),
           'niveau' => $c->getNiveau(),
       ];
   }, array_slice($allCourses, 0, 5))
   ```

4. **Shorten system prompt:**
   - Remove examples
   - Keep only essential instructions

### Solution 3: Upgrade Groq Plan 💰

**Free Tier:** 6,000 TPM
**Paid Tier:** 30,000+ TPM

Visit: https://console.groq.com/settings/billing

**Cost:** ~$10-20/month for higher limits

### Solution 4: Smart Data Loading 🧠

Only send data relevant to the question:

```php
// If question contains "cours", send only courses
// If question contains "event", send only events
// If question contains "user", send only users
```

This would reduce tokens to ~1,500-2,000 per request.

### Solution 5: Use Caching 💾

Cache the system prompt and database context for 1 minute:

```php
// Cache database data for 60 seconds
$cacheKey = 'ai_db_data_' . $user->getId();
$data = $cache->get($cacheKey, function() {
    return $this->getAllDatabaseData(...);
});
```

This reduces database queries and token usage.

## Recommended Solution

**Combination of Solutions 1 + 2:**

1. **Reduce token usage to ~2,000-2,500** (allows 2 requests/minute)
2. **Add 10-second cooldown** between requests (prevents spam)

This gives the best user experience without requiring payment.

## Implementation

### Step 1: Reduce Data Further

Edit `src/Service/AIAssistantService.php`:

```php
// Reduce users to 10
array_slice($allUsers, 0, 10)

// Reduce courses to 5
array_slice($allCourses, 0, 5)

// Reduce events to 3
array_slice($upcomingEvents, 0, 3)

// Reduce challenges to 5
array_slice($allChallenges, 0, 5)

// Reduce communities to 5
array_slice($allCommunities, 0, 5)
```

### Step 2: Add Cooldown in Frontend

Edit `templates/ai_assistant/chat_widget.html.twig`:

```javascript
let lastRequestTime = 0;
const COOLDOWN_MS = 10000; // 10 seconds

function sendMessage() {
    const now = Date.now();
    const timeSinceLastRequest = now - lastRequestTime;
    
    if (timeSinceLastRequest < COOLDOWN_MS) {
        const waitTime = Math.ceil((COOLDOWN_MS - timeSinceLastRequest) / 1000);
        addMessage(`⏱️ Veuillez attendre ${waitTime} secondes...`, 'bot');
        return;
    }
    
    lastRequestTime = now;
    
    // Continue with normal request...
}
```

## Testing

After implementing:

1. **Send first request:** Should work ✅
2. **Wait 10 seconds**
3. **Send second request:** Should work ✅
4. **Try immediately:** Should show cooldown message ✅

## Current Status

**Issue:** Rate limit exceeded (6,000 TPM)
**Cause:** Sending ~3,000-3,765 tokens per request
**Impact:** Can only make 1-2 requests per minute

**Solutions Available:**
1. ⏱️ Add cooldown (10 seconds) - **RECOMMENDED**
2. 📉 Reduce tokens to ~2,000 - **RECOMMENDED**
3. 💰 Upgrade Groq plan ($10-20/month)
4. 🧠 Smart data loading (advanced)
5. 💾 Caching (advanced)

## Quick Fix (Right Now)

**Just wait 10-15 seconds between AI requests!**

The rate limit resets every minute, so if you space out your requests, it will work fine.

---

**Date:** February 25, 2026
**Issue:** Groq rate limit exceeded
**Limit:** 6,000 tokens per minute
**Current usage:** ~3,000-3,765 tokens per request
**Solution:** Wait 10 seconds between requests OR reduce token usage
