<section class="content-view {{ $page === 'instructor-dashboard' ? 'is-active' : '' }}">
    <div class="page-heading"><h1>Instructor Dashboard</h1><p>Manage your courses and student performance.</p></div>
    <div class="metric-grid">
        <article class="stat-card"><span class="stat-icon indigo"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8"></path></svg></span><div><p>Assigned Courses</p><strong data-instructor-metric="courses">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon purple"><svg viewBox="0 0 24 24"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4"></path><circle cx="12" cy="9" r="3"></circle></svg></span><div><p>Total Students</p><strong data-instructor-metric="students">0</strong></div></article>
        <article class="stat-card"><span class="stat-icon orange"><svg viewBox="0 0 24 24"><path d="m12 4 2.2 4.5 4.8.7-3.5 3.4.8 4.8-4.3-2.3-4.3 2.3.8-4.8L5 9.2l4.8-.7L12 4Z"></path></svg></span><div><p>Pending Grading</p><strong data-instructor-metric="grades">0</strong></div></article>
    </div>
    <section class="panel panel-after-grid"><h2>Recent Courses</h2><div class="mini-list" id="instructor-recent-courses"></div></section>
</section>

<section class="content-view {{ $page === 'instructor-courses' ? 'is-active' : '' }}">
    <div class="page-heading"><h1>My Courses</h1><p>View and manage the courses you are teaching.</p></div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="instructorCourses" placeholder="Search courses..."></label>
        <div class="table-wrap"><table><thead><tr><th>N°</th><th>Course Name</th><th>Description</th><th>Duration</th><th>Enrolled Students</th></tr></thead><tbody id="instructor-course-table"></tbody></table></div>
    </section>
</section>

<section class="content-view {{ $page === 'instructor-grades' ? 'is-active' : '' }}">
    <div class="page-heading"><h1>Grade Management</h1><p>Assign and update grades for students in your courses.</p></div>
    <section class="panel">
        <label class="table-search"><svg viewBox="0 0 24 24"><path d="m21 21-4.3-4.3"></path><circle cx="11" cy="11" r="7"></circle></svg><input type="search" data-table-search="instructorGrades" placeholder="Search by student name..."></label>
        <div class="table-wrap"><table><thead><tr><th>Student</th><th>Course</th><th>Score</th><th>Grade</th><th>Status</th><th>Actions</th></tr></thead><tbody id="instructor-grade-table"></tbody></table></div>
    </section>
</section>
