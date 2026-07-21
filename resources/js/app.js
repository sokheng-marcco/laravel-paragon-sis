const users = [
    { id: 1, fullName: 'Student 1', email: 'student1@university.edu', role: 'student', createdAt: '2026-01-08' },
    { id: 2, fullName: 'Student 2', email: 'student2@university.edu', role: 'student', createdAt: '2026-01-10' },
    { id: 3, fullName: 'Student 3', email: 'student3@university.edu', role: 'student', createdAt: '2026-01-12' },
    { id: 4, fullName: 'Student 4', email: 'student4@university.edu', role: 'student', createdAt: '2026-01-14' },
    { id: 5, fullName: 'Student 5', email: 'student5@university.edu', role: 'student', createdAt: '2026-01-16' },
    { id: 6, fullName: 'Instructor 1', email: 'instructor1@university.edu', role: 'instructor', createdAt: '2025-12-20' },
    { id: 7, fullName: 'Instructor 2', email: 'instructor2@university.edu', role: 'instructor', createdAt: '2025-12-22' },
    { id: 8, fullName: 'Registrar Officer', email: 'registrar@university.edu', role: 'employee', createdAt: '2025-12-26' },
    { id: 9, fullName: 'System Admin', email: 'admin@university.edu', role: 'admin', createdAt: '2025-12-27' },
];

let students = [
    { studentId: 'S1000', userId: 1, phone: '555-0100', address: 'Phnom Penh', dob: '2000-07-15' },
    { studentId: 'S1001', userId: 2, phone: '555-0101', address: 'Phnom Penh', dob: '2000-05-15' },
    { studentId: 'S1002', userId: 3, phone: '555-0102', address: 'Phnom Penh', dob: '2003-07-15' },
    { studentId: 'S1003', userId: 4, phone: '555-0103', address: 'Phnom Penh', dob: '2003-02-15' },
    { studentId: 'S1004', userId: 5, phone: '555-0104', address: 'Phnom Penh', dob: '2000-02-15' },
];

const instructors = [
    { instructorId: 201, userId: 6, phone: '555-0201', department: 'Computer Science' },
    { instructorId: 202, userId: 7, phone: '555-0202', department: 'Information Systems' },
    { instructorId: 203, userId: 6, phone: '555-0203', department: 'Mathematics' },
    { instructorId: 204, userId: 7, phone: '555-0204', department: 'Business' },
    { instructorId: 205, userId: 6, phone: '555-0205', department: 'English' },
    { instructorId: 206, userId: 7, phone: '555-0206', department: 'Design' },
];

const employees = [
    { employeeId: 301, userId: 8, phone: '555-0301', position: 'Registrar Officer' },
];

const courses = [
    { courseId: 'C2000', instructorId: 201, courseName: 'Database Systems', description: 'Relational data design.', duration: 16 },
    { courseId: 'C2001', instructorId: 202, courseName: 'Student Information Systems', description: 'Academic records and workflows.', duration: 14 },
    { courseId: 'C2002', instructorId: 203, courseName: 'Web Application Development', description: 'Frontend and Laravel workflows.', duration: 15 },
    { courseId: 'C2003', instructorId: 204, courseName: 'Business Analytics', description: 'Dashboards and reporting.', duration: 12 },
    { courseId: 'C2004', instructorId: 205, courseName: 'Academic Writing', description: 'Research and writing practice.', duration: 10 },
    { courseId: 'C2005', instructorId: 206, courseName: 'UI Design Fundamentals', description: 'Product interface foundations.', duration: 11 },
    { courseId: 'C2006', instructorId: 201, courseName: 'Software Engineering', description: 'Project planning and delivery.', duration: 16 },
    { courseId: 'C2007', instructorId: 202, courseName: 'Systems Analysis', description: 'Requirements and process modeling.', duration: 13 },
    { courseId: 'C2008', instructorId: 203, courseName: 'Statistics', description: 'Probability and data analysis.', duration: 12 },
    { courseId: 'C2009', instructorId: 204, courseName: 'Management Information Systems', description: 'Technology for organizations.', duration: 14 },
    { courseId: 'C2010', instructorId: 205, courseName: 'Programming I', description: 'Programming fundamentals.', duration: 16 },
    { courseId: 'C2011', instructorId: 206, courseName: 'Capstone Project', description: 'Final project studio.', duration: 16 },
];

