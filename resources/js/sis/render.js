import {
    courseName,
    courses,
    enrollments,
    formatDate,
    grades,
    instructorName,
    instructors,
    matchesSearch,
    pagination,
    profiles,
    state,
    studentName,
    students,
    userById,
    users,
} from './data';

const $ = (selector) => document.querySelector(selector);
const tableBody = (id) => document.getElementById(id);
const setText = (selector, value) => {
    const element = $(selector);
    if (element) element.textContent = value;
};
const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
}[character]));

const actionButtons = (entity, id) => `
    <span class="table-actions">
        <a class="table-action" href="/admin/${entityRouteSegment(entity)}/${id}/edit${window.location.search}" aria-label="Edit">
            <svg viewBox="0 0 24 24"><path d="M4 20h4L18.5 9.5a2.1 2.1 0 0 0-3-3L5 17v3Z"></path></svg>
        </a>
        <a class="table-action delete" href="/admin/${entityRouteSegment(entity)}/${id}/delete${window.location.search}" aria-label="Delete">
            <svg viewBox="0 0 24 24"><path d="M5 7h14M10 11v6M14 11v6M8 7l1-3h6l1 3M7 7l1 13h8l1-13"></path></svg>
        </a>
    </span>
`;

function entityRouteSegment(entity) {
    return entity === 'user' ? 'users' : `${entity}s`;
}

const emptyRow = (columns) => `<tr><td colspan="${columns}" class="empty-state">No records found.</td></tr>`;
const emptyList = '<p class="empty-state">No records found.</p>';
const queryFor = (key) => `${state.globalSearch} ${state.tableSearches[key] ?? ''}`.trim();
const average = (items) => items.length
    ? items.reduce((sum, item) => sum + Number(item.score), 0) / items.length
    : 0;
const rowNumber = (key, index) => {
    const currentPage = pagination[key]?.currentPage ?? 1;
    const perPage = pagination[key]?.perPage ?? 10;
    return ((currentPage - 1) * perPage) + index + 1;
};

export function renderAccount() {
    const profile = profiles[state.currentRole];
    const displayName = profile.name || 'User';
    const initial = displayName.charAt(0).toUpperCase();

    setText('[data-role-label]', state.currentRole === 'admin' ? 'administrator' : state.currentRole);
    setText('[data-current-name]', displayName);
    setText('[data-current-role]', profile.role);
    setText('.admin-avatar', initial);
    setText('[data-profile-initial]', initial);
    setText('[data-profile-name]', displayName);
    setText('[data-profile-email]', profile.email || '—');
    setText('[data-profile-role]', profile.role);
    setText('[data-profile-phone]', profile.phone || '—');
    setText('[data-profile-department]', profile.department || '—');
    setText('[data-profile-address]', profile.address || '—');

    const idByRole = {
        admin: profile.employeeId,
        instructor: profile.instructorId,
        student: profile.studentId,
    };
    const idLabelByRole = {
        admin: 'Employee ID',
        instructor: 'Instructor ID',
        student: 'Student ID',
    };
    const secondaryLabelByRole = {
        admin: 'Position',
        instructor: 'Department',
        student: 'Date of Birth',
    };
    const secondaryValueByRole = {
        admin: profile.position,
        instructor: profile.department,
        student: formatDate(profile.dateOfBirth),
    };

    setText('[data-profile-id-label]', idLabelByRole[state.currentRole]);
    setText('[data-profile-id]', idByRole[state.currentRole] || '—');
    setText('[data-profile-secondary-label]', secondaryLabelByRole[state.currentRole]);
    setText('[data-profile-secondary]', secondaryValueByRole[state.currentRole] || '—');

    const addressRow = $('[data-profile-address-row]');
    if (addressRow) addressRow.hidden = state.currentRole !== 'student';
}

