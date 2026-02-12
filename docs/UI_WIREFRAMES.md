# UI/UX Wireframes & Design Theme

## Design Philosophy

**Theme**: Cinematic Research Platform
- Modern, professional, and visually engaging
- Purple-pink gradient color scheme
- Glass-morphism effects
- Smooth animations and transitions
- Responsive and accessible

## Color Palette

### Primary Colors
```css
--primary: #6366f1        /* Indigo Blue */
--secondary: #8b5cf6      /* Purple */
--accent: #ec4899         /* Pink */
```

### Gradient Combinations
```css
/* Main Gradient */
background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);

/* Card Gradient */
background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));

/* Button Gradient */
background: linear-gradient(135deg, #6366f1, #8b5cf6);
```

### Neutral Colors
```css
--background: #0f172a      /* Dark Blue */
--surface: #1e293b         /* Slate */
--text: #f1f5f9           /* Light Gray */
--border: rgba(255, 255, 255, 0.1)
```

## Typography

### Font Family
```css
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
```

### Font Sizes
- **Headings**: 2rem - 2.5rem
- **Body**: 1rem
- **Small**: 0.875rem

### Font Weights
- **Bold**: 600-700 (headings)
- **Normal**: 400 (body text)

## Layout Structure

### Grid System
```
┌─────────────────────────────────────────────────────────┐
│                      HEADER (Fixed)                      │
│  Logo                                    Navigation      │
└─────────────────────────────────────────────────────────┘
│                                                          │
│                    MAIN CONTENT                          │
│  ┌────────────────────────────────────────────────┐    │
│  │                                                 │    │
│  │              Content Area                       │    │
│  │          (Cards, Forms, Posts)                  │    │
│  │                                                 │    │
│  └────────────────────────────────────────────────┘    │
│                                                          │
└─────────────────────────────────────────────────────────┘
│                      FOOTER                              │
│              Copyright & Links                           │
└─────────────────────────────────────────────────────────┘
```

## Page Wireframes

### 1. Login/Register Page

```
┌───────────────────────────────────────────────────────────┐
│                    HEADER                                  │
│         Research Collaboration Platform                    │
└───────────────────────────────────────────────────────────┘

        ┌─────────────────────────────────────┐
        │                                     │
        │      LOGIN TO YOUR ACCOUNT          │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │ Email                         │ │
        │  │ [____________________________]│ │
        │  └───────────────────────────────┘ │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │ Password                      │ │
        │  │ [____________________________]│ │
        │  └───────────────────────────────┘ │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │      [LOGIN BUTTON]           │ │
        │  └───────────────────────────────┘ │
        │                                     │
        │         Forgot Password?            │
        │                                     │
        │              OR                     │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │  🔐 Sign in with Google       │ │
        │  └───────────────────────────────┘ │
        │                                     │
        │    Don't have an account?           │
        │  ┌───────────────────────────────┐ │
        │  │   [CREATE NEW ACCOUNT]        │ │
        │  └───────────────────────────────┘ │
        │                                     │
        └─────────────────────────────────────┘

        ┌─────────────────────────────────────┐
        │  WELCOME TO RESEARCH PLATFORM       │
        │                                     │
        │  • Share research ideas             │
        │  • Collaborate with researchers     │
        │  • Get feedback on projects         │
        │  • Discover opportunities           │
        │                                     │
        │  Test: test@example.com / pass123   │
        └─────────────────────────────────────┘
```

### 2. Dashboard Page

```
┌───────────────────────────────────────────────────────────┐
│  HEADER                                    [Profile] [Logout]│
└───────────────────────────────────────────────────────────┘

┌──────────┐  ┌────────────────────────────────────────────┐
│          │  │                                            │
│  PROFILE │  │        CREATE NEW RESEARCH POST            │
│          │  │                                            │
│  Photo   │  │  Title: [_____________________________]   │
│          │  │                                            │
│  Name    │  │  Description:                              │
│  Email   │  │  [________________________________]        │
│          │  │  [________________________________]        │
│  Stats:  │  │  [________________________________]        │
│  Posts:5 │  │                                            │
│          │  │  Category: [Computer Science ▼]           │
│  [Browse]│  │                                            │
│  [Logout]│  │  [CREATE POST BUTTON]                     │
│          │  │                                            │
└──────────┘  └────────────────────────────────────────────┘

              ┌────────────────────────────────────────────┐
              │         YOUR RESEARCH POSTS                │
              ├────────────────────────────────────────────┤
              │                                            │
              │  📄 AI in Healthcare                       │
              │     Category: Artificial Intelligence      │
              │     👁 125 views  ❤ 23 likes  💬 8 comments│
              │     [View] [Delete]                        │
              │                                            │
              ├────────────────────────────────────────────┤
              │                                            │
              │  📄 Machine Learning Study                 │
              │     Category: Computer Science             │
              │     👁 89 views  ❤ 15 likes  💬 5 comments │
              │     [View] [Delete]                        │
              │                                            │
              └────────────────────────────────────────────┘
```

### 3. Browse Posts Page

