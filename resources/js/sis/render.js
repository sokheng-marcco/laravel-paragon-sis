import {
    courseName,
    courses,
    enrollments,
    formatDate,
    grades,
    instructorName,
    instructors,
    matchesSearch,
    profiles,
    state,
    studentName,
    students,
    userById,
    users,
} from './data';

const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => [...document.querySelectorAll(selector)];
const tbody = (id) => document.getElementById(id);

const actionButtons = (entity, id) => `
    <span class="table-actions">
        <button class="table-action" type="button" data-edit="${entity}" data-id="${id}" aria-label="Edit">
            <svg viewBox="0 0 24 24"><path d="M4 20h4L18.5 9.5a2.1 2.1 0 0 0-3-3L5 17v3Z"></path></svg>
        </button>
        <button class="table-action delete" type="button" data-delete="${entity}" data-id="${id}" aria-label="Delete">
            <svg viewBox="0 0 24 24"><path d="M5 7h14M10 11v6M14 11v6M8 7l1-3h6l1 3M7 7l1 13h8l1-13"></path></svg>
        </button>
    </span>
`;

const emptyRow = (columns) => `<tr><td colspan="${columns}" class="empty-state">No records found.</td></tr>`;
const queryFor = (key) => `${state.globalSearch} ${state.tableSearches[key] ?? ''}`.trim();
const average = (items) => items.length ? items.reduce((sum, item) => sum + Number(item.score), 0) / items.length : 0;

export function showScreen(screen) {
    state.screen = screen;
    $$('[data-screen]').forEach((element) => {
        element.classList.toggle('is-hidden', element.dataset.screen !== screen);
    });
}

export function showPanel(panel) {
    state.currentPanel = panel;
    $$('.content-view').forEach((view) => view.classList.toggle('is-active', view.dataset.panel === panel));
    $$('.nav-item').forEach((item) => item.classList.toggle('is-active', item.dataset.view === panel));
}

export function renderAccount() {
    const profile = profiles[state.currentRole];
    const initial = profile.name.charAt(0).toUpperCase();

    $$('[data-roles]').forEach((item) => {
        item.hidden = !item.dataset.roles.split(' ').includes(state.currentRole);
    });

    $('[data-role-label]').textContent = state.currentRole;
    $('[data-current-name]').textContent = profile.name;
    $('[data-current-role]').textContent = profile.role;
    $('.admin-avatar').textContent = initial;
    $('[data-profile-initial]').textContent = initial;
    $('[data-profile-name]').textContent = profile.name;
    $('[data-profile-email]').textContent = profile.email;
    $('[data-profile-role]').textContent = profile.role;
    $('[data-profile-phone]').textContent = profile.phone;
    $('[data-profile-department]').textContent = profile.department;
    $('[data-profile-address]').textContent = profile.address;
}