export function renderAdmin() {
    $('[data-metric="students"]').textContent = pagination.students?.total ?? students.length;
    $('[data-metric="instructors"]').textContent = instructors.length;
    $('[data-metric="courses"]').textContent = pagination.courses?.total ?? courses.length;
    $('[data-metric="enrollments"]').textContent = pagination.enrollments?.total ?? enrollments.length;
    $('[data-metric="completed"]').textContent = enrollments.filter((item) => item.status === 'completed').length;
    $('[data-metric="average-score"]').textContent = `${average(grades).toFixed(1)}%`;

    tableBody('recent-enrollments').innerHTML = enrollments.slice(0, 5).map((item) => `
        <div class="mini-item"><div><strong>${escapeHtml(studentName(item.studentId))}</strong><span>${escapeHtml(courseName(item.courseId))}</span></div><span class="status-pill ${escapeHtml(item.status)}">${escapeHtml(item.status)}</span></div>
    `).join('') || emptyList;

    tableBody('recent-grades').innerHTML = grades.slice(0, 5).map((item) => `
        <div class="mini-item"><div><strong>${escapeHtml(studentName(item.studentId))}</strong><span>${escapeHtml(courseName(item.courseId))}</span></div><strong>${escapeHtml(item.grade)}</strong></div>
    `).join('') || emptyList;

    const studentRows = students
        .map((student) => ({ ...student, user: userById(student.userId) }))
        .filter((student) => matchesSearch([student.studentId, student.user?.fullName, student.user?.email, student.phone], queryFor('students')));
    tableBody('student-table').innerHTML = studentRows.map((student, index) => `
        <tr><td>${rowNumber('students', index)}</td><td>${escapeHtml(student.user?.fullName ?? 'Unknown')}</td><td>${escapeHtml(student.user?.email ?? '—')}</td><td>${escapeHtml(student.phone || '—')}</td><td>${formatDate(student.dob)}</td><td>${actionButtons('student', student.studentId)}</td></tr>
    `).join('') || emptyRow(6);

    const courseRows = courses.filter((course) => matchesSearch(
        [course.courseId, course.courseName, course.description, instructorName(course.instructorId)],
        queryFor('courses'),
    ));
    tableBody('course-table').innerHTML = courseRows.map((course, index) => `
        <tr><td>${rowNumber('courses', index)}</td><td>${escapeHtml(course.courseName)}</td><td>${escapeHtml(course.description || '—')}</td><td>${escapeHtml(instructorName(course.instructorId))}</td><td>${course.duration} weeks</td><td>${actionButtons('course', course.courseId)}</td></tr>
    `).join('') || emptyRow(6);

    const enrollmentRows = enrollments.filter((item) => matchesSearch(
        [item.enrollmentId, studentName(item.studentId), courseName(item.courseId), item.status],
        queryFor('enrollments'),
    ));
    tableBody('enrollment-table').innerHTML = enrollmentRows.map((item, index) => `
        <tr><td>${rowNumber('enrollments', index)}</td><td>${escapeHtml(studentName(item.studentId))}</td><td>${escapeHtml(courseName(item.courseId))}</td><td>${formatDate(item.enrollmentDate)}</td><td><span class="status-pill ${escapeHtml(item.status)}">${escapeHtml(item.status)}</span></td><td>${actionButtons('enrollment', item.enrollmentId)}</td></tr>
    `).join('') || emptyRow(6);

    const gradeRows = grades.filter((item) => matchesSearch(
        [item.gradeId, studentName(item.studentId), courseName(item.courseId), item.grade],
        queryFor('grades'),
    ));
    tableBody('grade-table').innerHTML = gradeRows.map((item, index) => `
        <tr><td>${rowNumber('grades', index)}</td><td>${escapeHtml(studentName(item.studentId))}</td><td>${escapeHtml(courseName(item.courseId))}</td><td>${Number(item.score).toFixed(1)}%</td><td>${escapeHtml(item.grade)}</td><td>${escapeHtml(instructorName(item.gradedBy))}</td><td>${actionButtons('grade', item.gradeId)}</td></tr>
    `).join('') || emptyRow(7);

    const userRows = users.filter((user) => matchesSearch(
        [user.id, user.fullName, user.email, user.role],
        queryFor('users'),
    ));
    tableBody('user-table').innerHTML = userRows.map((user, index) => `
        <tr><td>${rowNumber('users', index)}</td><td>${escapeHtml(user.fullName)}</td><td>${escapeHtml(user.email)}</td><td>${escapeHtml(user.role === 'employee' ? 'administrator' : user.role)}</td><td>${formatDate(user.createdAt)}</td><td>${actionButtons('user', user.id)}</td></tr>
    `).join('') || emptyRow(6);
}

export function renderStudent() {
    const studentId = profiles.student.studentId;
    const myEnrollments = enrollments.filter((item) => item.studentId === studentId);
    const myGrades = grades.filter((item) => item.studentId === studentId);

    $('[data-student-metric="courses"]').textContent = myEnrollments.length;
    $('[data-student-metric="completed"]').textContent = myEnrollments.filter((item) => item.status === 'completed').length;
    $('[data-student-metric="average"]').textContent = `${average(myGrades).toFixed(1)}%`;

    tableBody('student-recent-grades').innerHTML = myGrades.slice(0, 5).map((grade) => `
        <div class="mini-item"><div><strong>${escapeHtml(courseName(grade.courseId))}</strong><span>Graded on ${formatDate(grade.gradedAt)}</span></div><strong>${escapeHtml(grade.grade)}</strong></div>
    `).join('') || emptyList;

    const browseRows = courses.filter((course) => matchesSearch(
        [course.courseName, course.description, instructorName(course.instructorId)],
        queryFor('studentBrowse'),
    ));
    tableBody('student-browse-table').innerHTML = browseRows.map((course) => {
        const enrolled = enrollments.some((item) => item.studentId === studentId && item.courseId === course.courseId);
        return `<tr><td>${escapeHtml(course.courseName)}</td><td>${escapeHtml(course.description || '—')}</td><td>${escapeHtml(instructorName(course.instructorId))}</td><td>${course.duration} weeks</td><td><button class="${enrolled ? 'secondary-action' : 'primary-action'} compact" type="button" data-enroll="${course.courseId}" ${enrolled ? 'disabled' : ''}>${enrolled ? 'Enrolled' : 'Enroll'}</button></td></tr>`;
    }).join('') || emptyRow(5);

    tableBody('student-enrollment-table').innerHTML = myEnrollments.map((item) => `
        <tr><td>${escapeHtml(courseName(item.courseId))}</td><td>${formatDate(item.enrollmentDate)}</td><td><span class="status-pill ${escapeHtml(item.status)}">${escapeHtml(item.status)}</span></td></tr>
    `).join('') || emptyRow(3);

    tableBody('student-grade-table').innerHTML = myGrades.map((item) => `
        <tr><td>${escapeHtml(courseName(item.courseId))}</td><td>${Number(item.score).toFixed(1)}%</td><td>${escapeHtml(item.grade)}</td><td>${escapeHtml(instructorName(item.gradedBy))}</td><td>${formatDate(item.gradedAt)}</td></tr>
    `).join('') || emptyRow(5);
}

