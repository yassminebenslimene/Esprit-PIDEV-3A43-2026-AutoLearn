# Email Troubleshooting Guide

## What Was Fixed

### 1. BackofficeController - Admin Creating Students
**Problem**: No email was being sent when admin creates a new student
**Solution**: Added email sending code with BrevoMailService

### 2. BrevoMailService - Template Rendering Bug
**Problem**: Twig date filter `{{ "now"|date("Y") }}` wasn't being replaced
**Solution**: Added explicit replacement for date filter in renderTemplate()

### 3. Error Messages Hidden
**Problem**: Exceptions were caught silently, hiding the real errors
**Solution**: Updated flash messages to show actual error messages

## How to Test

### Test 1: Admin Creates Student (BackofficeController)
1. Log in as admin
2. Go to `/backoffice/users/new`
3. Create a new student with valid email
4. Check for success/warning message
5. Check student's email inbox

### Test 2: Self-Registration (SecurityController)
1. Go to registration page
2. Register as new student
3. Check for success/warning message
4. Check email inbox

### Test 3: UserController (if used)
1. Access user creation form
2. Create new student
3. Check for success/warning message
4. Check email inbox

## Debugging Steps

### Step 1: Check Brevo API Key
```bash
# In autolearn directory
type .env | findstr BREVO_API_KEY
```
Make sure the key starts with `xkeysib-` and is valid.

### Step 2: Check Logs
Look for log entries in `var/log/dev.log`:
```bash
# View recent logs
type var\log\dev.log | findstr /i "brevo mail"
```

### Step 3: Test Brevo API Directly
You can test if your Brevo API key works by running this PHP script:

```php
<?php
require 'vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$apiKey = 'YOUR_BREVO_API_KEY';

try {
    $response = $client->post('https://api.brevo.com/v3/smtp/email', [
        'headers' => [
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'sender' => ['email' => 'autolearn66@gmail.com', 'name' => 'AutoLearn'],
            'to' => [['email' => 'YOUR_TEST_EMAIL@example.com', 'name' => 'Test']],
            'subject' => 'Test Email',
            'htmlContent' => '<h1>Test</h1><p>This is a test email.</p>',
        ]
    ]);
    echo "Success! Status: " . $response->getStatusCode() . "\n";
    echo $response->getBody()->getContents();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## Common Issues

### Issue 1: "Brevo API key is not configured"
- Check `.env` file has `BREVO_API_KEY=xkeysib-...`
- Clear Symfony cache: `php bin/console cache:clear`

### Issue 2: "Template not found"
- Verify templates exist:
  - `templates/emails/welcome.html.twig`
  - `templates/emails/welcome.txt.twig`
  - `templates/emails/registration_confirmation.html.twig`
  - `templates/emails/registration_confirmation.txt.twig`

### Issue 3: "401 Unauthorized" from Brevo
- API key is invalid or expired
- Get new key from Brevo dashboard: https://app.brevo.com/settings/keys/api

### Issue 4: "403 Forbidden" from Brevo
- Sender email not verified in Brevo
- Go to https://app.brevo.com/senders and verify `autolearn66@gmail.com`

### Issue 5: Emails not arriving
- Check spam folder
- Verify recipient email is valid
- Check Brevo dashboard for delivery status: https://app.brevo.com/email/logs

## Brevo Configuration

### Current Setup
- **API Key**: Configured in `.env`
- **Sender Email**: autolearn66@gmail.com
- **Sender Name**: AutoLearn
- **Method**: Direct API calls (not SMTP)

### Verify Sender Email
1. Go to https://app.brevo.com/senders
2. Make sure `autolearn66@gmail.com` is verified
3. If not, click "Add a sender" and verify it

## Testing Checklist

- [ ] Brevo API key is valid
- [ ] Sender email is verified in Brevo
- [ ] All email templates exist
- [ ] GuzzleHTTP is installed (`composer require guzzlehttp/guzzle`)
- [ ] Cache is cleared
- [ ] Test admin creating student
- [ ] Test self-registration
- [ ] Check error messages if emails fail
- [ ] Check Brevo logs for delivery status

## Next Steps if Still Not Working

1. Run the test script above to verify Brevo API works
2. Check `var/log/dev.log` for detailed error messages
3. Verify sender email in Brevo dashboard
4. Try sending to different email addresses
5. Check Brevo account limits (free tier has daily limits)