export function renderAdmin() {
    $('[data-metric="students"]').textContent = students.length;
    $('[data-metric="instructors"]').textContent = instructors.length;
    $('[data-metric="courses"]').textContent = courses.length;
    $('[data-metric="enrollments"]').textContent = enrollments.length;
    $('[data-metric="completed"]').textContent = enrollments.filter((item) => item.status === 'completed').length;
    $('[data-metric="average-score"]').textContent = `${average(grades).toFixed(1)}%`;

    tbody('recent-enrollments').innerHTML = enrollments.slice(0, 5).map((item) => `
        <div class="mini-item"><div><strong>${studentName(item.studentId)}</strong><span>${courseName(item.courseId)}</span></div><span class="status-pill ${item.status}">${item.status}</span></div>
    `).join('');

    tbody('recent-grades').innerHTML = grades.slice(0, 5).map((item) => `
        <div class="mini-item"><div><strong>${studentName(item.studentId)}</strong><span>${courseName(item.courseId)}</span></div><strong>${item.grade}</strong></div>
    `).join('');

    const studentRows = students
        .map((student) => ({ ...student, user: userById(student.userId) }))
        .filter((student) => matchesSearch([student.studentId, student.user?.fullName, student.user?.email, student.phone], queryFor('students')));
    tbody('student-table').innerHTML = studentRows.map((student) => `
        <tr><td>${student.studentId}</td><td>${student.user.fullName}</td><td>${student.user.email}</td><td>${student.phone}</td><td>${formatDate(student.dob)}</td><td>${actionButtons('student', student.studentId)}</td></tr>
    `).join('') || emptyRow(6);

    const courseRows = courses.filter((course) => matchesSearch([course.courseId, course.courseName, course.description, instructorName(course.instructorId)], queryFor('courses')));
    tbody('course-table').innerHTML = courseRows.map((course) => `
        <tr><td>${course.courseId}</td><td>${course.courseName}</td><td>${course.description}</td><td>${instructorName(course.instructorId)}</td><td>${course.duration} weeks</td><td>${actionButtons('course', course.courseId)}</td></tr>
    `).join('') || emptyRow(6);

    const enrollmentRows = enrollments.filter((item) => matchesSearch([item.enrollmentId, studentName(item.studentId), courseName(item.courseId), item.status], queryFor('enrollments')));
    tbody('enrollment-table').innerHTML = enrollmentRows.map((item) => `
        <tr><td>${item.enrollmentId}</td><td>${studentName(item.studentId)}</td><td>${courseName(item.courseId)}</td><td>${formatDate(item.enrollmentDate)}</td><td><span class="status-pill ${item.status}">${item.status}</span></td><td>${actionButtons('enrollment', item.enrollmentId)}</td></tr>
    `).join('') || emptyRow(6);

    const gradeRows = grades.filter((item) => matchesSearch([item.gradeId, studentName(item.studentId), courseName(item.courseId), item.grade], queryFor('grades')));
    tbody('grade-table').innerHTML = gradeRows.map((item) => `
        <tr><td>${item.gradeId}</td><td>${studentName(item.studentId)}</td><td>${courseName(item.courseId)}</td><td>${Number(item.score).toFixed(1)}%</td><td>${item.grade}</td><td>${instructorName(item.gradedBy)}</td></tr>
    `).join('') || emptyRow(6);

    const userRows = users.filter((user) => matchesSearch([user.id, user.fullName, user.email, user.role], queryFor('users')));
    tbody('user-table').innerHTML = userRows.map((user) => `
        <tr><td>${user.id}</td><td>${user.fullName}</td><td>${user.email}</td><td>${user.role}</td><td>${formatDate(user.createdAt)}</td><td>${actionButtons('user', user.id)}</td></tr>
    `).join('') || emptyRow(6);
}

export function renderStudent() {
    const studentId = profiles.student.studentId;
    const myEnrollments = enrollments.filter((item) => item.studentId === studentId);
    const myGrades = grades.filter((item) => item.studentId === studentId);

    $('[data-student-metric="courses"]').textContent = myEnrollments.length;
    $('[data-student-metric="completed"]').textContent = myEnrollments.filter((item) => item.status === 'completed').length;
    $('[data-student-metric="average"]').textContent = `${average(myGrades).toFixed(1)}%`;

    tbody('student-recent-grades').innerHTML = myGrades.slice(0, 5).map((grade) => `
        <div class="mini-item"><div><strong>${courseName(grade.courseId)}</strong><span>Graded on ${formatDate(grade.gradedAt)}</span></div><strong>${grade.grade}</strong></div>
    `).join('');

    const browseRows = courses.filter((course) => matchesSearch([course.courseName, course.description, instructorName(course.instructorId)], queryFor('studentBrowse')));
    tbody('student-browse-table').innerHTML = browseRows.map((course) => {
        const enrolled = enrollments.some((item) => item.studentId === studentId && item.courseId === course.courseId);
        return `<tr><td>${course.courseName}</td><td>${course.description}</td><td>${instructorName(course.instructorId)}</td><td>${course.duration} weeks</td><td><button class="${enrolled ? 'secondary-action' : 'primary-action'} compact" type="button" data-enroll="${course.courseId}" ${enrolled ? 'disabled' : ''}>${enrolled ? 'Enrolled' : 'Enroll'}</button></td></tr>`;
    }).join('') || emptyRow(5);

    tbody('student-enrollment-table').innerHTML = myEnrollments.map((item) => `
        <tr><td>${courseName(item.courseId)}</td><td>${formatDate(item.enrollmentDate)}</td><td><span class="status-pill ${item.status}">${item.status}</span></td></tr>
    `).join('') || emptyRow(3);

    tbody('student-grade-table').innerHTML = myGrades.map((item) => `
        <tr><td>${courseName(item.courseId)}</td><td>${Number(item.score).toFixed(1)}%</td><td>${item.grade}</td><td>${instructorName(item.gradedBy)}</td><td>${formatDate(item.gradedAt)}</td></tr>
    `).join('') || emptyRow(5);
}