export function renderInstructor() {
    const instructorId = profiles.instructor.instructorId;
    const myCourses = courses.filter((course) => course.instructorId === instructorId);
    const courseIds = myCourses.map((course) => course.courseId);
    const myEnrollments = enrollments.filter((item) => courseIds.includes(item.courseId));
    const gradedPairs = new Set(grades.map((grade) => `${grade.studentId}:${grade.courseId}`));

    $('[data-instructor-metric="courses"]').textContent = myCourses.length;
    $('[data-instructor-metric="students"]').textContent = new Set(myEnrollments.map((item) => item.studentId)).size;
    $('[data-instructor-metric="grades"]').textContent = myEnrollments.filter(
        (item) => !gradedPairs.has(`${item.studentId}:${item.courseId}`),
    ).length;

    tableBody('instructor-recent-courses').innerHTML = myCourses.map((course) => `
        <div class="mini-item"><div><strong>${escapeHtml(course.courseName)}</strong><span>${escapeHtml(course.description || '—')}</span></div><strong>${course.duration}w</strong></div>
    `).join('') || emptyList;

    const courseRows = myCourses.filter((course) => matchesSearch(
        [course.courseName, course.description],
        queryFor('instructorCourses'),
    ));
    tableBody('instructor-course-table').innerHTML = courseRows.map((course, index) => `
        <tr><td>${rowNumber('courses', index)}</td><td>${escapeHtml(course.courseName)}</td><td>${escapeHtml(course.description || '—')}</td><td>${course.duration} weeks</td><td>${course.enrollmentsCount}</td></tr>
    `).join('') || emptyRow(5);

    const gradeRows = myEnrollments
        .map((item) => ({
            enrollment: item,
            grade: grades.find((grade) => grade.studentId === item.studentId && grade.courseId === item.courseId),
        }))
        .filter(({ enrollment, grade }) => matchesSearch(
            [studentName(enrollment.studentId), courseName(enrollment.courseId), grade?.grade ?? 'pending'],
            queryFor('instructorGrades'),
        ));

    tableBody('instructor-grade-table').innerHTML = gradeRows.map(({ enrollment, grade }) => `
        <tr>
            <td>${escapeHtml(studentName(enrollment.studentId))}</td>
            <td>${escapeHtml(courseName(enrollment.courseId))}</td>
            <td>${grade ? `${Number(grade.score).toFixed(1)}%` : '-'}</td>
            <td>${escapeHtml(grade?.grade ?? '-')}</td>
            <td><span class="status-pill ${grade ? 'completed' : 'pending'}">${grade ? 'graded' : 'pending'}</span></td>
            <td><a class="primary-action compact" href="/instructor/grades/${enrollment.studentId}/${enrollment.courseId}/edit${window.location.search}">${grade ? 'Update' : 'Assign'}</a></td>
        </tr>
    `).join('') || emptyRow(6);
}

function paginationUrl(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    return `${url.pathname}${url.search}`;
}

export function renderPagination() {
    const container = $('[data-pagination]');
    const meta = pagination[state.activePaginationKey];
    if (!container || !meta) {
        container?.classList.add('is-hidden');
        return;
    }

    const previous = meta.currentPage > 1
        ? `<a class="secondary-action" href="${paginationUrl(meta.currentPage - 1)}">Previous</a>`
        : '<span class="secondary-action is-disabled">Previous</span>';
    const next = meta.currentPage < meta.lastPage
        ? `<a class="secondary-action" href="${paginationUrl(meta.currentPage + 1)}">Next</a>`
        : '<span class="secondary-action is-disabled">Next</span>';

    container.innerHTML = `
        <span>Showing ${meta.from ?? 0}–${meta.to ?? 0} of ${meta.total}</span>
        <div>${previous}<strong>Page ${meta.currentPage} of ${meta.lastPage}</strong>${next}</div>
    `;
    container.classList.remove('is-hidden');
}

export function renderAll() {
    if ($('#sis-app')) renderAccount();
    if ($('[data-metric="students"]')) renderAdmin();
    if ($('[data-student-metric="courses"]')) renderStudent();
    if ($('[data-instructor-metric="courses"]')) renderInstructor();
    renderPagination();
}
