# Building React Frontend with PHP REST API Backend
## Research Collaboration Platform - Complete Guide

## Architecture Overview

```
Frontend (React)          Backend (PHP)
Port 3000          ←→     Port 8080
- UI Components           - REST API
- State Management        - Database Operations
- User Interactions       - Authentication
```

## Step 1: Setup React Project

### 1.1 Install Node.js
Download from: https://nodejs.org/ (LTS version)

### 1.2 Create React App
```bash
cd C:\
npx create-react-app research-collab-frontend
cd research-collab-frontend
```

### 1.3 Install Required Dependencies
```bash
npm install lucide-react axios
```

## Step 2: Convert PHP Backend to REST API

### 2.1 Create API Directory Structure
```
C:\xampp\htdocs\research-collab-api\
├── api\
│   ├── auth\
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── posts\
│   │   ├── create.php
│   │   ├── list.php
│   │   ├── get.php
│   │   ├── update.php
│   │   └── delete.php
│   ├── comments\
│   │   ├── create.php
│   │   └── list.php
│   └── likes\
│       ├── toggle.php
│       └── count.php
├── config\
│   ├── database.php
│   └── cors.php
└── .htaccess
```

### 2.2 Enable CORS (config/cors.php)
```php
<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
```

### 2.3 API Response Helper (config/response.php)
```php
<?php
function sendJSON($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function sendError($message, $status = 400) {
    sendJSON(['error' => $message], $status);
}

function sendSuccess($data, $message = 'Success') {
    sendJSON(['success' => true, 'message' => $message, 'data' => $data]);
}
?>
```

## Step 3: Create REST API Endpoints

### 3.1 Authentication API (api/auth/login.php)
```php
<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../config/response.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password'])) {
    sendError('Email and password required', 400);
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT user_id, name, email, password FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($data['password'], $user['password'])) {
    session_start();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    
    sendSuccess([
        'user' => [
            'id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email']
        ]
    ], 'Login successful');
}

sendError('Invalid credentials', 401);
?>
```

### 3.2 Posts API (api/posts/list.php)
```php
<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../config/response.php';

$pdo = getDBConnection();

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';

$sql = "SELECT p.*, u.name as author_name, 
        (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) as likes,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments
        FROM research_posts p 
        JOIN users u ON p.user_id = u.user_id 
        WHERE 1=1";

if ($search) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
}
if ($status !== 'all') {
    $sql .= " AND p.status = ?";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);

$params = [];
if ($search) {
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}
if ($status !== 'all') {
    $params[] = $status;
}

$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendSuccess($posts);
?>
```

### 3.3 Create Post API (api/posts/create.php)
```php
<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../config/response.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    sendError('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['title']) || !isset($data['description'])) {
    sendError('Title and description required', 400);
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("INSERT INTO research_posts (user_id, title, description) VALUES (?, ?, ?)");

if ($stmt->execute([$_SESSION['user_id'], $data['title'], $data['description']])) {
    $postId = $pdo->lastInsertId();
    sendSuccess(['post_id' => $postId], 'Post created successfully');
}

sendError('Failed to create post', 500);
?>
```

## Step 4: React Frontend Structure

### 4.1 Project Structure
```
src/
├── components/
│   ├── Header.jsx
│   ├── Navigation.jsx
│   ├── ProjectCard.jsx
│   ├── PostCard.jsx
│   ├── Modal.jsx
│   └── SearchBar.jsx
├── pages/
│   ├── Dashboard.jsx
│   ├── Projects.jsx
│   ├── Literature.jsx
│   ├── Datasets.jsx
│   ├── Discussions.jsx
│   └── Tasks.jsx
├── services/
│   └── api.js
├── App.jsx
└── index.js
```

