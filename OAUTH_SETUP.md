# Google & Facebook OAuth Setup Guide

This guide explains how to set up Google and Facebook authentication for your FruitWeb application.

## Prerequisites

The following have already been configured:
- ✅ Laravel Socialite package installed
- ✅ `SocialAuthController` created
- ✅ OAuth routes configured
- ✅ User model updated with `provider` and `provider_id` fields
- ✅ Database migration created
- ✅ `config/services.php` configured
- ✅ Login page updated with Google and Facebook buttons

## Setup Instructions

### 1. Google OAuth Setup

#### Step 1: Create a Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click "Create Project" and give it a name (e.g., "FruitWeb")
3. Click "Create"

#### Step 2: Enable Google+ API
1. In the left sidebar, click "APIs & Services" → "Library"
2. Search for "Google+ API"
3. Click on it and select "Enable"

#### Step 3: Create OAuth 2.0 Credentials
1. Go to "APIs & Services" → "Credentials"
2. Click "Create Credentials" → "OAuth 2.0 Client IDs"
3. If prompted, set up the OAuth consent screen first:
   - Choose "External" user type
   - Fill in the app information
   - Add your email as a test user
4. Select "Web application"
5. Under "Authorized redirect URIs", add:
   - `http://localhost:8000/auth/google/callback`
   - `http://yourdomain.com/auth/google/callback` (for production)
6. Click "Create"
7. Copy the **Client ID** and **Client Secret**

#### Step 4: Add to .env
Add these to your `.env` file:
```
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

### 2. Facebook OAuth Setup

#### Step 1: Create a Facebook App
1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Click "My Apps" → "Create App"
3. Select "Consumer" and click "Next"
4. Fill in the app details:
   - App Name: "FruitWeb"
   - App Contact Email: your email
   - App Purpose: Choose appropriate category
5. Click "Create App"

#### Step 2: Add Facebook Login Product
1. In your app dashboard, click "Add Product"
2. Find "Facebook Login" and click "Set Up"
3. Choose "Web"

#### Step 3: Configure Settings
1. Go to "Settings" → "Basic"
2. Note your **App ID** and **App Secret**
3. Go to "Settings" → "Basic" and scroll down
4. Under "App Domains", add:
   - `localhost:8000`
   - `yourdomain.com` (for production)

#### Step 4: Configure OAuth Redirect URIs
1. Go to "Facebook Login" → "Settings"
2. Under "Valid OAuth Redirect URIs", add:
   - `http://localhost:8000/auth/facebook/callback`
   - `https://yourdomain.com/auth/facebook/callback` (for production)
3. Save Changes

#### Step 5: Add to .env
Add these to your `.env` file:
```
FACEBOOK_CLIENT_ID=your_app_id_here
FACEBOOK_CLIENT_SECRET=your_app_secret_here
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

---

### 3. Verify Configuration

1. Clear Laravel cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. Run your application:
   ```bash
   php artisan serve
   ```

3. Visit `http://localhost:8000/login`

4. You should see the Google and Facebook login buttons

5. Test the OAuth flow by clicking either button

---

## How It Works

When a user clicks Google or Facebook:

1. **Redirect Phase**: User is sent to Google/Facebook login page
2. **Authorization**: User authorizes FruitWeb to access their profile
3. **Callback Phase**: Google/Facebook redirects back to your app with user data
4. **Account Linking**: The app checks if user exists:
   - ✅ If existing user with same provider → Login
   - ✅ If existing user with same email → Link provider to account
   - ✅ If new user → Create account and login

---

## Troubleshooting

### "Redirect URI mismatch"
- Ensure the redirect URI in your OAuth provider settings matches exactly:
  - Protocol: `http://` (local) or `https://` (production)
  - Domain: `localhost:8000` or your actual domain
  - Path: `/auth/google/callback` or `/auth/facebook/callback`

### "Invalid Client ID/Secret"
- Double-check you copied the credentials correctly from the provider dashboard
- Verify they're added to the `.env` file
- Run `php artisan config:clear`

### Users can't authorize
- Ensure your app is published (Facebook)
- Check that you added your email as a test user (Google)
- Verify domain settings in provider console

---

## File Structure

Created/Modified files:
- `app/Http/Controllers/Auth/SocialAuthController.php` - Handles OAuth logic
- `config/services.php` - OAuth provider configuration
- `routes/web.php` - OAuth routes
- `app/Models/User.php` - Added provider fields
- `database/migrations/2025_01_29_add_oauth_columns_to_users_table.php` - Schema migration
- `resources/views/auth/login.blade.php` - Updated with OAuth buttons
- `.env` - Environment variables

---

## Production Notes

For production deployment:
1. Update `.env` with production domain
2. Update redirect URIs in OAuth provider settings
3. Use `https://` protocol
4. Set `APP_DEBUG=false`
5. Ensure proper SSL certificate

