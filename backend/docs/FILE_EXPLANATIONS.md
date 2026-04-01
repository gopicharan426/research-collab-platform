# Research Collaboration Platform - File Explanations

## 📁 Project Structure

```
research-collab-platform/
├── frontend/                    # User Interface Files
├── backend/                     # Server Logic Files
├── .gitignore                   # Git ignore rules
├── README.md                    # Project overview
├── PROJECT_STRUCTURE.md         # Structure documentation
├── SYSTEM_ARCHITECTURE.md       # System design
└── TECHNICAL_OVERVIEW.md        # Technical details
```

---

## 🎨 FRONTEND FILES (User Interface)

### 📄 frontend/index.php
**Purpose:** Home page - Browse all research posts  
**What it does:**
- Displays all research posts from database
- Shows search bar for filtering posts
- Lists posts with title, author, date, views, likes, comments
- Admin users see "Delete Post" button on all posts
- Includes navigation menu (Dashboard, Home, Logout)

**User sees:**
- All research posts in cards
- Search functionality
- Post previews (300 characters)
- "Read More" buttons

---

### 📄 frontend/login.php
**Purpose:** User authentication page  
**What it does:**
- Shows login form (email + password)
- Shows registration form (name + email + password)
- Handles user login via backend auth API
- Handles user registration via backend auth API
- Google OAuth "Sign in with Google" button
- Password reset link
- Validates input before submission

**User sees:**
- Login form
- Register form (toggle)
- Google sign-in button
- Forgot password link
- Success/error messages

---

### 📄 frontend/dashboard.php
**Purpose:** User's personal dashboard  
**What it does:**
- Shows user profile (name, email, post count)
- Displays "Create New Research Post" form
- Lists all posts created by logged-in user
- Allows user to delete their own posts
- Shows post statistics (views, likes)

**User sees:**
- Profile card with avatar
- Create post form (title + description)
- List of their own posts
- Delete button for each post
- View/Like counts

---

### 📄 frontend/post_details.php
**Purpose:** Individual post view with comments  
**What it does:**
- Displays full post content (title + description)
- Shows post metadata (author, date, views, likes)
- Displays all comments with authors
- Provides "Add Comment" form
- Like/Unlike button with current status
- Admin delete button (if admin)
- Increments view count when page loads

**User sees:**
- Full post text
- Author information
- Like button (❤️ Like/Unlike)
- All comments
- Add comment form
- Back to Home button

---

### 📄 frontend/google_callback.php
**Purpose:** Google OAuth callback handler  
**What it does:**
- Receives authorization code from Google
- Exchanges code for access token
- Fetches user profile from Google API
- Checks if user exists in database
- Creates new account if first-time user
- Logs in existing user
- Creates session
- Redirects to home page

**User sees:**
- Brief loading (automatic redirect)
- Then lands on home page (logged in)

---

### 📄 frontend/reset_password.php
**Purpose:** Password reset functionality  
**What it does:**
- Shows email input form
- Sends password reset email
- Validates reset token
- Allows user to set new password
- Updates password in database

**User sees:**
- Email input form
- "Send Reset Link" button
- Success/error messages
- New password form (after clicking email link)

---

### 🎨 frontend/css/style.css
**Purpose:** All styling for the application  
**What it does:**
- Defines colors, fonts, spacing
- Creates responsive layout (mobile-friendly)
- Styles buttons, forms, cards
- Cinematic dark theme design
- Hover effects and animations
- Navigation menu styling
- Alert boxes (success/error)

**Contains:**
- CSS variables for colors
- Responsive grid layout
- Button styles
- Form styles
- Card designs
- Footer styling

---

### ⚡ frontend/js/script.js
**Purpose:** Client-side JavaScript interactions  
**What it does:**
- Form validation before submission
- Confirmation dialogs (delete actions)
- Dynamic UI updates
- Character counters for text fields
- Alert auto-dismiss
- Smooth scrolling

**Features:**
- Validates email format
- Checks password length
- Confirms before deleting posts
- Shows/hides elements dynamically

---

## 🔧 BACKEND FILES (Server Logic)

### 🔐 backend/app/auth/auth.php
**Purpose:** Authentication & authorization APIs  
**What it does:**

**Functions:**
1. **registerUser($name, $email, $password)**
   - Validates input (email format, password length)
   - Checks if email already exists
   - Hashes password with bcrypt
   - Inserts new user into database
   - Returns success or error message

2. **loginUser($email, $password)**
   - Validates input
   - Queries database for user
   - Verifies password hash
   - Creates session with user data
   - Returns success or error message

