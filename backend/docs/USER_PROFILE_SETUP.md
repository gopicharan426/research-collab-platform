# User Profile & Search Features - Setup Guide

## ✅ What's Been Added

### 1. User Profile Fields
- Bio (500 chars)
- Research Interests
- Phone Number
- Location
- Website URL
- LinkedIn Profile URL

### 2. Edit Profile Page
- Users can update their bio and details
- URL validation for website and LinkedIn
- Located at: `frontend/edit_profile.php`

### 3. User Search
- Search users by name, email, or department
- View search results with user info
- Located at: `frontend/search_users.php`

### 4. Public Profile View
- View any user's profile
- See their bio, research interests, contact info
- View all their research posts
- See statistics (posts, likes, views)
- Located at: `frontend/view_profile.php`

### 5. Enhanced Dashboard
- Shows user bio preview
- Displays statistics (posts, likes, views)
- "Edit Profile" button added
- "Search Users" link in navigation

---

## 🚀 Setup Steps

### Step 1: Update Database

**Option A: Run SQL in phpMyAdmin**
```sql
ALTER TABLE users 
ADD COLUMN bio TEXT DEFAULT NULL AFTER designation,
ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER bio,
ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER profile_picture,
ADD COLUMN location VARCHAR(100) DEFAULT NULL AFTER phone,
ADD COLUMN website VARCHAR(255) DEFAULT NULL AFTER location,
ADD COLUMN linkedin VARCHAR(255) DEFAULT NULL AFTER website,
ADD COLUMN research_interests TEXT DEFAULT NULL AFTER linkedin;
```

**Option B: Import SQL File**
- Go to phpMyAdmin
- Select `research_collab` database
- Click Import
- Choose: `backend/database/add_profile_fields.sql`
- Click Go

### Step 2: Test Features

**1. Edit Your Profile**
- Login to your account
- Go to Dashboard
- Click "Edit Profile" button
- Fill in your bio, research interests, etc.
- Click "Update Profile"

**2. Search for Users**
- Click "Search Users" in navigation
- Enter a name, email, or department
- Click Search
- View results

**3. View User Profile**
- From search results, click "View Profile"
- See user's complete profile
- View their research posts
- See their statistics

---

## 📋 New Pages Created

### 1. edit_profile.php
**Purpose:** Edit user profile information

**Fields:**
- Bio (textarea, 500 chars)
- Research Interests (textarea)
- Phone Number
- Location
- Website URL
- LinkedIn URL

**Features:**
- URL validation
- XSS protection
- Shows current account info (read-only)

### 2. search_users.php
**Purpose:** Search for other users

**Features:**
- Search by name, email, or department
- Shows up to 50 results
- Displays user role, department, bio preview
- Link to view full profile

### 3. view_profile.php
**Purpose:** View public profile of any user

**Shows:**
- User name, email, role
- Department, class/designation
- Contact info (phone, location)
- Website and LinkedIn links
- Bio and research interests
- Statistics (posts, likes, views)
- All user's research posts

---

## 🎯 User Workflows

### Workflow 1: Update Your Profile
1. Login → Dashboard
2. Click "Edit Profile"
3. Fill in bio, research interests, contact info
4. Click "Update Profile"
5. Success message appears
6. Profile updated in database

### Workflow 2: Search for Users
1. Click "Search Users" in navigation
2. Enter search term (name/email/department)
3. Click "Search"
4. View results list
5. Click "View Profile" on any user

### Workflow 3: View User Profile
1. From search results, click "View Profile"
2. See complete user information
3. View their bio and research interests
4. See their statistics
5. Browse their research posts
6. Click on any post to read details

---

## 📊 Database Schema Updates

```sql
users table (new columns):
- bio (TEXT) - User biography
- profile_picture (VARCHAR(255)) - Profile image path
- phone (VARCHAR(20)) - Phone number
- location (VARCHAR(100)) - City, Country
- website (VARCHAR(255)) - Personal website URL
- linkedin (VARCHAR(255)) - LinkedIn profile URL
- research_interests (TEXT) - Research areas
```

---

## 🔧 Backend Functions Added

### profile.php Functions:

1. **getUserProfile($userId)**
   - Fetches complete user profile
   - Returns all profile fields

2. **updateUserProfile($userId, $data)**
   - Updates user profile fields
   - Validates URLs
   - Sanitizes inputs (XSS protection)

3. **searchUsers($searchTerm)**
   - Searches users by name, email, department
   - Returns up to 50 results
   - Orders by name

4. **getUserPostCount($userId)**
   - Counts user's total posts

5. **getUserTotalLikes($userId)**
   - Counts total likes on user's posts

6. **getUserTotalViews($userId)**
   - Sums total views on user's posts

---

## 🎨 UI Updates

### Dashboard Changes:
- ✅ Bio preview (first 100 chars)
- ✅ Statistics grid (Posts, Likes, Views)
- ✅ "Edit Profile" button
- ✅ "Search Users" in navigation

### Navigation Updates:
All pages now have "Search Users" link

---

## 🔒 Security Features

✅ **Input Validation**
- URL format validation
- Length limits enforced
- Required field checks

✅ **XSS Protection**
- htmlspecialchars() on all outputs
- ENT_QUOTES flag used
- UTF-8 encoding

✅ **SQL Injection Prevention**
- PDO prepared statements
- Parameter binding

✅ **Access Control**
- requireLogin() on all pages
- Users can only edit own profile
- All profiles are viewable (public)

---

## 📝 Testing Checklist

### Test Edit Profile:
- [ ] Fill all fields and save
- [ ] Leave fields empty and save
- [ ] Enter invalid website URL
- [ ] Enter invalid LinkedIn URL
- [ ] Check bio character limit (500)
- [ ] Verify data saved in database

### Test User Search:
- [ ] Search by name
- [ ] Search by email
- [ ] Search by department
- [ ] Search with no results
- [ ] Click "View Profile" button

### Test Profile View:
- [ ] View own profile
- [ ] View other user's profile
- [ ] Check all fields display correctly
- [ ] Verify statistics are accurate
- [ ] Click on user's posts
- [ ] Test with user who has no bio
- [ ] Test with user who has no posts

---

## 🎉 Features Summary

**User Can:**
1. ✅ Add bio and personal details
2. ✅ Add research interests
3. ✅ Add contact information
4. ✅ Add website and LinkedIn links
5. ✅ Search for other users
6. ✅ View any user's profile
7. ✅ See user's research posts
8. ✅ View user statistics

**Dashboard Shows:**
1. ✅ Bio preview
2. ✅ Post count
3. ✅ Total likes received
4. ✅ Total views received
5. ✅ Edit profile button

---

## 🚀 Ready to Use!

All files created and ready. Just run the database update SQL and start using the features!

**Quick Start:**
1. Run SQL to add profile columns
2. Login to your account
3. Click "Edit Profile"
4. Fill in your details
5. Click "Search Users" to find others
6. View their profiles and posts
