<section class="content-view {{ $page === 'student-dashboard' ? 'is-active' : '' }}">
    <div class="page-heading"><h1>Welcome back, Student!</h1><p>Here is your academic overview.</p></div>
    <div class="metric-grid">
        <article class="stat-card"><span class="stat-icon indigo"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8"></path></svg></span><div><p>Enrolled Courses</p><strong data-student-metric="courses">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon green"><svg viewBox="0 0 24 24"><path d="m7 12 3 3 7-7"></path><circle cx="12" cy="12" r="9"></circle></svg></span><div><p>Completed Courses</p><strong data-student-metric="completed">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon purple"><svg viewBox="0 0 24 24"><path d="M4 10 12 6l8 4-8 4-8-4Z"></path></svg></span><div><p>Current GPA / Avg</p><strong data-student-metric="average">0</strong></div></article>
    </div>
    <section class="panel panel-after-grid"><h2>Recent Grades</h2><div class="mini-list" id="student-recent-grades"></div></section>
</section>

<section class="content-view {{ $page === 'student-browse' ? 'is-active' : '' }}">
    <div class="page-heading"><h1>Browse Courses</h1><p>Discover and enroll in new courses.</p></div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="studentBrowse" placeholder="Search courses..."></label>
        <div class="table-wrap"><table><thead><tr><th>Course</th><th>Description</th><th>Instructor</th><th>Duration</th><th>Action</th></tr></thead><tbody id="student-browse-table"></tbody></table></div>
    </section>
</section>

<section class="content-view {{ $page === 'student-enrollments' ? 'is-active' : '' }}">
    <div class="page-heading"><h1>My Enrollments</h1><p>Review current and completed course registrations.</p></div>
    <section class="panel"><div class="table-wrap"><table><thead><tr><th>Course</th><th>Enrollment Date</th><th>Status</th></tr></thead><tbody id="student-enrollment-table"></tbody></table></div></section>
</section>

<section class="content-view {{ $page === 'student-grades' ? 'is-active' : '' }}">
    <div class="page-heading"><h1>My Grades</h1><p>Track your academic performance across courses.</p></div>
    <section class="panel"><div class="table-wrap"><table><thead><tr><th>Course</th><th>Score</th><th>Letter Grade</th><th>Instructor</th><th>Date Graded</th></tr></thead><tbody id="student-grade-table"></tbody></table></div></section>
</section>