const enrollments = Array.from({ length: 63 }, (_, index) => ({
    enrollmentId: `E${3000 + index}`,
    studentId: students[index % students.length].studentId,
    courseId: courses[index % courses.length].courseId,
    employeeId: 301,
    enrollmentDate: `2026-02-${String((index % 24) + 1).padStart(2, '0')}`,
    status: index % 6 === 0 ? 'pending' : 'completed',
}));

const grades = Array.from({ length: 29 }, (_, index) => {
    const score = [82, 76, 91, 88, 72, 95, 69, 84, 79, 86][index % 10];
    return {
        gradeId: `G${4000 + index}`,
        studentId: students[index % students.length].studentId,
        courseId: courses[index % courses.length].courseId,
        score,
        grade: score >= 90 ? 'A' : score >= 80 ? 'B+' : score >= 70 ? 'B' : 'C',
        gradedBy: instructors[index % instructors.length].instructorId,
        gradedAt: `2026-05-${String((index % 24) + 1).padStart(2, '0')}`,
    };
});

let currentSearch = '';
let currentStudentSearch = '';
let currentRole = 'admin';

const roleProfiles = {
    admin: { name: 'System Admin', email: 'admin@university.edu', role: 'admin', initial: 'S', startView: 'dashboard' },
    student: { name: 'Student 1', email: 'student1@university.edu', role: 'student', initial: 'S', startView: 'student-portal' },
    instructor: { name: 'Instructor 1', email: 'instructor1@university.edu', role: 'instructor', initial: 'I', startView: 'instructor-portal' },
};

const byId = (id) => document.getElementById(id);
const userById = (id) => users.find((user) => user.id === id);
const studentById = (id) => students.find((student) => student.studentId === id);
const courseById = (id) => courses.find((course) => course.courseId === id);
const instructorById = (id) => instructors.find((instructor) => instructor.instructorId === id);
const employeeById = (id) => employees.find((employee) => employee.employeeId === id);

const formatDate = (date) => new Intl.DateTimeFormat('en-CA').format(new Date(date));

const getStudentName = (studentId) => userById(studentById(studentId)?.userId)?.fullName ?? 'Unknown student';
const getCourseName = (courseId) => courseById(courseId)?.courseName ?? 'Unknown course';
const getInstructorName = (instructorId) => userById(instructorById(instructorId)?.userId)?.fullName ?? 'Unknown instructor';
const getEmployeeName = (employeeId) => userById(employeeById(employeeId)?.userId)?.fullName ?? 'Unknown employee';

const averageScore = () => {
    const total = grades.reduce((sum, grade) => sum + grade.score, 0);
    return `${(total / grades.length).toFixed(1)}%`;
};

const renderMetrics = () => {
    document.querySelector('[data-metric="students"]').textContent = students.length;
    document.querySelector('[data-metric="instructors"]').textContent = instructors.length;
    document.querySelector('[data-metric="courses"]').textContent = courses.length;
    document.querySelector('[data-metric="enrollments"]').textContent = enrollments.length;
    document.querySelector('[data-metric="completed"]').textContent = enrollments.filter((item) => item.status === 'completed').length;
    document.querySelector('[data-metric="average-score"]').textContent = averageScore();
};