### 4.2 API Service (src/services/api.js)
```javascript
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8080/research-collab-api/api';

const api = axios.create({
    baseURL: API_BASE_URL,
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json'
    }
});

export const authAPI = {
    login: (email, password) => api.post('/auth/login.php', { email, password }),
    register: (name, email, password) => api.post('/auth/register.php', { name, email, password }),
    logout: () => api.post('/auth/logout.php')
};

export const postsAPI = {
    getAll: (search = '', status = 'all') => 
        api.get(`/posts/list.php?search=${search}&status=${status}`),
    create: (data) => api.post('/posts/create.php', data),
    delete: (id) => api.delete(`/posts/delete.php?id=${id}`)
};

export const commentsAPI = {
    getByPost: (postId) => api.get(`/comments/list.php?post_id=${postId}`),
    create: (postId, text) => api.post('/comments/create.php', { post_id: postId, comment_text: text })
};

export const likesAPI = {
    toggle: (postId) => api.post('/likes/toggle.php', { post_id: postId }),
    count: (postId) => api.get(`/likes/count.php?post_id=${postId}`)
};

export default api;
```

### 4.3 Main App Component (src/App.jsx)
```javascript
import React, { useState, useEffect } from 'react';
import { authAPI, postsAPI } from './services/api';
import Header from './components/Header';
import Navigation from './components/Navigation';
import Projects from './pages/Projects';
import './App.css';

function App() {
    const [user, setUser] = useState(null);
    const [activeTab, setActiveTab] = useState('projects');
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        loadPosts();
    }, []);

    const loadPosts = async () => {
        setLoading(true);
        try {
            const response = await postsAPI.getAll();
            setPosts(response.data.data);
        } catch (error) {
            console.error('Error loading posts:', error);
        }
        setLoading(false);
    };

    const handleLogin = async (email, password) => {
        try {
            const response = await authAPI.login(email, password);
            setUser(response.data.data.user);
        } catch (error) {
            alert('Login failed');
        }
    };

    return (
        <div className="app">
            <Header user={user} />
            <Navigation activeTab={activeTab} setActiveTab={setActiveTab} />
            <main className="main-content">
                {activeTab === 'projects' && <Projects posts={posts} onRefresh={loadPosts} />}
                {/* Add other tabs */}
            </main>
        </div>
    );
}

export default App;
```

## Step 5: Styling (Copy from React Component)

### 5.1 App.css
Copy the inline styles from the React component to CSS file:
```css
.app {
    min-height: 100vh;
    background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 50%, #e8f0f7 100%);
    font-family: "Courier New", monospace;
}

.header {
    background: rgba(255, 255, 255, 0.95);
    border-bottom: 3px solid #2563eb;
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
}

/* Add all other styles from the React component */
```

## Step 6: Running the Application

### 6.1 Start PHP Backend
```bash
cd C:\xampp\htdocs\research-collab-api
C:\xampp\php\php.exe -S localhost:8080
```

### 6.2 Start React Frontend
```bash
cd C:\research-collab-frontend
npm start
```

### 6.3 Access Application
- Frontend: http://localhost:3000
- Backend API: http://localhost:8080

## Step 7: Database Updates

Run this SQL to add missing columns:
```sql
ALTER TABLE research_posts ADD COLUMN status VARCHAR(50) DEFAULT 'active';
ALTER TABLE research_posts ADD COLUMN progress INT DEFAULT 0;
ALTER TABLE research_posts ADD COLUMN deadline DATE;
ALTER TABLE research_posts ADD COLUMN tags TEXT;
ALTER TABLE research_posts ADD COLUMN team TEXT;
```

## Step 8: Deployment

### For Production:
1. Build React app: `npm run build`
2. Copy build folder to hosting
3. Update API URLs in production
4. Configure CORS for production domain

## Key Benefits of This Approach:

✅ Modern React UI with all features
✅ Existing PHP backend reused as API
✅ Separation of concerns
✅ Scalable architecture
✅ Easy to maintain
✅ Professional full-stack project

## Next Steps:

1. Create the API endpoints (copy code above)
2. Set up React project
3. Copy the React component code
4. Connect frontend to backend
5. Test all features
6. Deploy

This gives you the exact UI you want while leveraging your existing PHP backend!