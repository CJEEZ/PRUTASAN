# Deploying PRUTEXPRES to Render

## Prerequisites
- GitHub account with your repository pushed
- Render account (free at render.com)

## Deployment Steps

### 1. Prepare Your Git Repository
```powershell
cd c:\Xamppp\htdocs\PRUTEXPRES

# Initialize/reinit git if needed
git init
git add .
git commit -m "Initial commit for Render deployment"
```

### 2. Push to GitHub
- Create a new repository on GitHub
- Add remote: `git remote add origin https://github.com/YOUR_USERNAME/PRUTEXPRES.git`
- Push: `git branch -M main && git push -u origin main`

### 3. Deploy on Render
1. Go to https://render.com
2. Sign up (free account)
3. Click "**+ New**" → "**Web Service**"
4. Connect your GitHub account and select your PRUTEXPRES repository
5. Configure deployment:
   - **Name**: `prutexpres` (or your preferred name)
   - **Runtime**: PHP
   - **Build Command**: `composer install && php artisan migrate --force`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

### 4. Set Environment Variables
In Render dashboard, add these environment variables:

| Key | Value |
|-----|-------|
| `APP_KEY` | Generate from `php artisan key:generate` locally, copy output |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `LOG_CHANNEL` | `stderr` |
| `DB_CONNECTION` | `pgsql` |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_DRIVER` | `file` |

**Database Variables**: These will be auto-populated if using Render's PostgreSQL

### 5. Add PostgreSQL Database
1. In Render dashboard → "**+ New**" → "**PostgreSQL**"
2. Create database (free plan available)
3. Render will auto-populate DB environment variables

### 6. Deploy
- Click "**Create Web Service**"
- Render will automatically:
  - Clone your repository
  - Install dependencies
  - Run migrations
  - Start your app

## Post-Deployment

### Generate App Key
If not set during deployment:
1. SSH into Render instance (via dashboard)
2. Run: `php artisan key:generate`

### Run Migrations
```bash
# Via Render shell:
php artisan migrate --force
```

### View Logs
- Dashboard → Select service → "**Logs**" tab

## Troubleshooting

### Build Fails
- Check logs in Render dashboard
- Ensure `composer.lock` is committed to git
- Verify PHP extensions in `composer.json`

### Database Connection Issues
- Verify `DB_*` environment variables in dashboard
- Ensure PostgreSQL service is created
- Check that app has permission to create tables

### App Won't Start
- Check "Start Command" in settings
- Verify `Procfile` format
- Look for errors in logs (click service → Logs)

## Recommended: Use render.yaml

Instead of manual setup, Render can read `render.yaml` from your repo root for automatic configuration. This file is already created in your project.

## Cost
- **Free tier**: 0.1 vCPU, 512 MB RAM per service
- **PostgreSQL**: 0.5 GB free storage
- Sufficient for small-medium projects
- Paid plans available if you scale

## Next Steps
1. Push your code to GitHub
2. Connect Render to GitHub
3. Deploy and monitor in Render dashboard