const renderRecentLists = () => {
    byId('recent-enrollments').innerHTML = enrollments.slice(0, 5).map((enrollment) => `
        <div class="mini-item">
            <div>
                <strong>${getStudentName(enrollment.studentId)}</strong>
                <span>${getCourseName(enrollment.courseId)}</span>
            </div>
            <span class="status-pill ${enrollment.status}">${enrollment.status}</span>
        </div>
    `).join('');

    byId('recent-grades').innerHTML = grades.slice(0, 5).map((grade) => `
        <div class="mini-item">
            <div>
                <strong>${getStudentName(grade.studentId)}</strong>
                <span>${getCourseName(grade.courseId)}</span>
            </div>
            <strong>${grade.grade}</strong>
        </div>
    `).join('');
};

const studentRows = () => students
    .map((student) => ({ ...student, user: userById(student.userId) }))
    .filter(({ studentId, user }) => {
        const globalMatch = `${studentId} ${user.fullName} ${user.email}`.toLowerCase().includes(currentSearch);
        const studentMatch = String(studentId).toLowerCase().includes(currentStudentSearch);
        return globalMatch && studentMatch;
    });

const renderStudents = () => {
    byId('student-table').innerHTML = studentRows().map((student) => `
        <tr>
            <td>${student.studentId}</td>
            <td>${student.user.fullName}</td>
            <td>${student.user.email}</td>
            <td>${student.phone}</td>
            <td>${formatDate(student.dob)}</td>
            <td>
                <span class="table-actions">
                    <button class="table-action" type="button" aria-label="Edit ${student.user.fullName}">
                        <svg viewBox="0 0 24 24"><path d="M4 20h4L18.5 9.5a2.1 2.1 0 0 0-3-3L5 17v3Z"></path></svg>
                    </button>
                    <button class="table-action delete" type="button" aria-label="Delete ${student.user.fullName}">
                        <svg viewBox="0 0 24 24"><path d="M5 7h14M10 11v6M14 11v6M8 7l1-3h6l1 3M7 7l1 13h8l1-13"></path></svg>
                    </button>
                </span>
            </td>
        </tr>
    `).join('');
};

const renderCourses = () => {
    byId('course-table').innerHTML = courses.map((course) => `
        <tr>
            <td>${course.courseId}</td>
            <td>${course.courseName}</td>
            <td>${getInstructorName(course.instructorId)}</td>
            <td>${course.duration} weeks</td>
            <td>${enrollments.filter((item) => item.courseId === course.courseId).length}</td>
        </tr>
    `).join('');
};

const renderEnrollments = () => {
    byId('enrollment-table').innerHTML = enrollments.slice(0, 18).map((enrollment) => `
        <tr>
            <td>${enrollment.enrollmentId}</td>
            <td>${getStudentName(enrollment.studentId)}</td>
            <td>${getCourseName(enrollment.courseId)}</td>
            <td>${getEmployeeName(enrollment.employeeId)}</td>
            <td>${formatDate(enrollment.enrollmentDate)}</td>
            <td><span class="status-pill ${enrollment.status}">${enrollment.status}</span></td>
        </tr>
    `).join('');
};

const renderGrades = () => {
    byId('grade-table').innerHTML = grades.slice(0, 18).map((grade) => `
        <tr>
            <td>${grade.gradeId}</td>
            <td>${getStudentName(grade.studentId)}</td>
            <td>${getCourseName(grade.courseId)}</td>
            <td>${grade.score.toFixed(1)}%</td>
            <td>${grade.grade}</td>
            <td>${getInstructorName(grade.gradedBy)}</td>
        </tr>
    `).join('');
};

const renderUsers = () => {
    byId('user-table').innerHTML = users.map((user) => `
        <tr>
            <td>${user.id}</td>
            <td>${user.fullName}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td>${formatDate(user.createdAt)}</td>
        </tr>
    `).join('');
};

