# Database Schema & Entity Design

## Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────┐
│                         USERS TABLE                              │
├─────────────────────────────────────────────────────────────────┤
│ PK │ id (INT, AUTO_INCREMENT)                                   │
│    │ name (VARCHAR 100) NOT NULL                                │
│    │ email (VARCHAR 100) UNIQUE NOT NULL                        │
│    │ password (VARCHAR 255) NOT NULL                            │
│    │ google_id (VARCHAR 255) NULL                               │
│    │ reset_token (VARCHAR 64) NULL                              │
│    │ reset_token_expiry (DATETIME) NULL                         │
│    │ created_at (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ 1
                              │
                              │ has many
                              │
                              │ N
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                    RESEARCH_POSTS TABLE                          │
├─────────────────────────────────────────────────────────────────┤
│ PK │ id (INT, AUTO_INCREMENT)                                   │
│ FK │ user_id (INT) NOT NULL → users.id                          │
│    │ title (VARCHAR 200) NOT NULL                               │
│    │ description (TEXT) NOT NULL                                │
│    │ category (VARCHAR 100) NOT NULL                            │
│    │ views (INT DEFAULT 0)                                      │
│    │ created_at (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ 1
                              │
                ┌─────────────┴─────────────┐
                │                           │
                │ has many                  │ has many
                │                           │
                │ N                         │ N
                ↓                           ↓
┌───────────────────────────────┐  ┌───────────────────────────────┐
│      COMMENTS TABLE           │  │     POST_LIKES TABLE          │
├───────────────────────────────┤  ├───────────────────────────────┤
│ PK │ id (INT, AUTO_INCREMENT) │  │ PK │ id (INT, AUTO_INCREMENT) │
│ FK │ post_id (INT) NOT NULL   │  │ FK │ post_id (INT) NOT NULL   │
│ FK │ user_id (INT) NOT NULL   │  │ FK │ user_id (INT) NOT NULL   │
│    │ comment (TEXT) NOT NULL  │  │    │ created_at (TIMESTAMP)   │
│    │ created_at (TIMESTAMP)   │  └───────────────────────────────┘
└───────────────────────────────┘
        │                                   │
        │ N                                 │ N
        │                                   │
        │ belongs to                        │ belongs to
        │                                   │
        │ 1                                 │ 1
        ↓                                   ↓
┌─────────────────────────────────────────────────────────────────┐
│                         USERS TABLE                              │
│                      (Same as above)                             │
└─────────────────────────────────────────────────────────────────┘
```

## Relationships

### 1. Users ↔ Research Posts (One-to-Many)
- One user can create many research posts
- Each post belongs to one user
- **Foreign Key**: `research_posts.user_id` → `users.id`
- **Cascade**: ON DELETE CASCADE (delete posts when user is deleted)

### 2. Research Posts ↔ Comments (One-to-Many)
- One post can have many comments
- Each comment belongs to one post
- **Foreign Key**: `comments.post_id` → `research_posts.id`
- **Cascade**: ON DELETE CASCADE

### 3. Users ↔ Comments (One-to-Many)
- One user can write many comments
- Each comment belongs to one user
- **Foreign Key**: `comments.user_id` → `users.id`
- **Cascade**: ON DELETE CASCADE

### 4. Research Posts ↔ Post Likes (One-to-Many)
- One post can have many likes
- Each like belongs to one post
- **Foreign Key**: `post_likes.post_id` → `research_posts.id`
- **Cascade**: ON DELETE CASCADE

### 5. Users ↔ Post Likes (One-to-Many)
- One user can like many posts
- Each like belongs to one user
- **Foreign Key**: `post_likes.user_id` → `users.id`
- **Cascade**: ON DELETE CASCADE

## Table Structures

### 1. USERS Table

| Column | Data Type | Constraints | Description |
|--------|-----------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| name | VARCHAR(100) | NOT NULL | User's full name |
| email | VARCHAR(100) | UNIQUE, NOT NULL | User's email (login) |
| password | VARCHAR(255) | NOT NULL | Hashed password (bcrypt) |
| google_id | VARCHAR(255) | NULL | Google OAuth ID |
| reset_token | VARCHAR(64) | NULL | Password reset token |
| reset_token_expiry | DATETIME | NULL | Token expiration time |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation date |

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE INDEX on `email`
- INDEX on `google_id`

### 2. RESEARCH_POSTS Table

| Column | Data Type | Constraints | Description |
|--------|-----------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique post identifier |
| user_id | INT | FOREIGN KEY, NOT NULL | Post author (users.id) |
| title | VARCHAR(200) | NOT NULL | Post title |
| description | TEXT | NOT NULL | Post content |
| category | VARCHAR(100) | NOT NULL | Research category |
| views | INT | DEFAULT 0 | View counter |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Post creation date |

**Indexes**:
- PRIMARY KEY on `id`
- FOREIGN KEY on `user_id` → `users.id`
- INDEX on `user_id` (for faster queries)
- INDEX on `category` (for filtering)

### 3. COMMENTS Table

| Column | Data Type | Constraints | Description |
|--------|-----------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique comment identifier |
| post_id | INT | FOREIGN KEY, NOT NULL | Related post (research_posts.id) |
| user_id | INT | FOREIGN KEY, NOT NULL | Comment author (users.id) |
| comment | TEXT | NOT NULL | Comment content |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Comment creation date |

**Indexes**:
- PRIMARY KEY on `id`
- FOREIGN KEY on `post_id` → `research_posts.id`
- FOREIGN KEY on `user_id` → `users.id`
- INDEX on `post_id` (for faster comment retrieval)

### 4. POST_LIKES Table

| Column | Data Type | Constraints | Description |
|--------|-----------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique like identifier |
| post_id | INT | FOREIGN KEY, NOT NULL | Liked post (research_posts.id) |
| user_id | INT | FOREIGN KEY, NOT NULL | User who liked (users.id) |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Like timestamp |

**Indexes**:
- PRIMARY KEY on `id`
- FOREIGN KEY on `post_id` → `research_posts.id`
- FOREIGN KEY on `user_id` → `users.id`
- UNIQUE INDEX on (`post_id`, `user_id`) - prevent duplicate likes

## Data Type Justifications

### VARCHAR vs TEXT
- **VARCHAR**: Fixed-length fields (name, email, title) - faster indexing
- **TEXT**: Variable-length content (description, comments) - no length limit

### INT vs BIGINT
- **INT**: Sufficient for small-medium scale (up to 2 billion records)
- **BIGINT**: Use for large-scale applications

### TIMESTAMP vs DATETIME
- **TIMESTAMP**: Auto-updates, timezone aware, better for created_at
- **DATETIME**: Manual control, no timezone

### Password Storage
- **VARCHAR(255)**: Stores bcrypt hash (60 chars) with room for future algorithms

## Normalization

### Current Form: 3NF (Third Normal Form)

**1NF**: All columns contain atomic values
- ✓ No repeating groups
- ✓ Each cell contains single value

**2NF**: No partial dependencies
- ✓ All non-key attributes depend on entire primary key
- ✓ No composite keys with partial dependencies

**3NF**: No transitive dependencies
- ✓ No non-key attribute depends on another non-key attribute
- ✓ User name stored in users table, not duplicated in posts

## Sample Data

### Users
```sql
INSERT INTO users (name, email, password) VALUES
('John Doe', 'john@example.com', '$2y$10$hash...'),
('Jane Smith', 'jane@example.com', '$2y$10$hash...'),
('Test User', 'test@example.com', '$2y$10$hash...');
```

### Research Posts
```sql
INSERT INTO research_posts (user_id, title, description, category) VALUES
(1, 'AI in Healthcare', 'Exploring machine learning...', 'Artificial Intelligence'),
(2, 'Climate Change Study', 'Analysis of global warming...', 'Environmental Science');
```

### Comments
```sql
INSERT INTO comments (post_id, user_id, comment) VALUES
(1, 2, 'Great research! Very insightful.'),
(1, 3, 'Would love to collaborate on this.');
```

### Post Likes
```sql
INSERT INTO post_likes (post_id, user_id) VALUES
(1, 2),
(1, 3),
(2, 1);
```

## Query Optimization

### Common Queries

**Get all posts with author info**:
```sql
SELECT p.*, u.name as author_name 
FROM research_posts p 
JOIN users u ON p.user_id = u.id 
ORDER BY p.created_at DESC;
```

**Get post with like count**:
```sql
SELECT p.*, COUNT(pl.id) as like_count 
FROM research_posts p 
LEFT JOIN post_likes pl ON p.id = pl.post_id 
WHERE p.id = ? 
GROUP BY p.id;
```

**Check if user liked post**:
```sql
SELECT COUNT(*) 
FROM post_likes 
WHERE post_id = ? AND user_id = ?;
```

## Future Enhancements

1. **Tags Table**: Many-to-many relationship for post tags
2. **Followers Table**: User following system
3. **Notifications Table**: User notification system
4. **Files Table**: Research paper uploads
5. **Messages Table**: Direct messaging between users
