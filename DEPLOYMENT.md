# Online Hosting Deployment Guide

## Step 1: Choose Free Hosting Provider
**Recommended: 000webhost.com**
- Sign up at https://www.000webhost.com
- Create new website
- Note your database details

## Step 2: Upload Files
1. Download FileZilla or use hosting file manager
2. Upload ALL files from `public/` folder to `public_html/`
3. Upload `app/` folder to root directory
4. Upload `database/` folder to root directory

## Step 3: Setup Database
1. Go to hosting control panel → MySQL Databases
2. Create database: `research_collab`
3. Import `database/schema.sql` via phpMyAdmin
4. Note database credentials

## Step 4: Update Configuration
1. Open `app/config/database.php`
2. Replace with your hosting database details:
```php
define('DB_HOST', 'sql123.000webhost.com');     // Your DB host
define('DB_USER', 'id12345_username');          // Your DB username  
define('DB_PASS', 'your_password');             // Your DB password
define('DB_NAME', 'id12345_research_collab');   // Your DB name
```

## Step 5: Test
- Visit: `https://yoursite.000webhostapp.com`
- Login: test@example.com / password123

## File Structure for Upload:
```
public_html/
├── index.php
├── dashboard.php
├── browse.php
├── post_details.php
├── setup.php
├── css/style.css
└── js/script.js

Root Directory/
├── app/
├── database/
└── README.md
```

## Alternative Hosting Options:
- **InfinityFree.net** - Free hosting
- **Heroku** - Free tier with ClearDB
- **Railway** - Free hosting with MySQL

Your friends will access: `https://yoursite.000webhostapp.com`