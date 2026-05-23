# StudHub — How to Run & Terminate

## Prerequisites

Before running the system, verify you have these installed:

```powershell
php --version       # PHP 8.2.12 at C:\xampp\php\php.exe
node --version      # Node v24.13.0+ needed for Vite
npm --version       # npm 11.14.1+
```

---

## 1. First-Time Setup (already done for you)

```powershell
# Install PHP dependencies
$env:Path = "C:\xampp\php;$env:Path"
& "C:\xampp\php\php.exe" "C:\xampp\php\composer" install --no-interaction

# Install frontend dependencies & build
npm install
npm run build

# Copy .env if not already present
if (!(Test-Path ".env")) { Copy-Item ".env.example" ".env" }

# Generate app key & create SQLite database
php artisan key:generate
New-Item -ItemType File -Path "database\database.sqlite" -Force
php artisan migrate --force
php artisan db:seed --force
```

---

## 2. Running the System

### 2a. Start the Dev Server (HTTP only — simplest)

```powershell
$env:Path = "C:\xampp\php;$env:Path"
php artisan serve
```

The app will be available at **http://localhost:8000**

> Press `Ctrl+C` in the terminal to stop.

### 2b. Start with Live Frontend Reloading (recommended for development)

Open **two separate PowerShell terminals**:

**Terminal 1** — Laravel backend:
```powershell
$env:Path = "C:\xampp\php;$env:Path"
php artisan serve
```

**Terminal 2** — Vite frontend (rebuilds CSS/JS on file changes):
```powershell
npm run dev
```

### 2c. Start with WebSocket Chat (full stack)

If you need real-time chat with Reverb, open **three terminals**:

**Terminal 1** — Laravel backend:
```powershell
$env:Path = "C:\xampp\php;$env:Path"
php artisan serve
```

**Terminal 2** — Vite + Reverb listener:
```powershell
npm run dev
```

**Terminal 3** — Reverb WebSocket server:
```powershell
$env:Path = "C:\xampp\php;$env:Path"
php artisan reverb:start
```

---

## 3. Login Credentials (local dev SQLite)

| Role       | Email               | Password   |
|------------|---------------------|------------|
| Student    | `test@seait.edu.ph` | `password` |
| Moderator  | `mod@seait.edu.ph`  | `password` |
| Admin      | `admin@seait.edu.ph`| `password` |

> Database is at `database/database.sqlite` — no MySQL needed for local dev.

---

## 4. How to Terminate

### Stop the dev server(s):
Press **`Ctrl+C`** in each open terminal window.

### Kill leftover PHP processes (if any hang):
```powershell
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
```

### Kill leftover Node processes:
```powershell
Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force
```

---

## 5. Running Tests

```powershell
$env:Path = "C:\xampp\php;$env:Path"

# Full test suite (SQLite in-memory, no database needed)
php vendor/bin/pest

# Compact output
php vendor/bin/pest --compact

# Filter by test name
php vendor/bin/pest --filter="Moderation"

# Single test file
php vendor/bin/pest tests/Feature/Reputation/KarmaTest.php
```

---

## 6. Running the Linters

```powershell
$env:Path = "C:\xampp\php;$env:Path"

# Code style (PSR-12)
php vendor/bin/pint --test        # Check only
php vendor/bin/pint               # Auto-fix

# Static analysis (Level 6)
php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
```

---

## 7. Quick Reference — All in One

```powershell
# Start, test, and terminate — the full cycle:

# 1. START
Start-Process powershell -ArgumentList "-NoExit", "-Command `"`$env:Path='C:\xampp\php;`$env:Path'; php artisan serve`"" 
Start-Process powershell -ArgumentList "-NoExit", "-Command `"npm run dev`""

# 2. TEST (in another terminal)
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest --compact

# 3. TERMINATE
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force
```

---

## 8. URL Map

| Page           | URL                                   |
|----------------|---------------------------------------|
| Home / Welcome | http://localhost:8000                 |
| Login          | http://localhost:8000/login           |
| Register       | http://localhost:8000/register        |
| Dashboard      | http://localhost:8000/dashboard       |
| Chat           | http://localhost:8000/chat            |
| Resources      | http://localhost:8000/resources       |
| Requests       | http://localhost:8000/requests        |
| Leaderboard    | http://localhost:8000/leaderboard     |
| Lends          | http://localhost:8000/lends           |
| My Shelf       | http://localhost:8000/my-shelf        |
| Profile        | http://localhost:8000/profile         |
| Admin          | http://localhost:8000/admin           |
| Moderation     | http://localhost:8000/moderation      |
| Health Check   | http://localhost:8000/up              |

---

## 9. Troubleshooting

| Problem | Fix |
|---------|-----|
| `php artisan` not found | Add PHP to PATH first: `$env:Path = "C:\xampp\php;$env:Path"` |
| Vite fails to start | Run `npm install && npm run build` first |
| Test database errors | Delete `database/database.sqlite` and re-run `php artisan migrate --force` |
| "Class not found" | Run `$env:Path = "C:\xampp\php;$env:Path"; & "C:\xampp\php\php.exe" "C:\xampp\php\composer" dump-autoload` |
| `npm run dev` fails | Run `npm ci` to clean-install, then `npm run build` |