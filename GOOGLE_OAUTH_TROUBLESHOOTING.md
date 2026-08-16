# Google OAuth Blocking - Troubleshooting Guide

## Common Causes & Solutions

### 🔴 Issue 1: "Redirect URI Mismatch"

**Error Message:** "The redirect URI in the request does not match the registered redirect URI"

**Solution:**
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Navigate to **APIs & Services** → **Credentials**
3. Find your OAuth 2.0 Client ID
4. Click **Edit**
5. Under **Authorized redirect URIs**, ensure you have:
   - `http://localhost:8000/auth/google/callback` ✅
   - `http://localhost/fruit2web/public/auth/google/callback` ✅
6. Click **Save**
7. Clear Laravel cache: `php artisan config:clear`

---

### 🔴 Issue 2: "Invalid Client" or "Client Not Found"

**Cause:** Credentials not set or wrong format

**Solution:**
1. Check your `.env` file has these lines:
   ```
   GOOGLE_CLIENT_ID=your_actual_client_id
   GOOGLE_CLIENT_SECRET=your_actual_client_secret
   ```
2. Make sure they're filled in (not empty)
3. Run: `php artisan config:clear`
4. Restart your server

---

### 🔴 Issue 3: "OAuth Consent Screen Not Configured"

**Error:** "The OAuth client was not found"

**Solution:**
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Go to **APIs & Services** → **OAuth consent screen**
3. Click **Create** 
4. Select **External**
5. Fill in:
   - **App name:** FruitWeb
   - **User support email:** your@email.com
   - **Developer contact:** your@email.com
6. Click **Save and Continue**
7. On Scopes: Click **Save and Continue**
8. On Test users: Add your email, click **Save and Continue**
9. Review and click **Back to Dashboard**

---

### 🔴 Issue 4: "Access Blocked" or "This app isn't verified"

**Cause:** Google thinks your app is suspicious (normal for development)

**Solution - Option A (Testing):**
1. Add yourself as a test user:
   - Go to **OAuth consent screen**
   - Scroll to **Test users**
   - Click **Add Users**
   - Add your email
2. Now you can test without warnings

**Solution - Option B (Production):**
1. Complete the OAuth verification:
   - Go to **OAuth consent screen**
   - Click **Publish App**
   - Verify your domain ownership
   - Submit for Google review (takes a few days)

---

### 🔴 Issue 5: "Localhost Connection Refused"

**Cause:** Laravel server not running

**Solution:**
```bash
# Start your Laravel development server
php artisan serve

# Or if using XAMPP, make sure Apache is running
```

Then test with: `http://localhost:8000/auth/google`

---

### 🔴 Issue 6: Check Error Logs

**Solution:**
1. Open your Laravel log file:
   ```
   storage/logs/laravel.log
   ```
2. Look for error messages related to Google
3. Share the error with details to troubleshoot further

---

## 🧪 Testing Steps

1. ✅ Fill in credentials in `.env`:
   ```
   GOOGLE_CLIENT_ID=your_id_here
   GOOGLE_CLIENT_SECRET=your_secret_here
   ```

2. ✅ Clear cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. ✅ Start Laravel server:
   ```bash
   php artisan serve
   ```

4. ✅ Go to: `http://localhost:8000/auth/google`

5. ✅ You should be redirected to Google login page

6. ✅ After login, you should be redirected back to your app

---

## 📱 Quick Checklist

- [ ] Google Client ID filled in `.env`
- [ ] Google Client Secret filled in `.env`
- [ ] Redirect URI configured in Google Console
- [ ] Redirect URI matches between `.env` and Google Console
- [ ] OAuth Consent Screen configured
- [ ] Your email added as test user
- [ ] Laravel cache cleared
- [ ] Laravel server running
- [ ] Correct URL in address bar (http://localhost:8000)

---

## 🆘 Still Blocked?

1. Share the **exact error message** you're seeing
2. Check **storage/logs/laravel.log** for detailed error
3. Verify your **credentials are correct** (copy-paste from Google Console)
4. Make sure **Laravel server is running**

