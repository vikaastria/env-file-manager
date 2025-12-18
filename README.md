# Env File Manager

A secure Laravel Livewire application for managing environment (.env) files across multiple projects.

## Features

- **Secure Storage**: All environment files are encrypted using Laravel's built-in encryption before storage
- **User Authentication**: Laravel Breeze with Livewire stack for secure authentication
- **Access Control**: Policy-based authorization ensuring users can only access their own files
- **Environment Tags**: Organize files by environment (Development, Staging, Production)
- **Easy Management**: Create, edit, view, and download .env files
- **Modern UI**: Built with Tailwind CSS and dark mode support
- **Latest Stack**: Laravel 12 + Livewire v3

## Security Features

1. **Encryption at Rest**: All env file contents are encrypted using `Crypt::encryptString()` before database storage
2. **Authorization Policies**: Comprehensive policy system prevents unauthorized access
3. **CSRF Protection**: Built-in Laravel CSRF protection on all forms
4. **Input Validation**: All user inputs are validated and sanitized
5. **Authentication Required**: All env file routes require authentication and email verification
6. **Secure Headers**: Laravel's security headers are enabled by default

## Installation

1. Clone the repository:
```bash
git clone https://github.com/vikaastria/env-file-manager.git
cd env-file-manager
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=env_file_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations:
```bash
php artisan migrate
```

8. Build assets:
```bash
npm run build
```

## Development

Run the development server:
```bash
php artisan serve
```

And in a separate terminal, run Vite:
```bash
npm run dev
```

Visit `http://localhost:8000` in your browser.

## Usage

1. **Register/Login**: Create an account or login to access the application
2. **Create Env File**: Click "Create New Env File" and fill in the details
3. **View Files**: Browse your saved env files with search and filter options
4. **Edit**: Update env files as needed
5. **Download**: Download env files for use in your projects
6. **Delete**: Remove env files you no longer need

## Tech Stack

- **Laravel 12**: Latest Laravel framework
- **Livewire v3**: For reactive components
- **Laravel Breeze**: Authentication scaffolding
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Minimal JavaScript framework
- **MySQL/PostgreSQL**: Database support

## Security Considerations

⚠️ **Important Security Notes**:

1. Keep your `APP_KEY` secure - this is used to encrypt env file contents
2. Use HTTPS in production to protect data in transit
3. Regularly backup your database
4. Keep Laravel and dependencies up to date
5. Use strong passwords for user accounts
6. Consider implementing 2FA for additional security

## License

Open-source software licensed under the MIT license.

## Support

For issues, questions, or contributions, please use the GitHub issue tracker.
