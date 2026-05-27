# Server Startup Checklist

## ⚠️ AI agents: Allowed to run these commands
> AI agents **may** run the commands in this file when explicitly asked to start the server.
> However, the agent **must** first confirm with the user that:
> 1. All file edits are complete and saved
> 2. The user is ready for the server to start
> 3. No other server instances are running
>
> **Caveats for AI agents:**
> - Do NOT run `npm run dev` (hot-reload mode) — only `npm run build` (production build)
> - Do NOT run `php artisan serve` in the background and continue editing — the server ties up the terminal
> - Always kill leftover processes first to avoid port conflicts
> - Always delete `public/hot` if it exists to prevent stale Vite references
> - After the server starts, inform the user it's running and how to terminate it

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

## One-liner (all steps)
Copy-paste this into PowerShell to do everything at once:
```powershell
Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force; Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force; if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }; $env:Path = "C:\xampp\php;$env:Path"; php artisan view:clear; php artisan config:clear; npm run build; php artisan serve
```

## Terminating the server
Press **`Ctrl + C`** in the terminal where the server is running, then run:
```powershell
Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force; Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force; if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }
```

## For dev mode (with hot reload) — HUMAN ONLY
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