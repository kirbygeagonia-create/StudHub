# StudHub — Run & Termination Guide

## Requirements

- **PHP 8.2+** at `C:\xampp\php\php.exe`
- **Composer** at `C:\xampp\php\composer`
- **Node.js** + npm
- **Database:** SQLite (default, zero-config for dev) or MySQL

## Quick Start (Dev)

```powershell
# 1. Open terminal in the project folder
cd "C:\Users\ADMIN\OneDrive\Desktop\Another_Project"

# 2. Add PHP to PATH for this session
$env:Path = "C:\xampp\php;$env:Path"

# 3. Install PHP dependencies
php composer install

# 4. Copy .env (skip if .env already exists)
copy .env.example .env

# 5. Generate app key (skip if already set)
php artisan key:generate

# 6. Run database migrations
php artisan migrate

# 7. Seed SEAIT data (school, colleges, programs, subjects)
php artisan db:seed

# 8. Build frontend assets (CSS/JS)
npm install
npm run build

# 9. Start the dev server
php artisan serve --port=8000
```

Then open **http://127.0.0.1:8000** in your browser.

### Dev login credentials (after seeding `DevUsersSeeder`)

| Role | Email | Password |
|------|-------|----------|
| Student | `student@seait.edu.ph` | `password` |
| Moderator | `moderator@seait.edu.ph` | `password` |
| Admin | `admin@seait.edu.ph` | `password` |

> If `DevUsersSeeder` hasn't been run: `php artisan db:seed --class=DevUsersSeeder`

## Running Tests

```powershell
$env:Path = "C:\xampp\php;$env:Path"

# All tests (SQLite in-memory, no MySQL needed)
php vendor/bin/pest

# Specific test file
php vendor/bin/pest tests/Feature/Chat/ChatAccessTest.php

# Filter by name
php vendor/bin/pest --filter="SmokeTest"
```

## Running Linters

```powershell
$env:Path = "C:\xampp\php;$env:Path"

# PHPStan Level 6
php vendor/bin/phpstan analyse --level 6 app/ --memory-limit=1G

# Laravel Pint (code style) — dry run
php vendor/bin/pint --test

# Laravel Pint — auto-fix
php vendor/bin/pint
```

## CI Pipeline (local simulation)

```powershell
$env:Path = "C:\xampp\php;$env:Path"
php composer run ci
# Runs: pint --test → phpstan → pest
```

## How to Terminate the System

### Stop the dev server

```powershell
# Find the PHP process
Get-Process -Name "php" | Format-Table Id, ProcessName, StartTime

# Kill it
Stop-Process -Name "php" -Force
```

Or press **Ctrl+C** in the terminal window where `php artisan serve` is running.

### Stop background PHP processes (if started via PowerShell `Start-Process`)

```powershell
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
```

### Clean up

```powershell
# Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Reset database (optional — destroys all data)
php artisan migrate:fresh
php artisan db:seed
```

## Dev Watch Mode (for editing CSS/JS)

Instead of `npm run build`, use:

```powershell
npm run dev
```

This starts Vite HMR — changes to `resources/css/*.css` or `resources/js/*.js` will hot-reload in the browser without a manual rebuild.

## Switching to MySQL

In `.env`, comment out SQLite and uncomment MySQL:

```env
# DB_CONNECTION=sqlite
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studhub
DB_USERNAME=studhub
DB_PASSWORD=studhub
```

Then run:
```powershell
php artisan migrate:fresh
php artisan db:seed
```

## Common Issues

| Problem | Fix |
|---------|-----|
| "Pusher key" error in console | Already fixed — Echo lazily loads only when Reverb env vars are set |
| "Got it" button unresponsive | Run `npm run build` after pulling latest code |
| Livewire components not working | Ensure `@livewireStyles` and `@livewireScripts` are in layout (they are) |
| Blank page / 500 error | Run `php artisan view:clear` and `npm run build` |
| "intl" extension error | Install PHP `intl` extension or avoid `Number::ordinal()` |