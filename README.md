# SyncUP | Collaborative Study Group System

SyncUP is a full-stack web application that helps students organize study groups, share academic resources, and track progress through a gamified leaderboard. Built as a Single Page Application (SPA).

*Final project for CSE370 – Database Systems.*

---

## Features

- **Landing Page** — Clean entry point with feature highlights and dark/light mode toggle
- **Dashboard** — Real-time statistics across all modules
- **Student Management** — Full CRUD for managing student profiles
- **Courses & Study Groups** — Create course-specific groups (public or private)
- **Join Request System** — Students request to join groups; admins approve or reject
- **Resource Sharing** — Upload and share notes, PDFs, videos, and links
- **Session Scheduling** — Schedule study sessions with date, time, topic, and platform link
- **Attendance Tracking** — Mark and track attendance per session
- **Leaderboard** — Points-based ranking system; attending a session awards +10 points automatically via a DB trigger
- **Admin Panel** — Superuser access to manage all users and roles
- **Dark / Light Mode** — Theme preference saved in `localStorage`

---

## Tech Stack

| | |
|---|---|
| **Frontend** | HTML5, Tailwind CSS, Vanilla JavaScript (SPA) |
| **Backend** | PHP 8.x with PDO |
| **Database** | MySQL / MariaDB (with triggers & indexes) |
| **Icons** | Lucide Icons |

---

## Setup

### 1. Import the Database
Run this command to create the database with all tables, sample data, and the leaderboard trigger:
```bash
mysql -u root -p < db.sql
```

### 2. Configure Database Credentials
Edit `config/db.php`:
```php
$host   = 'localhost';
$user   = 'root';
$pass   = 'your_password';
$dbname = 'StudyGroupSystem';
```

### 3. Start the Server
```bash
php -S localhost:8000
```
Visit **http://localhost:8000** in your browser.

---

## Default Login Credentials

| Role | Student ID | Password |
|---|---|---|
| **Admin** | `admin` | `admin123` |
| Student (sample) | `2021001` | `pass` |
| Student (sample) | `2021002` | `pass` |

---

## Project Structure

```
/
├── index.html          # Landing page
├── dashboard.html      # Main app shell (login + SPA)
├── db.sql              # Database schema & sample data
│
├── api/                # PHP backend endpoints (JSON)
│   ├── auth/           # Login, logout, session, profile update
│   ├── student/        # Student CRUD
│   ├── course/         # Course CRUD
│   ├── group/          # Study group CRUD
│   ├── joinRequest/    # Join request workflow
│   ├── member/         # Group member management
│   ├── resource/       # Resource sharing
│   ├── session/        # Study session scheduling
│   ├── attendance/     # Attendance tracking
│   └── leaderboard/    # Leaderboard data
│
├── pages/              # HTML partials loaded by the SPA router
├── assets/js/          # app.js (core logic) + vendor libraries
└── config/             # db.php (connection) & auth.php (authorization)
```

---

## Authorization

- **System Admin** — full access to everything, including the Admin Panel
- **Group Admin** — can manage members, resources, sessions, and attendance within their group
- **General Member** — can browse group content and submit join requests
