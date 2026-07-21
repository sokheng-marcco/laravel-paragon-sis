<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#081224">

        <title>SIS Laravel</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div id="sis-app" class="app-shell">
            <aside class="sidebar" aria-label="Primary navigation">
                <div class="brand">
                    <span class="brand-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img">
                            <path d="M2.5 8.4 12 4l9.5 4.4L12 12.8 2.5 8.4Z"></path>
                            <path d="M6.8 10.6v4.1c0 1.4 2.3 2.7 5.2 2.7s5.2-1.3 5.2-2.7v-4.1"></path>
                        </svg>
                    </span>
                    <strong>EduManage SIS</strong>
                </div>

                <div class="nav-label" data-role-label>admin</div>
                <nav class="nav-list" aria-label="Admin sections">
                    <button class="nav-item is-active" type="button" data-view="dashboard" data-roles="admin">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"></path></svg>
                        </span>
                        Dashboard
                    </button>
                    <button class="nav-item" type="button" data-view="students" data-roles="admin">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4"></path><circle cx="12" cy="9" r="3"></circle><path d="M4 19c0-1.6 1-3 2.4-3.6M20 19c0-1.6-1-3-2.4-3.6"></path></svg>
                        </span>
                        Students
                    </button>
                    <button class="nav-item" type="button" data-view="courses" data-roles="admin">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                        </span>
                        Courses
                    </button>
                    <button class="nav-item" type="button" data-view="enrollments" data-roles="admin">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M8 12.5 11 15l5-6"></path><path d="M5 4h14v16H5z"></path></svg>
                        </span>
                        Enrollments
                    </button>
                    <button class="nav-item" type="button" data-view="grades" data-roles="admin">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="m12 4 2.2 4.5 4.8.7-3.5 3.4.8 4.8-4.3-2.3-4.3 2.3.8-4.8L5 9.2l4.8-.7L12 4Z"></path></svg>
                        </span>
                        Grades
                    </button>
                    <button class="nav-item" type="button" data-view="users" data-roles="admin">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M7 20v-2a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v2"></path><circle cx="12" cy="8" r="4"></circle></svg>
                        </span>
                        User Accounts
                    </button>
                    <button class="nav-item" type="button" data-view="profile" data-roles="admin">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.8-3.2 3.2-5 7-5s6.2 1.8 7 5"></path></svg>
                        </span>
                        Profile
                    </button>
                    <button class="nav-item" type="button" data-view="student-portal" data-roles="student">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"></path></svg>
                        </span>
                        Dashboard
                    </button>
                    <button class="nav-item" type="button" data-view="student-browse" data-roles="student">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                        </span>
                        Browse Courses
                    </button>
                    <button class="nav-item" type="button" data-view="student-enrollments" data-roles="student">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M8 12.5 11 15l5-6"></path><path d="M5 4h14v16H5z"></path></svg>
                        </span>
                        My Enrollments
                    </button>
                    <button class="nav-item" type="button" data-view="student-grades" data-roles="student">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="m12 4 2.2 4.5 4.8.7-3.5 3.4.8 4.8-4.3-2.3-4.3 2.3.8-4.8L5 9.2l4.8-.7L12 4Z"></path></svg>
                        </span>
                        My Grades
                    </button>
                    <button class="nav-item" type="button" data-view="profile" data-roles="student">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.8-3.2 3.2-5 7-5s6.2 1.8 7 5"></path></svg>
                        </span>
                        Profile
                    </button>
                    <button class="nav-item" type="button" data-view="instructor-portal" data-roles="instructor">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"></path></svg>
                        </span>
                        Dashboard
                    </button>
                    <button class="nav-item" type="button" data-view="instructor-courses" data-roles="instructor">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                        </span>
                        My Courses
                    </button>
                    <button class="nav-item" type="button" data-view="instructor-grades" data-roles="instructor">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 6h16M6 10h12M8 14h8M10 18h4"></path></svg>
                        </span>
                        Grade Management
                    </button>
                    <button class="nav-item" type="button" data-view="profile" data-roles="instructor">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.8-3.2 3.2-5 7-5s6.2 1.8 7 5"></path></svg>
                        </span>
                        Profile
                    </button>
                </nav>
            </aside>

            <main class="main-content">
                <header class="topbar">
                    <button class="menu-button" type="button" data-mobile-menu aria-label="Open navigation">
                        <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
                    </button>

                    <label class="search-control">
                        <input id="global-search" type="search" placeholder="Search anywhere...">
                    </label>

                    <div class="topbar-actions">
                        <label class="role-switcher">
                            <span>Preview</span>
                            <select id="role-preview">
                                <option value="admin">Admin</option>
                                <option value="student">Student</option>
                                <option value="instructor">Instructor</option>
                            </select>
                        </label>
                        <button class="notification-button" type="button" aria-label="Notifications">
                            <svg viewBox="0 0 24 24"><path d="M18 16v-5a6 6 0 0 0-12 0v5l-2 2h16l-2-2Z"></path><path d="M10 20h4"></path></svg>
                            <span></span>
                        </button>
                        <div class="admin-menu">
                            <span class="admin-avatar">S</span>
                            <div>
                                <strong data-current-name>System Admin</strong>
                                <small data-current-role>admin</small>
                            </div>
                            <svg viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"></path></svg>
                        </div>
                    </div>
                </header>

                <section class="content-view is-active" data-panel="dashboard">
                    <div class="page-heading">
                        <h1>Admin Dashboard</h1>
                        <p>Overview of university operations and performance.</p>
                    </div>

                    <div class="metric-grid">
                        <article class="stat-card">
                            <span class="stat-icon blue">
                                <svg viewBox="0 0 24 24"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4"></path><circle cx="12" cy="9" r="3"></circle></svg>
                            </span>
                            <div>
                                <p>Total Students</p>
                                <strong data-metric="students">0</strong>
                            </div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon purple">
                                <svg viewBox="0 0 24 24"><path d="M4 10 12 6l8 4-8 4-8-4Z"></path><path d="M6 12v4c1.8 1.2 3.8 1.8 6 1.8s4.2-.6 6-1.8v-4"></path></svg>
                            </span>
                            <div>
                                <p>Total Instructors</p>
                                <strong data-metric="instructors">0</strong>
                            </div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon indigo">
                                <svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                            </span>
                            <div>
                                <p>Total Courses</p>
                                <strong data-metric="courses">0</strong>
                            </div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon cyan">
                                <svg viewBox="0 0 24 24"><path d="M7 5h10v14H7z"></path><path d="M10 9h4M10 13h4"></path></svg>
                            </span>
                            <div>
                                <p>Total Enrollments</p>
                                <strong data-metric="enrollments">0</strong>
                            </div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon green">
                                <svg viewBox="0 0 24 24"><path d="m7 12 3 3 7-7"></path><circle cx="12" cy="12" r="9"></circle></svg>
                            </span>
                            <div>
                                <p>Completed Courses</p>
                                <strong data-metric="completed">0</strong>
                            </div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon orange">
                                <svg viewBox="0 0 24 24"><path d="M5 17 10 12l3 3 6-7"></path><path d="M15 8h4v4"></path></svg>
                            </span>
                            <div>
                                <p>Average Grade</p>
                                <strong data-metric="average-score">0%</strong>
                            </div>
                        </article>
                    </div>

                    <div class="dashboard-panels">
                        <section class="panel">
                            <h2>Recent Enrollments</h2>
                            <div class="mini-list" id="recent-enrollments"></div>
                        </section>
                        <section class="panel">
                            <h2>Recent Grades</h2>
                            <div class="mini-list" id="recent-grades"></div>
                        </section>
                    </div>
                </section>

                <section class="content-view" data-panel="students">
                    <div class="page-heading row-heading">
                        <div>
                            <h1>Students</h1>
                            <p>Manage student records and personal information.</p>
                        </div>
                        <button class="primary-action" type="button" data-open-student-form>
                            + Add Student
                        </button>
                    </div>
                    <section class="panel">
                        <label class="table-search">
                            <svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg>
                            <input id="student-search" type="search" placeholder="Search by Student ID...">
                        </label>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>DOB</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="student-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="courses">
                    <div class="page-heading row-heading">
                        <div>
                            <h1>Courses</h1>
                            <p>Manage course catalog and instructor assignments.</p>
                        </div>
                    </div>
                    <section class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Course ID</th>
                                        <th>Course Name</th>
                                        <th>Instructor</th>
                                        <th>Duration</th>
                                        <th>Enrolled</th>
                                    </tr>
                                </thead>
                                <tbody id="course-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="enrollments">
                    <div class="page-heading">
                        <h1>Enrollments</h1>
                        <p>Review course registration and enrollment status.</p>
                    </div>
                    <section class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Enrollment ID</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="enrollment-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="grades">
                    <div class="page-heading">
                        <h1>Grades</h1>
                        <p>Track student assessment results.</p>
                    </div>
                    <section class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Grade ID</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Graded By</th>
                                    </tr>
                                </thead>
                                <tbody id="grade-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="users">
                    <div class="page-heading">
                        <h1>User Accounts</h1>
                        <p>View sample system users by role.</p>
                    </div>
                    <section class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody id="user-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="profile">
                    <div class="page-heading">
                        <h1>Profile</h1>
                        <p>System administrator account preview.</p>
                    </div>
                    <section class="panel profile-panel">
                        <span class="profile-avatar" data-profile-initial>S</span>
                        <div>
                            <h2 data-profile-name>System Admin</h2>
                            <p data-profile-email>admin@university.edu</p>
                            <span data-profile-role>Role: administrator</span>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="student-portal">
                    <div class="page-heading">
                        <h1>Student Portal</h1>
                        <p>Personal courses, grades, and enrollment status.</p>
                    </div>
                    <div class="metric-grid">
                        <article class="stat-card">
                            <span class="stat-icon blue"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 9h8M8 13h6"></path></svg></span>
                            <div><p>My Courses</p><strong data-student-metric="courses">0</strong></div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon green"><svg viewBox="0 0 24 24"><path d="m7 12 3 3 7-7"></path><circle cx="12" cy="12" r="9"></circle></svg></span>
                            <div><p>Completed</p><strong data-student-metric="completed">0</strong></div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon orange"><svg viewBox="0 0 24 24"><path d="M5 17 10 12l3 3 6-7"></path><path d="M15 8h4v4"></path></svg></span>
                            <div><p>Average</p><strong data-student-metric="average">0%</strong></div>
                        </article>
                    </div>
                    <div class="dashboard-panels">
                        <section class="panel">
                            <h2>Recent Grades</h2>
                            <div class="mini-list" id="student-recent-grades"></div>
                        </section>
                    </div>
                </section>

                <section class="content-view" data-panel="student-browse">
                    <div class="page-heading">
                        <h1>Browse Courses</h1>
                        <p>Discover and enroll in new courses.</p>
                    </div>
                    <section class="panel">
                        <label class="table-search">
                            <svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg>
                            <input type="search" placeholder="Search courses...">
                        </label>
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Course</th><th>Description</th><th>Instructor</th><th>Duration</th></tr></thead>
                                <tbody id="student-browse-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="student-enrollments">
                    <div class="page-heading">
                        <h1>My Enrollments</h1>
                        <p>Review current and completed course registrations.</p>
                    </div>
                    <section class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Course</th><th>Enrollment Date</th><th>Status</th></tr></thead>
                                <tbody id="student-enrollment-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="student-grades">
                    <div class="page-heading">
                        <h1>My Grades</h1>
                        <p>Track your academic performance across courses.</p>
                    </div>
                    <section class="panel">
                            <div class="table-wrap">
                                <table>
                                    <thead><tr><th>Course</th><th>Score</th><th>Letter Grade</th><th>Instructor</th><th>Date Graded</th></tr></thead>
                                    <tbody id="student-grade-table"></tbody>
                                </table>
                            </div>
                    </section>
                </section>

                <section class="content-view" data-panel="instructor-portal">
                    <div class="page-heading">
                        <h1>Instructor Portal</h1>
                        <p>Assigned courses, enrolled students, and grade work.</p>
                    </div>
                    <div class="metric-grid">
                        <article class="stat-card">
                            <span class="stat-icon indigo"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8"></path></svg></span>
                            <div><p>Assigned Courses</p><strong data-instructor-metric="courses">0</strong></div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon blue"><svg viewBox="0 0 24 24"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4"></path><circle cx="12" cy="9" r="3"></circle></svg></span>
                            <div><p>Students</p><strong data-instructor-metric="students">0</strong></div>
                        </article>
                        <article class="stat-card">
                            <span class="stat-icon purple"><svg viewBox="0 0 24 24"><path d="m12 4 2.2 4.5 4.8.7-3.5 3.4.8 4.8-4.3-2.3-4.3 2.3.8-4.8L5 9.2l4.8-.7L12 4Z"></path></svg></span>
                            <div><p>Grades Posted</p><strong data-instructor-metric="grades">0</strong></div>
                        </article>
                    </div>
                    <section class="panel panel-after-grid">
                        <h2>Recent Courses</h2>
                        <div class="mini-list" id="instructor-recent-courses"></div>
                    </section>
                </section>

                <section class="content-view" data-panel="instructor-courses">
                    <div class="page-heading">
                        <h1>My Courses</h1>
                        <p>View and manage the courses you are teaching.</p>
                    </div>
                    <section class="panel">
                        <label class="table-search">
                            <svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg>
                            <input type="search" placeholder="Search courses...">
                        </label>
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Course ID</th><th>Course Name</th><th>Description</th><th>Duration</th><th>Enrolled Students</th></tr></thead>
                                <tbody id="instructor-course-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section class="content-view" data-panel="instructor-grades">
                    <div class="page-heading">
                        <h1>Grade Management</h1>
                        <p>Assign and update grades for students in your courses.</p>
                    </div>
                    <section class="panel">
                        <label class="table-search">
                            <svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg>
                            <input type="search" placeholder="Search by student name...">
                        </label>
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Student</th><th>Course</th><th>Score</th><th>Grade</th><th>Status</th><th>Actions</th></tr></thead>
                                <tbody id="instructor-grade-table"></tbody>
                            </table>
                        </div>
                    </section>
                </section>


                <footer class="footer">© 2026 EduManage SIS. All rights reserved.</footer>
            </main>
        </div>

        <div class="modal-backdrop" id="student-modal" aria-hidden="true">
            <section class="modal" role="dialog" aria-modal="true" aria-labelledby="student-modal-title">
                <button class="modal-close" type="button" data-close-student-form aria-label="Close">×</button>
                <h2 id="student-modal-title">Add Student</h2>
                <form id="student-form" class="student-form">
                    <label>
                        User Account
                        <input name="fullName" required placeholder="Student name">
                    </label>
                    <label>
                        Email
                        <input name="email" type="email" required placeholder="student@university.edu">
                    </label>
                    <label>
                        Phone Number
                        <input name="phone" placeholder="555-0100">
                    </label>
                    <label>
                        Address
                        <input name="address" placeholder="Phnom Penh">
                    </label>
                    <label>
                        Date of Birth
                        <input name="dob" type="date">
                    </label>
                    <div class="form-actions">
                        <button class="secondary-action" type="button" data-close-student-form>Cancel</button>
                        <button class="primary-action" type="submit">Save Student</button>
                    </div>
                </form>
            </section>
        </div>
    </body>
</html>
