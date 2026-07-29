# EduManage SIS

EduManage SIS is a role-based Student Information System built as a learning project for CS226. It provides separate portals for administrators, instructors, and students to manage academic records through a Laravel backend and a Blade-based frontend.

The application uses traditional Laravel page routing rather than a single-page application:

```text
Browser URL
    -> routes/web.php
    -> Blade page
    -> Vanilla JavaScript
    -> resources/js/sis/api.js
    -> routes/api.php
    -> Controller
    -> Eloquent model
    -> MySQL
```

## Features

### Authentication and accounts

- Sign in with an email address and password
- Laravel Sanctum token authentication for API requests
- Role-based authorization for `employee`, `instructor`, and `student`
- Change a default or existing password by providing:
  - Current password
  - New password
  - Password confirmation
- Sign out and revoke the current access token
- Edit the signed-in user's own profile
- No public user registration; administrators create user accounts
- New accounts receive the default password `11112222`

In this project, the `employee` database role represents the administrator portal. Employee and administrator are treated as the same application role.

### Administrator portal

Administrators can:

- View system statistics
- Manage students
- Manage courses
- Manage enrollments
- Manage grades
- Manage user accounts
- Edit their own profile

Create, edit, and delete modals use real Laravel web URLs, so browser refresh and back/forward navigation continue to work.

### Instructor portal

Instructors can:

- View assigned courses
- View students enrolled in their courses
- Assign and update grades for their own courses
- Edit their own profile

### Student portal

Students can:

- Browse available courses
- View and create their own enrollments
- View their own grades
- Edit their own profile information

### General UI behavior

- Database records are loaded through the API instead of frontend mock data
- List pages are paginated at 10 records per page
- Tables display user-friendly row numbers while database IDs remain available internally
- Delete operations use confirmation modals
- Sidebar navigation and active states are controlled by Laravel named routes

## Technology stack

- PHP 8.3 or later
- Laravel 13
- Laravel Sanctum
- MySQL
- Blade templates
- Vanilla JavaScript
- Tailwind CSS 4
- Vite 8
- PHPUnit 12

No React, Vue, or other SPA framework is used.

## Database structure

The main entities are:

- `users` — authentication details and application role
- `employees` — administrator/employee profile information
- `instructors` — instructor profile and department
- `students` — student contact and personal information
- `courses` — course details and assigned instructor
- `enrollments` — the relationship between students and courses
- `grades` — student scores and letter grades for courses

Each student, instructor, or employee profile belongs to a record in the `users` table.

## Installation

### 1. Install backend dependencies

```bash
composer install
```

### 2. Create the environment file

```bash
cp .env.example .env
php artisan key:generate
```

Configure the MySQL connection in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_information_system
DB_USERNAME=root
DB_PASSWORD=
```

Change the port, username, and password to match your local MySQL configuration.

### 3. Create and seed the database

```bash
php artisan migrate:fresh --seed
```

This command deletes existing tables before rebuilding and seeding them. Use `php artisan migrate --seed` if the database should not be reset.

### 4. Install frontend dependencies

```bash
npm install
```

## Running the application

Run Laravel:

```bash
php artisan serve
```

In another terminal, run Vite:

```bash
npm run dev
```

Then open:

```text
http://127.0.0.1:8000
```

You can also start the development services with:

```bash
composer run dev
```

## Seeded test data

`DatabaseSeeder` creates test records for each role and academic model, including:

- 10 employees/administrators
- 10 instructors
- 10 students
- 10 courses
- Enrollment and grade records

All seeded users have the default password:

```text
11112222
```

Example accounts:

| Portal | Email | Password |
| --- | --- | --- |
| Administrator | `admin@university.edu` | `11112222` |
| Instructor | `instructor1@university.edu` | `11112222` |
| Student | `student1@university.edu` | `11112222` |

After signing in, a user can replace the default password from `/change-password`.

## Frontend web routes

Laravel web routes display the Blade pages. Important examples include:

| Role | Page | URL |
| --- | --- | --- |
| Public | Landing page | `/` |
| Public | Sign in | `/signin` |
| Public | Change password | `/change-password` |
| Administrator | Dashboard | `/admin` |
| Administrator | Students | `/admin/students` |
| Administrator | Courses | `/admin/courses` |
| Administrator | Enrollments | `/admin/enrollments` |
| Administrator | Grades | `/admin/grades` |
| Administrator | User accounts | `/admin/users` |
| Administrator | Profile | `/admin/profile` |
| Instructor | Dashboard | `/instructor` |
| Instructor | My courses | `/instructor/courses` |
| Instructor | Grade management | `/instructor/grades` |
| Instructor | Profile | `/instructor/profile` |
| Student | Dashboard | `/student` |
| Student | Browse courses | `/student/courses` |
| Student | My enrollments | `/student/enrollments` |
| Student | My grades | `/student/grades` |
| Student | Profile | `/student/profile` |

Use the following command to see every registered web and API route:

```bash
php artisan route:list
```

## API overview

API routes return JSON and are separate from the Blade page routes.

### Authentication

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `POST` | `/api/auth/login` | Sign in and receive an access token |
| `POST` | `/api/auth/change-password` | Change a password using the current password |
| `GET` | `/api/auth/me` | Get the authenticated user |
| `POST` | `/api/auth/logout` | Revoke the current token |

Example login request:

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@university.edu","password":"11112222"}'
```

For protected endpoints, include the returned token:

```http
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Academic resources

| Resource | Base endpoint | Access summary |
| --- | --- | --- |
| Users | `/api/users` | Administrator management |
| Students | `/api/students` | Administrator management; students can access their own profile |
| Courses | `/api/courses` | Role-aware viewing; administrator management |
| Enrollments | `/api/enrollments` | Role-scoped viewing and authorized creation/management |
| Grades | `/api/grades` | Role-scoped viewing; administrators and assigned instructors manage grades |
| Profile | `/api/profile` | Authenticated users update their own information |

Use the normal REST methods:

- `GET` for listing or viewing records
- `POST` for creating records
- `PUT` or `PATCH` for updating records
- `DELETE` for deleting records

Authorization is enforced on the backend. The frontend UI is not treated as a security boundary.

## Project structure

```text
app/
  Http/Controllers/       API and authentication controllers
  Http/Middleware/        Role authorization middleware
  Models/                 Eloquent models and relationships
database/
  factories/              Test data factories
  migrations/             Database table definitions
  seeders/                Seeded users and academic data
resources/
  js/sis/api.js           API request helper
  js/sis/pages/           Page-specific frontend behavior
  views/                  Blade layouts, pages, and partials
routes/
  web.php                 Blade page routes
  api.php                 JSON API routes
tests/
  Feature/                Application and authorization tests
```

## Testing and verification

Run the backend test suite:

```bash
php artisan test
```

Build the frontend assets:

```bash
npm run build
```

Check the route configuration:

```bash
php artisan route:list
```

Feature tests verify API behavior, authentication, role authorization, pagination, profiles, and academic-management workflows.

## Learning focus

This project demonstrates:

- Laravel MVC and Eloquent relationships
- REST API design
- Token-based authentication
- Role-based access control
- Server-rendered Blade navigation
- Vanilla JavaScript API integration
- Form validation and JSON error handling
- Database factories, seeders, pagination, and feature testing

## License

This learning project is available under the [MIT License](https://opensource.org/licenses/MIT).
