# Google & Facebook OAuth Setup Guide

## Option 1: Quick Local Testing (Dummy Credentials)
For local testing WITHOUT real Google/Facebook setup, update your `.env`:

```env
# Google OAuth - Test/Dummy (for local testing only)
GOOGLE_CLIENT_ID=test-google-client-id-12345.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-test_secret_key_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Facebook OAuth - Test/Dummy (for local testing only)
FACEBOOK_CLIENT_ID=1234567890123456
FACEBOOK_CLIENT_SECRET=test_facebook_secret_key_here
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

**Note:** With dummy credentials, OAuth will fail at provider side, but the UI buttons will show and routes will work.

---

## Option 2: Real Google OAuth (Recommended for Testing)

### Step 1: Go to Google Cloud Console
- Visit: https://console.cloud.google.com/
- Sign in with your Google account

### Step 2: Create Project
- Click "Select a Project" → "New Project"
- Name: `fruit2web` → Create

### Step 3: Enable OAuth 2.0
- Go to "APIs & Services" → "OAuth consent screen"
- User Type: **External** → Create
- Fill in:
  - App name: `Fruit2Web`
  - User support email: Your email
  - Developer contact: Your email
- Save and Continue (skip scopes)
- Save and Continue (skip test users for now)

### Step 4: Create OAuth Credentials
- Go to "Credentials" (left sidebar)
- Click "Create Credentials" → "OAuth client ID"
- Application type: **Web application**
- Name: `Fruit2Web Local`
- Authorized redirect URIs: Add
  ```
  http://localhost:8000/auth/google/callback
  ```
- Click Create
- Copy: **Client ID** and **Client Secret**

### Step 5: Add to `.env`
```env
GOOGLE_CLIENT_ID=YOUR_COPIED_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_COPIED_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

## Option 3: Real Facebook OAuth

### Step 1: Go to Facebook Developer
- Visit: https://developers.facebook.com/
- Sign in with Facebook account

### Step 2: Create App
- My Apps → Create App
- App type: **Consumer**
- App name: `Fruit2Web`
- Create

### Step 3: Setup Login
- Add Product → Facebook Login
- Setup → Web
- Site URL: `http://localhost:8000`

### Step 4: Configure OAuth
- Settings → Basic (copy App ID and App Secret)
- Settings → Basic → App Domains: Add `localhost`
- Products → Facebook Login → Settings → Valid OAuth Redirect URIs:
  ```
  http://localhost:8000/auth/facebook/callback
  ```

### Step 5: Add to `.env`
```env
FACEBOOK_CLIENT_ID=YOUR_FACEBOOK_APP_ID
FACEBOOK_CLIENT_SECRET=YOUR_FACEBOOK_APP_SECRET
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

---

## After Updating `.env`:

Run these commands:
```bash
php artisan config:clear
php artisan cache:clear
php artisan serve
```

Then test:
1. Go to http://localhost:8000/login
2. Click Google or Facebook button
3. Should redirect to login page or show role selection after successful auth

---

## Troubleshooting

**Error: "Missing required parameter: client_id"**
- → Check if credentials are in `.env` and cache is cleared

**Error: "Redirect URI mismatch"**
- → Make sure redirect URL in OAuth provider matches exactly:
  `http://localhost:8000/auth/google/callback`
  `http://localhost:8000/auth/facebook/callback`

**Still not working?**
- Check `.env` file path: `c:\Xamppp\htdocs\fruit2web\.env`
- Verify APP_URL: `http://localhost:8000`
- Run: `php artisan config:clear && php artisan cache:clear && php artisan serve`
