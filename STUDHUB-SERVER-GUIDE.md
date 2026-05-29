# 🚀 StudHub — Server Startup & System Check Guide

> Copy-paste these commands in order. All paths assume you're in the project root.

---

## 🛑 Stop everything (always first)
```powershell
Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }
```

---

## 🧹 Clean up & cache
```powershell
$env:Path = "C:\xampp\php;$env:Path"
php artisan view:clear
php artisan config:clear
```

---

## 🏗️ Build frontend assets
```powershell
npm run build
```

---

## ▶️ Start the server
```powershell
php artisan serve
```
> Open **http://127.0.0.1:8000** in your browser.

---

## 🧪 Run tests
```powershell
$env:Path = "C:\xampp\php;$env:Path"
php vendor/bin/pest
```

## 🔍 Run PHPStan (static analysis)
```powershell
$env:Path = "C:\xampp\php;$env:Path"
php vendor/bin/phpstan analyse --level=6
```

---

## ⚡ Quick one-liner (full restart)
```powershell
Get-Process -Name "node","php" -ErrorAction SilentlyContinue | Stop-Process -Force; if (Test-Path "public/hot") { Remove-Item "public/hot" -Force }; $env:Path = "C:\xampp\php;$env:Path"; php artisan view:clear; php artisan config:clear; npm run build; php artisan serve
```

---

## 📋 Common pitfalls

| Symptom | Cause | Fix |
|---------|-------|-----|
| Double-click needed on buttons | Stale `public/hot` file pointing to dead Vite server | Delete `public/hot` |
| 8px space above nav bar | Vite HMR error overlay from dead dev server | Delete `public/hot` |
| Nav not sticky | Vite HMR overrides CSS `position` | Delete `public/hot` |
| 24-48px space above nav | UTF-8 BOM in blade files | Run `php check_bom.php` |
| Slow page loads | Browser waiting for dead Vite WebSocket | Delete `public/hot` |