```
┌───────────────────────────────────────────────────────────┐
│  HEADER                                    [Dashboard] [Logout]│
└───────────────────────────────────────────────────────────┘

        ┌─────────────────────────────────────────────┐
        │     BROWSE RESEARCH POSTS                   │
        │                                             │
        │  🔍 [Search posts...____________] [Search] │
        └─────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                                                              │
│  📄 AI in Healthcare                          by John Doe   │
│     Category: Artificial Intelligence                       │
│                                                              │
│     Exploring machine learning applications in medical      │
│     diagnosis and treatment planning...                     │
│                                                              │
│     👁 125 views  ❤ 23 likes  💬 8 comments                │
│     [Read More]                                             │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  📄 Climate Change Study                    by Jane Smith   │
│     Category: Environmental Science                         │
│                                                              │
│     Analysis of global warming trends and their impact      │
│     on coastal ecosystems...                                │
│                                                              │
│     👁 89 views  ❤ 15 likes  💬 5 comments                 │
│     [Read More]                                             │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  📄 Quantum Computing Basics                by Alex Chen    │
│     Category: Physics                                       │
│                                                              │
│     Introduction to quantum computing principles and        │
│     their applications...                                   │
│                                                              │
│     👁 67 views  ❤ 12 likes  💬 3 comments                 │
│     [Read More]                                             │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 4. Post Details Page

```
┌───────────────────────────────────────────────────────────┐
│  HEADER                                    [Dashboard] [Logout]│
└───────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                                                              │
│  📄 AI in Healthcare                                        │
│     by John Doe                                             │
│     Category: Artificial Intelligence                       │
│     Posted: 2024-01-15                                      │
│                                                              │
│  ─────────────────────────────────────────────────────────  │
│                                                              │
│  Exploring machine learning applications in medical         │
│  diagnosis and treatment planning. This research focuses    │
│  on using deep learning algorithms to analyze medical       │
│  images and predict patient outcomes...                     │
│                                                              │
│  [Full content here...]                                     │
│                                                              │
│  ─────────────────────────────────────────────────────────  │
│                                                              │
│  👁 125 views    [❤ Like (23)]                             │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  💬 COMMENTS (8)                                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Jane Smith - 2 hours ago                                   │
│  Great research! Very insightful analysis.                  │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Alex Chen - 5 hours ago                                    │
│  Would love to collaborate on this project.                 │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ADD YOUR COMMENT                                           │
│  [_____________________________________________]            │
│  [_____________________________________________]            │
│  [POST COMMENT]                                             │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 5. Password Reset Page

```
┌───────────────────────────────────────────────────────────┐
│                    HEADER                                  │
│         Research Collaboration Platform                    │
└───────────────────────────────────────────────────────────┘

        ┌─────────────────────────────────────┐
        │                                     │
        │        RESET PASSWORD               │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │ Email                         │ │
        │  │ [____________________________]│ │
        │  └───────────────────────────────┘ │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │ New Password                  │ │
        │  │ [____________________________]│ │
        │  └───────────────────────────────┘ │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │ Confirm Password              │ │
        │  │ [____________________________]│ │
        │  └───────────────────────────────┘ │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │   [RESET PASSWORD BUTTON]     │ │
        │  └───────────────────────────────┘ │
        │                                     │
        │  ┌───────────────────────────────┐ │
        │  │   [BACK TO LOGIN]             │ │
        │  └───────────────────────────────┘ │
        │                                     │
        └─────────────────────────────────────┘
```

## UI Components

### 1. Cards
```css
.card {
  background: rgba(30, 41, 59, 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}
```

### 2. Buttons
```css
.btn-primary {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  transition: transform 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}
```

### 3. Input Fields
```css
.form-control {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
  padding: 12px;
  border-radius: 8px;
}

.form-control:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}
```

## Responsive Design

### Breakpoints
```css
/* Mobile */
@media (max-width: 768px) {
  font-size: 14px;
  padding: 1rem;
}

/* Tablet */
@media (min-width: 769px) and (max-width: 1024px) {
  font-size: 15px;
}

/* Desktop */
@media (min-width: 1025px) {
  font-size: 16px;
}
```

## User Journey

### New User Flow
```
Landing Page → Register → Email Verification (future) → Dashboard
```

### Returning User Flow
```
Landing Page → Login → Dashboard → Browse/Create Posts
```

### Post Interaction Flow
```
Browse → Click Post → View Details → Like/Comment → Back to Browse
```

### Password Reset Flow
```
Login Page → Forgot Password → Reset Page → Enter Email & New Password → Login
```

## Accessibility Features

- **Keyboard Navigation**: Tab through all interactive elements
- **ARIA Labels**: Screen reader support
- **Color Contrast**: WCAG AA compliant
- **Focus Indicators**: Visible focus states
- **Semantic HTML**: Proper heading hierarchy

## Animation Effects

### Page Load
```css
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
```

### Hover Effects
```css
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(99, 102, 241, 0.3);
}
```

### Button Click
```css
.btn:active {
  transform: scale(0.98);
}
```

## Design Tools Used

- **Hand-drawn sketches**: Initial wireframes
- **CSS**: Custom styling and animations
- **Browser DevTools**: Responsive testing
- **Color Picker**: Gradient generation

## Future UI Enhancements

1. **Dark/Light Mode Toggle**
2. **Custom Avatars**
3. **Rich Text Editor** for posts
4. **Image Upload** support
5. **Notification Bell** with dropdown
6. **Loading Skeletons**
7. **Toast Notifications**
8. **Modal Dialogs**