3. **logoutUser()**
   - Destroys session
   - Redirects to login page

4. **isLoggedIn()**
   - Checks if session exists
   - Returns true/false

5. **isAdmin()**
   - Checks if user has admin role
   - Returns true/false

6. **requireLogin()**
   - Middleware function
   - Redirects to login if not logged in
   - Used on protected pages

---

### 📝 backend/app/posts/posts.php
**Purpose:** Post management APIs  
**What it does:**

**Functions:**
1. **createPost($userId, $title, $description)**
   - Validates title and description
   - Checks length limits (title: 200, description: 2000)
   - Inserts post into database
   - Returns success or error

2. **getAllPosts($search = '')**
   - Queries all posts from database
   - Joins with users table for author names
   - Filters by search term if provided
   - Orders by newest first
   - Returns array of posts

3. **getPostById($postId)**
   - Fetches single post by ID
   - Joins with users table
   - Returns post data or false

4. **getPostsByUser($userId)**
   - Fetches all posts by specific user
   - Returns array of user's posts

5. **deletePost($postId, $userId, $isAdmin)**
   - Checks if user owns post OR is admin
   - Deletes post from database
   - Cascades to delete comments and likes
   - Returns success or error

---

### ❤️ backend/app/posts/likes.php
**Purpose:** Engagement tracking (likes & views)  
**What it does:**

**Functions:**
1. **toggleLike($postId, $userId)**
   - Checks if user already liked post
   - If liked: Removes like (DELETE)
   - If not liked: Adds like (INSERT)
   - Returns success or error

2. **hasUserLiked($postId, $userId)**
   - Checks if user has liked specific post
   - Returns true/false

3. **getLikeCount($postId)**
   - Counts total likes for post
   - Returns number

4. **incrementViews($postId)**
   - Increases view count by 1
   - Called when post details page loads

5. **getViewCount($postId)**
   - Returns total view count for post

---

### 💬 backend/app/comments/comments.php
**Purpose:** Comment system APIs  
**What it does:**

**Functions:**
1. **addComment($postId, $userId, $commentText)**
   - Validates comment text
   - Checks length limit (max 1000 chars)
   - Inserts comment into database
   - Returns success or error

2. **getCommentsByPost($postId)**
   - Fetches all comments for specific post
   - Joins with users table for author names
   - Orders by oldest first (chronological)
   - Returns array of comments

3. **getCommentCount($postId)**
   - Counts total comments for post
   - Returns number

---

### 🗄️ backend/app/config/database.php
**Purpose:** Database connection  
**What it does:**
- Establishes PDO connection to MySQL
- Sets connection parameters (host, database, user, password)
- Configures error mode for debugging
- Returns PDO object for queries
- Used by all backend API files

**Contains:**
```php
function getDBConnection() {
    $host = 'localhost';
    $dbname = 'research_collab';
    $username = 'root';
    $password = '';
    // Returns PDO connection
}
```

---

### 🔑 backend/app/config/google_config.php
**Purpose:** Google OAuth configuration  
**What it does:**
- Stores Google OAuth credentials
- Client ID
- Client Secret
- Redirect URI
- OAuth URLs (auth, token, userinfo)

**Contains:**
```php
define('GOOGLE_CLIENT_ID', 'your-client-id');
define('GOOGLE_CLIENT_SECRET', 'your-secret');
define('GOOGLE_REDIRECT_URI', 'http://localhost:8080/google_callback.php');
```

**⚠️ SENSITIVE:** Should not be committed to Git (in .gitignore)

---

## 🗃️ BACKEND DATABASE FILES

### 📊 backend/database/schema.sql
**Purpose:** Main database structure  
**What it does:**
- Creates `research_collab` database
- Creates `users` table (user accounts)
- Creates `research_posts` table (posts)
- Creates `comments` table (comments)
- Sets up foreign key relationships
- Inserts sample test data

**Tables created:**
- users (user_id, name, email, password, created_at)
- research_posts (post_id, user_id, title, description, created_at)
- comments (comment_id, post_id, user_id, comment_text, created_at)

---

### 👑 backend/database/add_admin.sql
**Purpose:** Add admin role to users  
**What it does:**
- Adds `is_admin` column to users table
- Sets test user as admin (test@example.com)

**SQL:**
```sql
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;
UPDATE users SET is_admin = 1 WHERE email = 'test@example.com';
```

---

### 👍 backend/database/add_likes_views.sql
**Purpose:** Add engagement features  
**What it does:**
- Creates `post_likes` table (like tracking)
- Adds `views` column to research_posts table
- Sets up unique constraint (one like per user per post)

