# Free Hosting Deployment - Research Collaboration Platform

## Option 1: InfinityFree (Recommended)
**URL: infinityfree.net**
- Sign up free
- Create website
- Upload files via File Manager

## Option 2: 000webhost
**URL: 000webhost.com** 
- Sign up free
- Create website
- Upload via File Manager

## Option 3: Heroku (Advanced)
**URL: heroku.com**
- Free tier available
- Requires Git deployment

## Quick Deploy Steps:

### 1. Sign up at InfinityFree.net
- Create account
- Add new website
- Note your database details

### 2. Upload Files
Upload these files to `htdocs/` or `public_html/`:
```
- index.php
- dashboard.php  
- browse.php
- post_details.php
- setup.php
- css/style.css
- js/script.js
- app/ (entire folder)
- database/schema.sql
```

### 3. Create Database
- Go to MySQL Databases
- Create database: `research_collab`
- Import `database/schema.sql`

### 4. Update Config
Edit `app/config/database.php`:
```php
define('DB_HOST', 'sql200.infinityfree.com'); // Your host
define('DB_USER', 'if0_12345678');            // Your username
define('DB_PASS', 'your_password');           // Your password  
define('DB_NAME', 'if0_12345678_research');   // Your database
```

### 5. Test
Visit: `http://yoursite.infinityfreeapp.com/setup.php`

**Your friends access:** `http://yoursite.infinityfreeapp.com`