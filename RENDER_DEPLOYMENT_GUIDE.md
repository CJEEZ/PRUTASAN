# Deploying PRUTEXPRES to Render

## Prerequisites
- GitHub account with your repository pushed
- Render account at render.com
- A paid Render web service and PostgreSQL database, because free services do not provide durable application storage

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
  - **Runtime**: Docker
  - **Dockerfile**: `./Dockerfile`
  - **Plan**: Starter or another paid plan with a persistent disk

### 4. Set Environment Variables
In Render dashboard, add these environment variables:

| Key | Value |
|-----|-------|
| `APP_KEY` | Generate from `php artisan key:generate` locally, copy output |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | Your Render service URL, including `https://` |
| `LOG_CHANNEL` | `stderr` |
| `DB_CONNECTION` | `pgsql` |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `public` for temporary uploads, or configure S3 for durable production storage |

**Database Variables**: These will be auto-populated if using Render's PostgreSQL

### 5. Add PostgreSQL Database
1. In Render dashboard → "**+ New**" → "**PostgreSQL**"
2. Create a paid database, such as the `Basic-256MB` plan
3. Render will auto-populate DB environment variables

The `render.yaml` blueprint also creates a 1 GB persistent disk mounted at
`/var/www/html/storage/app/public`. This keeps profile photos and product
thumbnails across web-service deploys. Increase the disk size or use S3 if
the application will store more than 1 GB.

### 6. Deploy
- Click "**Create Web Service**"
- Render will automatically:
  - Clone your repository
  - Build the Docker image with PHP, Composer, Node, and frontend assets
  - Run migrations when the container starts
  - Create the public storage link when the container starts
  - Start Apache with Laravel's `public` directory as the document root

## Post-Deployment

### Generate App Key
Generate an application key privately with `php artisan key:generate --show`, then add it as the `APP_KEY` secret in Render. Never commit it to `render.yaml` or `.env`.

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

## Persistence and upgrades

- Never delete and recreate `prutexpres-db` during a deploy. Upgrade the existing
  database in Render if it is still on the free plan; changing the plan must
  preserve that database.
- Keep the same `APP_KEY` in Render. Changing it invalidates sessions and
  encrypted cookies, although it does not delete account rows.
- The database contains accounts, orders, products, and other application data.
  The persistent disk contains uploaded files. Both resources must remain
  attached to the same service/database for a redeploy to preserve data.
- Before changing infrastructure, create a database backup from the Render
  dashboard and verify it can be restored.

## Cost

The web service, persistent disk, and PostgreSQL database use paid Render
resources. Their exact prices depend on the selected plans and current Render
pricing.

## Next Steps
1. Push your code to GitHub
2. Connect Render to GitHub
3. Deploy and monitor in Render dashboard
