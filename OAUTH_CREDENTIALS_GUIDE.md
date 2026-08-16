# Complete OAuth Credentials Setup Guide

Follow these steps to get your Google and Facebook credentials.

---

## 📱 GOOGLE OAUTH SETUP

### Step 1: Go to Google Cloud Console
1. Open browser and go to: https://console.cloud.google.com/
2. Sign in with your Google account (create one if you don't have it)

### Step 2: Create a New Project
1. Look for the project selector at the top left (might show "My First Project" or similar)
2. Click on it
3. Click **"NEW PROJECT"**
4. Enter Project Name: `FruitWeb`
5. Click **"CREATE"**
6. Wait for project to be created (1-2 minutes)

### Step 3: Enable Google+ API
1. In the left sidebar, click **"APIs & Services"**
2. Click **"Library"**
3. In the search box, type: `Google+ API`
4. Click on **"Google+ API"**
5. Click the blue **"ENABLE"** button
6. Wait for it to enable

### Step 4: Create OAuth Credentials
1. Go to **"APIs & Services"** → **"Credentials"** (left sidebar)
2. Click **"+ CREATE CREDENTIALS"** button (top)
3. Select **"OAuth client ID"**

**If you see a popup asking to configure the OAuth consent screen:**
   - Click **"CONFIGURE CONSENT SCREEN"**
   - Select **"External"** user type
   - Click **"CREATE"**
   - Fill in the form:
     - App name: `FruitWeb`
     - User support email: your email
     - Developer contact: your email
   - Click **"SAVE AND CONTINUE"**
   - On Scopes page, click **"SAVE AND CONTINUE"**
   - On Test users page, click **"SAVE AND CONTINUE"**
   - Click **"BACK TO DASHBOARD"**

4. Now go to **"Credentials"** again
5. Click **"+ CREATE CREDENTIALS"** → **"OAuth client ID"**
6. Select **"Web application"**
7. Give it a name: `FruitWeb Web App`
8. Under **"Authorized redirect URIs"**, click **"ADD URI"**
9. Add this URL: `http://localhost:8000/auth/google/callback`
10. Add another URI: `http://localhost/fruit2web/public/auth/google/callback`
11. Click **"CREATE"**

### Step 5: Copy Your Google Credentials
1. A popup will appear with your credentials
2. **COPY AND SAVE:**
   - **Client ID** (looks like: `123456789-abcdefghijk.apps.googleusercontent.com`)
   - **Client Secret** (looks like: `GOCSPX-abc123xyz`)
3. You can close the popup

---

## 🔵 FACEBOOK OAUTH SETUP

### Step 1: Go to Facebook Developers
1. Open browser and go to: https://developers.facebook.com/
2. Sign in with your Facebook account (create one if you don't have it)

### Step 2: Create a New App
1. Click **"My Apps"** (top right)
2. Click **"Create App"**
3. Select **"Consumer"** → Click **"Next"**
4. Fill in the form:
   - **App Name:** `FruitWeb`
   - **App Contact Email:** your email
   - **App Purpose:** Select "Increase app installs or engagement" 
   - **App Type:** Choose any category
5. Click **"Create App"**
6. Complete the security check if asked

### Step 3: Add Facebook Login Product
1. You'll see your app dashboard
2. Find the **"Products"** section
3. Search for **"Facebook Login"**
4. Click **"Set Up"** on Facebook Login
5. Choose **"Web"**
6. For the website URL, enter: `http://localhost:8000`
7. Click **"Next"** through the wizard
8. Click **"Continue as Developer"** when finished

### Step 4: Configure App Settings
1. Go to **"Settings"** → **"Basic"** (left sidebar)
2. **SAVE THESE:**
   - **App ID** (number at top)
   - **App Secret** (click "Show" to reveal it)
3. Scroll down to **"App Domains"**
4. Click **"Add Domain"**
5. Add: `localhost:8000`
6. Add another: `localhost`
7. Click **"Save Changes"**

### Step 5: Configure OAuth Redirect URLs
1. Go to **"Facebook Login"** → **"Settings"** (left sidebar)
2. Scroll to **"Valid OAuth Redirect URIs"**
3. Click **"Add URI"**
4. Add this URL: `http://localhost:8000/auth/facebook/callback`
5. Add another: `http://localhost/fruit2web/public/auth/facebook/callback`
6. Click **"Save Changes"**

---

## ✅ Your Credentials Summary

Once you have gathered all credentials, you should have:

```
GOOGLE_CLIENT_ID = [your google client id]
GOOGLE_CLIENT_SECRET = [your google client secret]
FACEBOOK_CLIENT_ID = [your facebook app id]
FACEBOOK_CLIENT_SECRET = [your facebook app secret]
```

---

## 📝 Next Step

Once you have all 4 credentials, reply with them in this format:

```
Google Client ID: [paste here]
Google Client Secret: [paste here]
Facebook App ID: [paste here]
Facebook App Secret: [paste here]
```

I will add them to your `.env` file automatically!

