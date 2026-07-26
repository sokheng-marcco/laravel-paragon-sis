<section class="content-view" data-panel="admin-dashboard">
    <div class="page-heading">
        <h1>Admin Dashboard</h1>
        <p>Overview of university operations and performance.</p>
    </div>
    <div class="metric-grid">
        <article class="stat-card"><span class="stat-icon blue"><svg viewBox="0 0 24 24"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4"></path><circle cx="12" cy="9" r="3"></circle></svg></span><div><p>Total Students</p><strong data-metric="students">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon purple"><svg viewBox="0 0 24 24"><path d="M4 10 12 6l8 4-8 4-8-4Z"></path><path d="M6 12v4c1.8 1.2 3.8 1.8 6 1.8s4.2-.6 6-1.8v-4"></path></svg></span><div><p>Total Instructors</p><strong data-metric="instructors">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon indigo"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg></span><div><p>Total Courses</p><strong data-metric="courses">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon cyan"><svg viewBox="0 0 24 24"><path d="M7 5h10v14H7z"></path><path d="M10 9h4M10 13h4"></path></svg></span><div><p>Total Enrollments</p><strong data-metric="enrollments">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon green"><svg viewBox="0 0 24 24"><path d="m7 12 3 3 7-7"></path><circle cx="12" cy="12" r="9"></circle></svg></span><div><p>Completed Courses</p><strong data-metric="completed">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon orange"><svg viewBox="0 0 24 24"><path d="M5 17 10 12l3 3 6-7"></path><path d="M15 8h4v4"></path></svg></span><div><p>Average Grade</p><strong data-metric="average-score">0%</strong></div></article>
    </div>
    <div class="dashboard-panels">
        <section class="panel"><h2>Recent Enrollments</h2><div class="mini-list" id="recent-enrollments"></div></section>
        <section class="panel"><h2>Recent Grades</h2><div class="mini-list" id="recent-grades"></div></section>
    </div>
</section>

<section class="content-view" data-panel="admin-students">
    <div class="page-heading row-heading">
        <div><h1>Students</h1><p>Manage student records and personal information.</p></div>
        <button class="primary-action" type="button" data-open-modal="student">+ Add Student</button>
    </div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="students" placeholder="Search by Student ID..."></label>
        <div class="table-wrap"><table><thead><tr><th>Student ID</th><th>Name</th><th>Email</th><th>Phone</th><th>DOB</th><th>Actions</th></tr></thead><tbody id="student-table"></tbody></table></div>
    </section>
</section>

<section class="content-view" data-panel="admin-courses">
    <div class="page-heading row-heading">
        <div><h1>Courses</h1><p>Manage academic courses and assignments.</p></div>
        <button class="primary-action" type="button" data-open-modal="course">+ Add Course</button>
    </div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="courses" placeholder="Search courses..."></label>
        <div class="table-wrap"><table><thead><tr><th>Course ID</th><th>Course Name</th><th>Description</th><th>Instructor</th><th>Duration</th><th>Actions</th></tr></thead><tbody id="course-table"></tbody></table></div>
    </section>
</section>

<section class="content-view" data-panel="admin-enrollments">
    <div class="page-heading row-heading">
        <div><h1>Enrollments</h1><p>Manage student course registrations.</p></div>
        <button class="primary-action" type="button" data-open-modal="enrollment">+ Add Enrollment</button>
    </div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="enrollments" placeholder="Search by Student ID..."></label>
        <div class="table-wrap"><table><thead><tr><th>Enrollment ID</th><th>Student</th><th>Course</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody id="enrollment-table"></tbody></table></div>
    </section>
</section>

<section class="content-view" data-panel="admin-grades">
    <div class="page-heading"><h1>Grades</h1><p>Manage student grades and academic records.</p></div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="grades" placeholder="Search by Student ID..."></label>
        <div class="table-wrap"><table><thead><tr><th>Grade ID</th><th>Student</th><th>Course</th><th>Score</th><th>Grade</th><th>Instructor</th></tr></thead><tbody id="grade-table"></tbody></table></div>
    </section>
</section>

<section class="content-view" data-panel="admin-users">
    <div class="page-heading row-heading">
        <div><h1>User Accounts</h1><p>Manage login accounts by role.</p></div>
        <button class="primary-action" type="button" data-open-modal="user">+ Add User</button>
    </div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="users" placeholder="Search users..."></label>
        <div class="table-wrap"><table><thead><tr><th>User ID</th><th>Full Name</th><th>Email</th><th>Role</th><th>Created At</th><th>Actions</th></tr></thead><tbody id="user-table"></tbody></table></div>
    </section>
</section>
