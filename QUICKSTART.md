# Quick Start Guide

Get up and running with Env File Manager in minutes!

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- Database (MySQL, PostgreSQL, or SQLite)

## Installation Steps

### 1. Clone and Install Dependencies

```bash
# Clone the repository
git clone https://github.com/vikaastria/env-file-manager.git
cd env-file-manager

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Database

Edit `.env` file with your database settings:

**For SQLite (easiest for development):**
```env
DB_CONNECTION=sqlite
# DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD can be commented out
```

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=env_file_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations

```bash
# Create database tables
php artisan migrate
```

### 5. Build Assets

```bash
# For development
npm run dev

# OR for production
npm run build
```

### 6. Start the Application

```bash
# Start Laravel development server
php artisan serve
```

Visit: `http://localhost:8000`

## First Steps

### 1. Register an Account
- Click "Register" on the welcome page
- Fill in your name, email, and password
- You'll be automatically logged in

### 2. Explore the Dashboard
- View feature overview
- Click "Manage Your Env Files" to get started

### 3. Create Your First Env File
- Click "Create New Env File"
- Fill in the details:
  - **Name**: A descriptive name (e.g., "My App Production")
  - **Project Name**: Your project identifier (e.g., "my-app")
  - **Environment**: Development, Staging, or Production
  - **Description**: Optional notes about this env file
  - **Content**: Paste your env file content
- Click "Create Env File"

### 4. Manage Env Files
- **View**: Click the eye icon to view env file details
- **Edit**: Click the pencil icon to update content
- **Delete**: Click the trash icon to remove (with confirmation)
- **Download**: From the view page, download the .env file
- **Search**: Filter by name, project, or description
- **Filter**: Filter by environment type

## Development Workflow

### Running in Development Mode

Terminal 1 - Laravel Server:
```bash
php artisan serve
```

Terminal 2 - Asset Compilation:
```bash
npm run dev
```

### Making Changes

**Backend (PHP/Laravel):**
- Models: `app/Models/`
- Livewire Components: `app/Livewire/`
- Routes: `routes/web.php`
- Migrations: `database/migrations/`

**Frontend (Blade/Tailwind):**
- Views: `resources/views/`
- Livewire Views: `resources/views/livewire/`
- Styles: `resources/css/app.css`
- JavaScript: `resources/js/app.js`

### Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=EnvFileTest
```

## Production Deployment

### 1. Environment Setup

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use a strong, random APP_KEY
# BACKUP YOUR APP_KEY - encrypted data cannot be recovered without it!
```

### 2. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Build production assets
npm run build
```

### 3. Set Permissions

```bash
# Storage and cache directories need write permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Configure Web Server

**Nginx Example:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/env-file-manager/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Common Tasks

### Reset Database
```bash
php artisan migrate:fresh
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Create New User (via Tinker)
```bash
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
```

## Troubleshooting

### Issue: "No application encryption key has been specified"
**Solution:**
```bash
php artisan key:generate
```

### Issue: "SQLSTATE[HY000] [2002] No such file or directory"
**Solution:** Check database configuration in `.env`

### Issue: Assets not loading
**Solution:**
```bash
npm run build
php artisan storage:link
```

### Issue: Permission denied errors
**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
```

## Next Steps

- Read the full [README.md](README.md) for detailed information
- Review [SECURITY.md](SECURITY.md) for security best practices
- Customize the application for your needs
- Set up automated backups
- Configure email settings for notifications
- Consider implementing 2FA for enhanced security

## Support

For issues or questions:
- Check existing GitHub issues
- Create a new issue with detailed information
- Include error messages and steps to reproduce

Happy env file managing! 🎉