**Tables/Columns:**
- post_likes (like_id, post_id, user_id, created_at)
- research_posts.views (INT DEFAULT 0)

---

### 🔐 backend/database/add_reset_tokens.sql
**Purpose:** Password reset functionality  
**What it does:**
- Creates `password_reset_tokens` table
- Stores reset tokens with expiration
- Links tokens to user emails

**Table:**
- password_reset_tokens (token_id, email, token, expires_at, created_at)

---

## 📚 BACKEND DOCUMENTATION

### 📖 backend/docs/ARCHITECTURE.md
**Purpose:** System architecture documentation  
**What it does:**
- Explains overall system design
- Shows component relationships
- Describes data flow
- Architecture diagrams

---

### 📖 backend/docs/DATABASE_SCHEMA.md
**Purpose:** Database schema documentation  
**What it does:**
- Documents all tables
- Explains relationships
- Shows field types
- ER diagrams

---

### 📖 backend/docs/GITHUB_WORKFLOW.md
**Purpose:** Git workflow guide  
**What it does:**
- Explains how to use Git
- Commit guidelines
- Branch strategy
- Deployment process

---

### 📖 backend/docs/PROJECT_SUMMARY.md
**Purpose:** Project overview  
**What it does:**
- High-level project description
- Features list
- Technology stack
- Quick start guide

---

### 📖 backend/docs/UI_WIREFRAMES.md
**Purpose:** UI design documentation  
**What it does:**
- Page layouts
- User flow diagrams
- Design decisions
- Wireframes

---

## 📄 ROOT LEVEL FILES

### 🚫 .gitignore
**Purpose:** Git ignore rules  
**What it does:**
- Lists files Git should NOT track
- Protects sensitive files (google_config.php)
- Excludes temporary files (*.log, *.tmp)
- Excludes IDE files (.vscode/, .idea/)
- Excludes OS files (.DS_Store, Thumbs.db)

**Protects:**
- OAuth credentials
- Database passwords
- Environment variables
- Temporary files

---

### 📖 README.md
**Purpose:** Project introduction  
**What it does:**
- Project description
- Features overview
- Installation instructions
- How to run the project
- Technology stack
- Screenshots

**First file people see on GitHub**

---

### 📖 PROJECT_STRUCTURE.md
**Purpose:** File organization guide  
**What it does:**
- Explains folder structure
- Lists all files
- Describes frontend vs backend
- How files communicate

---

### 📖 SYSTEM_ARCHITECTURE.md
**Purpose:** Technical architecture  
**What it does:**
- System design diagrams
- Component architecture
- Data flow diagrams
- Complete workflows
- Security architecture

---

### 📖 TECHNICAL_OVERVIEW.md
**Purpose:** Technical details  
**What it does:**
- API documentation
- Database schema
- CRUD operations
- State management
- Security measures

---

## 🔄 How Files Work Together

### Example: User Creates a Post

1. **User fills form** → `frontend/dashboard.php` (HTML form)
2. **Form submits** → POST request to `dashboard.php`
3. **Dashboard includes** → `backend/app/posts/posts.php`
4. **Calls function** → `createPost($userId, $title, $description)`
5. **Function validates** → Checks input, length limits
6. **Function queries** → Uses `backend/app/config/database.php`
7. **Inserts to DB** → MySQL `research_posts` table
8. **Returns result** → Success or error message
9. **Dashboard displays** → Alert message to user
10. **Page reloads** → Shows new post in list

### Example: User Likes a Post

1. **User clicks like** → `frontend/post_details.php` (button)
2. **Form submits** → POST request
3. **Page includes** → `backend/app/posts/likes.php`
4. **Calls function** → `toggleLike($postId, $userId)`
5. **Function checks** → If already liked
6. **Function updates** → INSERT or DELETE in `post_likes` table
7. **Page reloads** → Shows updated like count and button state

---

## 📊 File Count Summary

**Frontend:** 8 files
- 6 PHP pages
- 1 CSS file
- 1 JS file

**Backend:** 14 files
- 6 PHP API files
- 4 SQL files
- 5 Documentation files

**Root:** 5 files
- 1 .gitignore
- 4 Markdown docs

**Total:** 27 files

---

## 🎯 Key Takeaways

**Frontend files:**
- Handle user interface
- Display data
- Collect user input
- Include backend APIs

**Backend files:**
- Process business logic
- Database operations
- Authentication
- Security
- API functions

**Database files:**
- Define structure
- Create tables
- Set up relationships

**Documentation files:**
- Explain how everything works
- Help developers understand
- Guide for future maintenance