const renderStudentPortal = () => {
    const studentId = 'S1000';
    const myEnrollments = enrollments.filter((item) => item.studentId === studentId).slice(0, 8);
    const myGrades = grades.filter((item) => item.studentId === studentId);
    const completed = myEnrollments.filter((item) => item.status === 'completed').length;
    const average = myGrades.reduce((sum, grade) => sum + grade.score, 0) / myGrades.length;

    document.querySelector('[data-student-metric="courses"]').textContent = myEnrollments.length;
    document.querySelector('[data-student-metric="completed"]').textContent = completed;
    document.querySelector('[data-student-metric="average"]').textContent = `${average.toFixed(1)}%`;

    byId('student-recent-grades').innerHTML = myGrades.slice(0, 5).map((grade) => `
        <div class="mini-item">
            <div>
                <strong>${getCourseName(grade.courseId)}</strong>
                <span>Graded on ${formatDate(grade.gradedAt)}</span>
            </div>
            <strong>${grade.grade}</strong>
        </div>
    `).join('');

    byId('student-browse-table').innerHTML = courses.map((course) => `
        <tr>
            <td>${course.courseName}</td>
            <td>${course.description}</td>
            <td>${getInstructorName(course.instructorId)}</td>
            <td>${course.duration} weeks</td>
        </tr>
    `).join('');

    byId('student-enrollment-table').innerHTML = myEnrollments.map((enrollment) => `
        <tr>
            <td>${getCourseName(enrollment.courseId)}</td>
            <td>${formatDate(enrollment.enrollmentDate)}</td>
            <td><span class="status-pill ${enrollment.status}">${enrollment.status}</span></td>
        </tr>
    `).join('');

    byId('student-grade-table').innerHTML = myGrades.map((grade) => `
        <tr>
            <td>${getCourseName(grade.courseId)}</td>
            <td>${grade.score.toFixed(1)}%</td>
            <td>${grade.grade}</td>
            <td>${getInstructorName(grade.gradedBy)}</td>
            <td>${formatDate(grade.gradedAt)}</td>
        </tr>
    `).join('');
};

const renderInstructorPortal = () => {
    const instructorId = 201;
    const assignedCourses = courses.filter((course) => course.instructorId === instructorId);
    const assignedCourseIds = assignedCourses.map((course) => course.courseId);
    const assignedEnrollments = enrollments.filter((item) => assignedCourseIds.includes(item.courseId));
    const studentCount = new Set(assignedEnrollments.map((item) => item.studentId)).size;
    const postedGrades = grades.filter((grade) => grade.gradedBy === instructorId).length;

    document.querySelector('[data-instructor-metric="courses"]').textContent = assignedCourses.length;
    document.querySelector('[data-instructor-metric="students"]').textContent = studentCount;
    document.querySelector('[data-instructor-metric="grades"]').textContent = postedGrades;

    byId('instructor-recent-courses').innerHTML = assignedCourses.map((course) => `
        <div class="mini-item">
            <div>
                <strong>${course.courseName}</strong>
                <span>${course.description}</span>
            </div>
            <span>${enrollments.filter((item) => item.courseId === course.courseId).length} students</span>
        </div>
    `).join('');

    byId('instructor-course-table').innerHTML = assignedCourses.map((course) => `
        <tr>
            <td>${course.courseId}</td>
            <td>${course.courseName}</td>
            <td>${course.description}</td>
            <td>${course.duration} weeks</td>
            <td>${enrollments.filter((item) => item.courseId === course.courseId).length}</td>
        </tr>
    `).join('');

    byId('instructor-grade-table').innerHTML = grades
        .filter((grade) => assignedCourseIds.includes(grade.courseId))
        .slice(0, 14)
        .map((grade, index) => `
            <tr>
                <td>${getStudentName(grade.studentId)}</td>
                <td>${getCourseName(grade.courseId)}</td>
                <td>${grade.score ? grade.score.toFixed(1) : 'Not graded'}</td>
                <td>${grade.grade}</td>
                <td>${grade.score ? 'Completed' : 'Pending'}</td>
                <td>
                    <button class="primary-action grade-action" type="button">${index % 3 === 0 ? 'Update' : 'Assign'}</button>
                </td>
            </tr>
        `).join('');
};