export function renderInstructor() {
    const instructorId = profiles.instructor.instructorId;
    const myCourses = courses.filter((course) => course.instructorId === instructorId);
    const courseIds = myCourses.map((course) => course.courseId);
    const myEnrollments = enrollments.filter((item) => courseIds.includes(item.courseId));
    const gradedPairs = grades.map((grade) => `${grade.studentId}:${grade.courseId}`);

    $('[data-instructor-metric="courses"]').textContent = myCourses.length;
    $('[data-instructor-metric="students"]').textContent = new Set(myEnrollments.map((item) => item.studentId)).size;
    $('[data-instructor-metric="grades"]').textContent = myEnrollments.filter((item) => !gradedPairs.includes(`${item.studentId}:${item.courseId}`)).length;

    tbody('instructor-recent-courses').innerHTML = myCourses.map((course) => `
        <div class="mini-item"><div><strong>${course.courseName}</strong><span>${course.description}</span></div><strong>${course.duration}w</strong></div>
    `).join('');

    const courseRows = myCourses.filter((course) => matchesSearch([course.courseName, course.description], queryFor('instructorCourses')));
    tbody('instructor-course-table').innerHTML = courseRows.map((course) => `
        <tr><td>${course.courseId}</td><td>${course.courseName}</td><td>${course.description}</td><td>${course.duration} weeks</td><td>${enrollments.filter((item) => item.courseId === course.courseId).length}</td></tr>
    `).join('') || emptyRow(5);

    const gradeRows = myEnrollments
        .map((item) => ({ enrollment: item, grade: grades.find((grade) => grade.studentId === item.studentId && grade.courseId === item.courseId) }))
        .filter(({ enrollment, grade }) => matchesSearch([studentName(enrollment.studentId), courseName(enrollment.courseId), grade?.grade ?? 'pending'], queryFor('instructorGrades')));

    tbody('instructor-grade-table').innerHTML = gradeRows.map(({ enrollment, grade }) => `
        <tr>
            <td>${studentName(enrollment.studentId)}</td>
            <td>${courseName(enrollment.courseId)}</td>
            <td>${grade ? `${Number(grade.score).toFixed(1)}%` : '-'}</td>
            <td>${grade?.grade ?? '-'}</td>
            <td><span class="status-pill ${grade ? 'completed' : 'pending'}">${grade ? 'graded' : 'pending'}</span></td>
            <td><button class="primary-action compact" type="button" data-grade-student="${enrollment.studentId}" data-grade-course="${enrollment.courseId}">${grade ? 'Update' : 'Assign'}</button></td>
        </tr>
    `).join('') || emptyRow(6);
}

export function renderAll() {
    renderAccount();
    renderAdmin();
    renderStudent();
    renderInstructor();
}
