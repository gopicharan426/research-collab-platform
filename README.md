# Research Collaboration Platform

A full-stack web application for researchers to share ideas, collaborate on projects, and engage with the academic community.

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 8.x
- **Database**: MySQL 8.x
- **Authentication**: Session-based + Google OAuth 2.0
- **Server**: PHP Built-in Server / Apache (XAMPP)

## Features

- User Authentication (Login/Register/Google OAuth)
- Password Reset Functionality
- Create & Share Research Posts
- Comment System
- Like/Unlike Posts
- View Counter
- Search Functionality
- User Dashboard
- Responsive Cinematic UI Design

## Installation Steps

### Prerequisites
- XAMPP (PHP 8.x + MySQL)
- Web Browser (Chrome/Firefox)
- Git (optional)

### Setup Instructions

1. **Clone/Download Project**
```bash
cd C:\xampp\htdocs
git clone <repository-url> research-collab-platform
```

2. **Start MySQL Server**
- Open XAMPP Control Panel
- Click "Start" next to MySQL

3. **Create Database**
- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Create database: `research_collab`
- Import schema: `database/schema.sql`
- Run updates: `database/add_likes_views.sql` and `database/add_reset_tokens.sql`

4. **Configure Google OAuth (Optional)**
- Get credentials from: https://console.cloud.google.com/
- Update `app/config/google_config.php` with your Client ID and Secret

5. **Start PHP Server**
```bash
cd C:\xampp\htdocs\research-collab-platform\public
php -S localhost:8080
```

6. **Access Application**
- Open browser: `http://localhost:8080`
- Test account: `test@example.com` / `password123`

## Project Structure

```
research-collab-platform/
├── app/
│   ├── auth/
│   │   └── auth.php              # Authentication functions
│   ├── posts/
│   │   ├── posts.php             # Post management
│   │   └── likes.php             # Like & view system
│   ├── comments/
│   │   └── comments.php          # Comment management
│   └── config/
│       ├── database.php          # Database connection
│       ├── google_config.php     # Google OAuth config
│       └── email_config.php      # Email configuration
├── database/
│   ├── schema.sql                # Database schema
│   ├── add_likes_views.sql       # Likes & views tables
│   └── add_reset_tokens.sql      # Password reset tokens
├── public/
│   ├── css/
│   │   └── style.css             # Cinematic UI styles
│   ├── js/
│   │   └── script.js             # Frontend JavaScript
│   ├── index.php                 # Login/Register page
│   ├── dashboard.php             # User dashboard
│   ├── browse.php                # Browse all posts
│   ├── post_details.php          # Individual post view
│   ├── reset_password.php        # Password reset
│   └── google_callback.php       # OAuth callback
├── docs/
│   ├── ARCHITECTURE.md           # System architecture
│   ├── DATABASE_SCHEMA.md        # ER diagrams
│   └── UI_WIREFRAMES.md          # UI/UX design
├── .gitignore
└── README.md
```

## Database Schema

### Tables
- **users**: User accounts and authentication
- **research_posts**: Research posts and content
- **comments**: Post comments
- **post_likes**: Like tracking

## Usage

1. **Register/Login**: Create account or use test credentials
2. **Dashboard**: View and manage your posts
3. **Create Post**: Share research ideas with title, description, and category
4. **Browse**: Explore all research posts with search
5. **Interact**: Like, comment, and view posts
6. **Reset Password**: Use forgot password feature if needed

## API Endpoints

- `POST /index.php` - Login/Register
- `GET /dashboard.php` - User dashboard
- `GET /browse.php` - Browse posts
- `GET /post_details.php?id=X` - View post
- `POST /reset_password.php` - Reset password

## Security Features

- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars)
- Session management
- CSRF protection ready

## Future Enhancements

- Email verification for password reset
- File upload for research papers
- Advanced search filters
- User profiles with bio
- Notification system
- Real-time chat

## Contributors

- Your Name - Developer

## License

MIT License - Academic Project

## Support

For issues or questions, contact: your-email@example.com
