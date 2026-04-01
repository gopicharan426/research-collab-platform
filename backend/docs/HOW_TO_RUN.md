# How to Run Research Collaboration Platform

## Quick Start (3 Steps)

### Step 1: Start MySQL
- Open **XAMPP Control Panel**
- Click **Start** next to **MySQL**
- Wait for green indicator

### Step 2: Start PHP Server
Open **Command Prompt** and run:
```bash
cd C:\xampp\htdocs\research-collab-platform\frontend
C:\xampp\php\php.exe -S localhost:8080
```

### Step 3: Open Browser
- Go to: **http://localhost:8080**
- Login with:
  - Email: `test@example.com`
  - Password: `password123`

---

## Stop the Project

Press **Ctrl + C** in Command Prompt

---

## Troubleshooting

### "Connection Refused" Error
- MySQL not running → Start XAMPP MySQL
- PHP server not running → Run Step 2 again

### "Column not found: is_admin"
Run this SQL in phpMyAdmin:
```sql
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;
UPDATE users SET is_admin = 1 WHERE email = 'test@example.com';
```

### Port 8080 Already in Use
Use different port:
```bash
C:\xampp\php\php.exe -S localhost:8000
```
Then open: **http://localhost:8000**

---

## First Time Setup

### 1. Import Database
- Open: **http://localhost/phpmyadmin**
- Create database: `research_collab`
- Import files in order:
  1. `backend/database/schema.sql`
  2. `backend/database/add_likes_views.sql`
  3. `backend/database/add_admin.sql`
  4. `backend/database/add_reset_tokens.sql`

### 2. Update Paths (If Needed)
If using frontend folder, update paths in PHP files:

**Change:**
```php
require_once '../app/auth/auth.php';
```

**To:**
```php
require_once '../backend/app/auth/auth.php';
```

---

## Project URLs

- **Home:** http://localhost:8080
- **Login:** http://localhost:8080/login.php
- **Dashboard:** http://localhost:8080/dashboard.php
- **phpMyAdmin:** http://localhost/phpmyadmin

---

## Test Account

**Email:** test@example.com  
**Password:** password123  
**Role:** Admin (can delete any post)

---

## Keep Command Prompt Open

Don't close the Command Prompt window while using the app!
