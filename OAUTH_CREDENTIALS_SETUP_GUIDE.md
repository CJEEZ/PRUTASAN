# Complete OAuth Setup Guide: Google & Facebook

This guide will walk you through getting your OAuth credentials step-by-step.

---

## 🔵 Part 1: Google OAuth Setup

### Step 1: Go to Google Cloud Console
1. Open your browser and go to: **https://console.cloud.google.com/**
2. Sign in with your Google account (create one if needed)

### Step 2: Create a New Project
1. At the top, click the project dropdown (currently says "Select a project")
2. Click **"NEW PROJECT"** button
3. Enter project name: `FruitWeb` (or any name you prefer)
4. Click **"CREATE"**
5. Wait 1-2 minutes for the project to be created
6. Once created, select the project from the dropdown

### Step 3: Enable Google+ API
1. In the left sidebar, click **"APIs & Services"** → **"Library"**
2. In the search box at the top, type: `Google+ API`
3. Click on **"Google+ API"** from the results
4. Click the blue **"ENABLE"** button
5. Wait for it to enable (you'll see a loading spinner)

### Step 4: Create OAuth Credentials
1. Go back to **"APIs & Services"** → **"Credentials"** (left sidebar)
2. Click **"+ CREATE CREDENTIALS"** button (top of page)
3. Select **"OAuth client ID"** from the dropdown
4. **If prompted for "OAuth consent screen":**
   - Click **"CONFIGURE CONSENT SCREEN"**
   - Select **"External"** user type
   - Click **"CREATE"**
   - Fill in the form:
     - **App name:** FruitWeb
     - **User support email:** your-email@gmail.com
     - **Developer contact:** your-email@gmail.com
   - Click **"SAVE AND CONTINUE"**
   - Skip "Scopes" and "Optional info" sections
   - Click **"SAVE AND CONTINUE"**
   - Review and click **"BACK TO DASHBOARD"**

5. Now create the OAuth credential again:
   - Click **"+ CREATE CREDENTIALS"** → **"OAuth client ID"**
   - Select **"Web application"**
   - Give it a name: `FruitWeb Web Client`
   - Under **"Authorized redirect URIs"**, click **"ADD URI"**
   - Add these URIs:
     ```
     http://localhost:8000/auth/google/callback
     http://127.0.0.1:8000/auth/google/callback
     ```
   - Click **"CREATE"**

### Step 5: Copy Your Credentials
1. A popup will appear showing your credentials
2. **Copy these two values:**
   - `Client ID` (looks like: `xxxxx.apps.googleusercontent.com`)
   - `Client Secret` (looks like a long random string)
3. Keep this window open or write them down safely

**Your Google Credentials are:**
```
GOOGLE_CLIENT_ID = [Copy the Client ID]
GOOGLE_CLIENT_SECRET = [Copy the Client Secret]
```

---

## 🔴 Part 2: Facebook OAuth Setup

### Step 1: Go to Facebook Developers
1. Open your browser and go to: **https://developers.facebook.com/**
2. Sign in with your Facebook account (create one if needed)

### Step 2: Create a New App
1. In the top right, click **"My Apps"** dropdown
2. Click **"Create App"**
3. Choose app type: **"Consumer"**
4. Click **"Next"**
5. Fill in the form:
   - **App Name:** FruitWeb (or any name)
   - **App Contact Email:** your email
   - **App Purpose:** Select "Build an app" or appropriate category
   - **App ID:** Leave blank (auto-generated)
6. Click **"Create App"**

### Step 3: Add Facebook Login Product
1. You're now in your app dashboard
2. Click **"+ Add Product"** (on the left side)
3. Find **"Facebook Login"** and click **"Set Up"**
4. Choose **"Web"** (since you're building a web app)
5. You can skip the quick start, click **"Settings"** instead

### Step 4: Configure App Settings
1. Go to **"Settings"** → **"Basic"** (left sidebar, under Facebook Login)
2. **Note down these values:**
   - **App ID** (at the top of the page)
   - **App Secret** (click to reveal, it's hidden by default)
3. Keep this page open

### Step 5: Add App Domains
1. Stay on **"Settings"** → **"Basic"**
2. Scroll down to **"App Domains"**
3. Click in the text field and add:
   ```
   localhost:8000
   127.0.0.1:8000
   ```
4. Click **"Save Changes"**

### Step 6: Configure OAuth Redirect URIs
1. Go to **"Facebook Login"** → **"Settings"** (left sidebar)
2. Under **"Valid OAuth Redirect URIs"**, add:
   ```
   http://localhost:8000/auth/facebook/callback
   http://127.0.0.1:8000/auth/facebook/callback
   ```
3. Click **"Save Changes"**

### Step 7: Copy Your Credentials
**Your Facebook Credentials are:**
```
FACEBOOK_CLIENT_ID = [Copy the App ID from Basic Settings]
FACEBOOK_CLIENT_SECRET = [Copy the App Secret from Basic Settings]
```

---

## ✅ Part 3: Add Credentials to Your Project

### Step 1: Find Your `.env` File
1. Open File Explorer on Windows
2. Navigate to: `C:\Xamppp\htdocs\fruit2web\`
3. Look for a file named **`.env`** (it might be hidden)
   - If you don't see it, press `Ctrl + H` to show hidden files

### Step 2: Edit the `.env` File
1. Right-click on `.env` → **"Open with"** → **"Notepad"** (or your favorite text editor)
2. Find these lines (they might already exist):
   ```env
   GOOGLE_CLIENT_ID=
   GOOGLE_CLIENT_SECRET=
   FACEBOOK_CLIENT_ID=
   FACEBOOK_CLIENT_SECRET=
   ```

3. Fill them in with your credentials:
   ```env
   GOOGLE_CLIENT_ID=your_google_client_id_here
   GOOGLE_CLIENT_SECRET=your_google_client_secret_here
   FACEBOOK_CLIENT_ID=your_facebook_app_id_here
   FACEBOOK_CLIENT_SECRET=your_facebook_app_secret_here
   ```

4. Save the file (Ctrl + S)

### Step 3: Clear Laravel Cache
1. Open PowerShell or Command Prompt
2. Navigate to your project:
   ```bash
   cd C:\Xamppp\htdocs\fruit2web
   ```
3. Run this command:
   ```bash
   php artisan config:clear
   ```

---

## 🎉 Part 4: Test Your Setup

1. Start your Laravel development server:
   ```bash
   php artisan serve
   ```

2. Go to: **http://localhost:8000/login**

3. You should see **Google** and **Facebook** login buttons

4. Click either button to test!

---

## ❓ Troubleshooting

### "Invalid redirect URI" Error
- Make sure your redirect URIs in Google/Facebook settings exactly match the ones in this guide
- Restart your Laravel server after updating `.env`

### "Cannot find Google/Facebook buttons"
- Clear your browser cache (Ctrl + Shift + Delete)
- Run `php artisan config:clear`

### "App not verified" Facebook Warning
- This is normal during development
- Click **"Continue"** to proceed
- To remove it, you need to submit your app for review (for production)

---

## 📝 Summary Checklist

- [ ] Got Google Client ID and Secret
- [ ] Got Facebook App ID and Secret
- [ ] Added all 4 credentials to `.env` file
- [ ] Ran `php artisan config:clear`
- [ ] Tested login buttons on your site

**You're all set!** 🚀