const renderRoleProfile = () => {
    const profile = roleProfiles[currentRole];
    document.querySelector('[data-current-name]').textContent = profile.name;
    document.querySelector('[data-current-role]').textContent = profile.role;
    document.querySelector('.admin-avatar').textContent = profile.initial;
    document.querySelector('[data-profile-initial]').textContent = profile.initial;
    document.querySelector('[data-profile-name]').textContent = profile.name;
    document.querySelector('[data-profile-email]').textContent = profile.email;
    document.querySelector('[data-profile-role]').textContent = `Role: ${profile.role}`;
    document.querySelector('[data-role-label]').textContent = profile.role;
};

const renderAll = () => {
    renderMetrics();
    renderRecentLists();
    renderStudents();
    renderCourses();
    renderEnrollments();
    renderGrades();
    renderUsers();
    renderStudentPortal();
    renderInstructorPortal();
    renderRoleProfile();
};

const switchView = (view) => {
    document.querySelectorAll('[data-panel]').forEach((panel) => {
        panel.classList.toggle('is-active', panel.dataset.panel === view);
    });
    document.querySelectorAll('[data-view]').forEach((button) => {
        button.classList.toggle('is-active', button.dataset.view === view);
    });
    document.querySelector('.sidebar').classList.remove('is-open');
};

const applyRole = (role) => {
    currentRole = role;
    document.querySelectorAll('[data-roles]').forEach((item) => {
        item.hidden = !item.dataset.roles.split(' ').includes(role);
    });
    renderRoleProfile();
    switchView(roleProfiles[role].startView);
};

const openModal = () => {
    const modal = byId('student-modal');
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('input')?.focus();
};

const closeModal = () => {
    const modal = byId('student-modal');
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    byId('student-form').reset();
};

document.querySelectorAll('[data-view]').forEach((button) => {
    button.addEventListener('click', () => switchView(button.dataset.view));
});

byId('global-search').addEventListener('input', (event) => {
    currentSearch = event.target.value.trim().toLowerCase();
    renderStudents();
});

byId('student-search').addEventListener('input', (event) => {
    currentStudentSearch = event.target.value.trim().toLowerCase();
    renderStudents();
});

document.querySelector('[data-mobile-menu]').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('is-open');
});

byId('role-preview').addEventListener('change', (event) => {
    applyRole(event.target.value);
});

document.querySelectorAll('[data-open-student-form]').forEach((button) => {
    button.addEventListener('click', openModal);
});

document.querySelectorAll('[data-close-student-form]').forEach((button) => {
    button.addEventListener('click', closeModal);
});

byId('student-modal').addEventListener('click', (event) => {
    if (event.target.id === 'student-modal') {
        closeModal();
    }
});

byId('student-form').addEventListener('submit', (event) => {
    event.preventDefault();

    const form = new FormData(event.currentTarget);
    const nextUserId = Math.max(...users.map((user) => user.id)) + 1;
    const nextStudentNumber = Math.max(...students.map((student) => Number(String(student.studentId).replace('S', '')))) + 1;
    const fullName = form.get('fullName');
    const email = form.get('email');

    users.push({
        id: nextUserId,
        fullName,
        email,
        role: 'student',
        createdAt: new Date().toISOString().slice(0, 10),
    });

    students = [
        {
            studentId: `S${nextStudentNumber}`,
            userId: nextUserId,
            phone: form.get('phone') || '555-0000',
            address: form.get('address') || 'Phnom Penh',
            dob: form.get('dob') || '2001-01-01',
        },
        ...students,
    ];

    closeModal();
    renderAll();
    switchView('students');
});

renderAll();
applyRole(currentRole);
