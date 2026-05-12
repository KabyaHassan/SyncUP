# SyncUP | Collaborative Study Group System

SyncUP is a modern, full-stack web application designed to help students organize study groups, share academic resources, and track progress through a gamified leaderboard. Originally a CLI-based system, it has been completely modernized into a high-performance Single Page Application (SPA).

##  Features

- **Professional Landing Page:** High-conversion entry point with clear feature highlights.
- **Dynamic Dashboard:** Real-time statistics across all academic modules.
- **Student Management:** Full CRUD operations for managing student profiles.
- **Study Groups & Courses:** Create course-specific groups and manage memberships.
- **Join Request System:** Seamless workflow for joining and approving group members.
- **Resource Repository:** Upload and share notes, slides, and study materials.
- **Session & Attendance:** Schedule study sessions and track attendance.
- **Gamified Leaderboard:** Points-based ranking system driven by attendance triggers.
- **Admin Control Panel:** Superuser access to manage system-wide user roles and status.
- **Dark/Light Mode:** Full theme persistence for a personalized user experience.

## Tech Stack

- **Frontend:** HTML5, Tailwind CSS (CDN), Vanilla JavaScript (SPA Architecture).
- **Backend:** PHP 8.x (Pure PHP with PDO).
- **Database:** MySQL / MariaDB (Relational schema with Triggers).
- **Icons:** Lucide Icons.

## Installation & Setup

### 1. Database Setup
Import the provided SQL schema into your MySQL/MariaDB server:
```bash
mysql -u root -p < db.sql
```
This will create the `StudyGroupSystem` database and populate it with sample data, including the Admin user.

### 2. Configuration
Ensure your database credentials in `config/db.php` match your local environment:
```php
$host = 'localhost';
$user = 'root';
$pass = 'your_password';
$dbname = 'StudyGroupSystem';
```

### 3. Run Locally
Start the PHP built-in development server from the project root:
```bash
php -S localhost:8000
```
Open your browser and navigate to `http://localhost:8000`.

##  Admin Access
To access the **Admin Panel**, use the following credentials:
- **Student ID:** `admin`
- **Password:** `admin123`

##  Project Structure
- `/api`: Backend PHP endpoints (CRUD logic).
- `/pages`: Frontend HTML modules for the SPA.
- `/assets`: JavaScript and CSS assets.
- `index.html`: Landing Page.
- `dashboard.html`: Main Application Shell.
- `db.sql`: Database Schema and Sample Data.

---
*Built as a final project for CSE370 (Database Systems).*
