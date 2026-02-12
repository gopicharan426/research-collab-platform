# System Architecture & Technology Stack

## Technology Stack Justification

### Frontend Layer
- **HTML5**: Semantic markup for better SEO and accessibility
- **CSS3**: Modern styling with gradients, animations, and responsive design
- **JavaScript (Vanilla)**: Lightweight, no framework overhead, fast performance

**Why not React/Vue?**
- Simpler deployment (no build process)
- Faster development for small-scale project
- Better for learning fundamentals
- Lower resource requirements

### Backend Layer
- **PHP 8.x**: 
  - Easy to learn and deploy
  - Excellent MySQL integration
  - Built-in session management
  - Wide hosting support
  - Perfect for academic projects

### Database Layer
- **MySQL 8.x**:
  - Reliable relational database
  - ACID compliance
  - Excellent for structured data
  - Free and open-source
  - Great documentation

### Authentication
- **Session-based Auth**: Secure, server-side session management
- **Google OAuth 2.0**: Third-party authentication for convenience

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   HTML/CSS   │  │  JavaScript  │  │  Browser     │      │
│  │   (UI/UX)    │  │  (Interactiv)│  │  (Chrome/FF) │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            ↕ HTTP/HTTPS
┌─────────────────────────────────────────────────────────────┐
│                     APPLICATION LAYER                        │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              PHP Built-in Server / Apache            │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   Auth       │  │   Posts      │  │  Comments    │     │
│  │   Module     │  │   Module     │  │  Module      │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   Session    │  │   Google     │  │   Email      │     │
│  │   Manager    │  │   OAuth      │  │   Service    │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            ↕ PDO
┌─────────────────────────────────────────────────────────────┐
│                       DATABASE LAYER                         │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                    MySQL Server                      │   │
│  │                                                      │   │
│  │  ┌──────┐  ┌──────────┐  ┌──────────┐  ┌────────┐ │   │
│  │  │Users │  │  Posts   │  │ Comments │  │ Likes  │ │   │
│  │  └──────┘  └──────────┘  └──────────┘  └────────┘ │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## Component Interaction Flow

### 1. User Authentication Flow
```
User → Login Form → auth.php → Database (users table)
                      ↓
                  Session Created
                      ↓
                  Redirect to Dashboard
```

### 2. Post Creation Flow
```
User → Dashboard → Create Post Form → posts.php
                                         ↓
                                    Validate Input
                                         ↓
                                    Insert to DB
                                         ↓
                                    Return Success
                                         ↓
                                    Refresh Page
```

### 3. Browse & Search Flow
```
User → Browse Page → Search Query → posts.php (getAllPosts)
                                         ↓
                                    SQL LIKE Query
                                         ↓
                                    Return Results
                                         ↓
                                    Display Posts
```

### 4. Like System Flow
```
User → Click Like → likes.php (toggleLike)
                         ↓
                    Check if liked
                         ↓
                    Insert/Delete
                         ↓
                    Update Count
                         ↓
                    Return JSON
```

### 5. Password Reset Flow
```
User → Forgot Password → reset_password.php
                              ↓
                         Enter Email
                              ↓
                         Verify User
                              ↓
                         Update Password
                              ↓
                         Redirect to Login
```

## Security Architecture

### Input Validation
- Server-side validation for all inputs
- HTML special characters escaping
- Email format validation
- Password strength requirements

### Database Security
- PDO prepared statements (SQL injection prevention)
- Password hashing with bcrypt
- Parameterized queries

### Session Security
- Server-side session storage
- Session regeneration on login
- Secure session configuration

### Authentication Security
- Password hashing (PASSWORD_DEFAULT)
- Google OAuth 2.0 integration
- Session-based authentication

## Scalability Considerations

### Current Architecture (Small Scale)
- Single server deployment
- Session-based authentication
- Direct database queries

### Future Enhancements (Medium Scale)
- Load balancer
- Redis for session storage
- Database replication
- CDN for static assets
- API-based architecture

## Deployment Architecture

### Development Environment
```
XAMPP (localhost)
├── Apache/PHP Server (Port 8080)
├── MySQL Server (Port 3306)
└── phpMyAdmin (Database Management)
```

### Production Environment (Future)
```
Cloud Provider (AWS/Azure/DigitalOcean)
├── Web Server (Apache/Nginx)
├── PHP-FPM
├── MySQL/PostgreSQL
├── SSL Certificate (HTTPS)
└── Domain Name
```

## Technology Comparison

| Feature | PHP Stack | MERN Stack | PERN Stack |
|---------|-----------|------------|------------|
| Learning Curve | Easy | Moderate | Moderate |
| Setup Time | Fast | Slow | Slow |
| Deployment | Simple | Complex | Complex |
| Performance | Good | Excellent | Excellent |
| Hosting Cost | Low | Medium | Medium |
| Best For | Small-Medium | Large Scale | Large Scale |

## Conclusion

The PHP + MySQL stack was chosen for this academic project because:
1. **Simplicity**: Easy to learn and implement
2. **Speed**: Fast development and deployment
3. **Cost**: Free and open-source
4. **Support**: Excellent documentation and community
5. **Hosting**: Wide availability of cheap hosting
6. **Academic**: Perfect for learning web development fundamentals
