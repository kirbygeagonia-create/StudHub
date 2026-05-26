# Server Startup Checklist

## ⚠️ IMPORTANT: AI agents must NOT run these commands
> The `planning/studhub-improvements-v2.md` guide explicitly states:
> **"Do not run npm, vite, or artisan commands — only file writes."**
> Running `npm run build`, `php artisan serve`, or any `php artisan` command
> during a UI-only file-edit session can cause unintended side effects.
> **Only a human should start the server.** AI agents should stop here after file edits.

## Before starting any server, ALWAYS:

### 1. Kill leftover processes
```powershell
Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
```

### 2. Remove stale Vite `hot` file
```powershell
if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }
```
> The `public/hot` file is created by `npm run dev` and tells Laravel to use the Vite dev server.
> If you kill the Vite process without deleting this file, the app will try to load scripts from a dead server.
> This causes: double-click issues, sticky nav breaking, 8px space above nav, and slow page loads.

### 3. Clear Laravel caches
```powershell
$env:Path = "C:\xampp\php;$env:Path"
php artisan view:clear
php artisan config:clear
```

### 4. Build frontend (if not using dev mode)
```powershell
npm run build
```

### 5. Start server
```powershell
php artisan serve
```

## For dev mode (with hot reload):
```powershell
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```
> When done, kill both processes AND delete `public/hot` before switching to production mode.

## Common pitfalls
| Symptom | Cause | Fix |
|---------|-------|-----|
| Double-click needed on buttons | Stale `public/hot` file pointing to dead Vite server | Delete `public/hot` |
| 8px space above nav bar | Vite HMR error overlay from dead dev server | Delete `public/hot` |
| Nav not sticky | Vite HMR overrides CSS `position` | Delete `public/hot` |
| 24-48px space above nav | UTF-8 BOM in blade files | Check with `check_bom.php` script |
| Slow page loads | Browser waiting for dead Vite WebSocket | Delete `public/hot` |