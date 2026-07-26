import {
    courseById,
    courses,
    enrollments,
    grades,
    instructors,
    nextId,
    profiles,
    scoreToLetter,
    state,
    studentById,
    students,
    users,
    userById,
} from './data';
import { renderAll, showPanel, showScreen } from './render';

const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => [...document.querySelectorAll(selector)];

const panelByRole = {
    admin: 'admin-dashboard',
    student: 'student-dashboard',
    instructor: 'instructor-dashboard',
};

const modalTitles = {
    student: { add: 'Add Student', edit: 'Edit Student' },
    course: { add: 'Add Course', edit: 'Edit Course' },
    enrollment: { add: 'Add Enrollment', edit: 'Edit Enrollment' },
    user: { add: 'Add User Account', edit: 'Edit User Account' },
    grade: { add: 'Assign Grade', edit: 'Update Grade' },
    profile: { add: 'Edit Profile', edit: 'Edit Profile' },
};

const selectOptions = (items, valueKey, labelKey) => items
    .map((item) => `<option value="${item[valueKey]}">${item[labelKey]}</option>`)
    .join('');

const studentOptions = () => students
    .map((student) => `<option value="${student.studentId}">${student.studentId} - ${userById(student.userId)?.fullName}</option>`)
    .join('');

const instructorOptions = () => instructors
    .map((instructor) => `<option value="${instructor.instructorId}">${userById(instructor.userId)?.fullName} - ${instructor.department}</option>`)
    .join('');

const courseOptions = () => courses
    .map((course) => `<option value="${course.courseId}">${course.courseName}</option>`)
    .join('');

const field = ({ label, name, type = 'text', value = '', options = '', required = true }) => {
    const input = type === 'select'
        ? `<select name="${name}" ${required ? 'required' : ''}>${options}</select>`
        : `<input type="${type}" name="${name}" value="${value ?? ''}" ${required ? 'required' : ''}>`;

    return `<label><span>${label}</span>${input}</label>`;
};

const setSelectValue = (name, value) => {
    const element = $(`[data-modal-fields] [name="${name}"]`);
    if (element) element.value = value;
};

function openModal(type, id = null, extra = {}) {
    const mode = id || type === 'profile' || type === 'grade' ? 'edit' : 'add';
    state.modal = { type, id, ...extra };
    $('[data-modal-title]').textContent = modalTitles[type][mode];
    $('[data-modal-fields]').innerHTML = modalFields(type, id, extra);
    $('[data-modal-backdrop]').classList.remove('is-hidden');
}

function closeModal() {
    state.modal = null;
    $('[data-modal-backdrop]').classList.add('is-hidden');
    $('[data-entity-form]').reset();
}

function modalFields(type, id, extra) {
    if (type === 'student') {
        const record = studentById(id) ?? {};
        const user = userById(record.userId) ?? {};
        return [
            field({ label: 'Full Name', name: 'fullName', value: user.fullName }),
            field({ label: 'Email', name: 'email', type: 'email', value: user.email }),
            field({ label: 'Phone Number', name: 'phone', value: record.phone }),
            field({ label: 'Address', name: 'address', value: record.address }),
            field({ label: 'Date of Birth', name: 'dob', type: 'date', value: record.dob }),
        ].join('');
    }

    if (type === 'course') {
        const record = courseById(id) ?? {};
        const fields = [
            field({ label: 'Course Name', name: 'courseName', value: record.courseName }),
            field({ label: 'Description', name: 'description', value: record.description }),
            field({ label: 'Instructor', name: 'instructorId', type: 'select', options: instructorOptions() }),
            field({ label: 'Duration', name: 'duration', type: 'number', value: record.duration ?? 12 }),
        ].join('');
        setTimeout(() => setSelectValue('instructorId', record.instructorId));
        return fields;
    }

    if (type === 'enrollment') {
        const record = enrollments.find((item) => item.enrollmentId === id) ?? {};
        const fields = [
            field({ label: 'Student', name: 'studentId', type: 'select', options: studentOptions() }),
            field({ label: 'Course', name: 'courseId', type: 'select', options: courseOptions() }),
            field({ label: 'Enrollment Date', name: 'enrollmentDate', type: 'date', value: record.enrollmentDate ?? new Date().toISOString().slice(0, 10) }),
            field({ label: 'Status', name: 'status', type: 'select', options: selectOptions([{ value: 'pending', label: 'Pending' }, { value: 'completed', label: 'Completed' }], 'value', 'label') }),
        ].join('');
        setTimeout(() => {
            setSelectValue('studentId', record.studentId);
            setSelectValue('courseId', record.courseId);
            setSelectValue('status', record.status ?? 'pending');
        });
        return fields;
    }

    if (type === 'user') {
        const record = userById(id) ?? {};
        const fields = [
            field({ label: 'Full Name', name: 'fullName', value: record.fullName }),
            field({ label: 'Email', name: 'email', type: 'email', value: record.email }),
            field({ label: 'Role', name: 'role', type: 'select', options: selectOptions([{ value: 'admin', label: 'Admin' }, { value: 'instructor', label: 'Instructor' }, { value: 'student', label: 'Student' }], 'value', 'label') }),
        ].join('');
        setTimeout(() => setSelectValue('role', record.role ?? 'student'));
        return fields;
    }

    if (type === 'grade') {
        const record = grades.find((item) => item.studentId === extra.studentId && item.courseId === extra.courseId) ?? {};
        return [
            field({ label: 'Student ID', name: 'studentId', value: extra.studentId, required: true }),
            field({ label: 'Course ID', name: 'courseId', value: extra.courseId, required: true }),
            field({ label: 'Score', name: 'score', type: 'number', value: record.score ?? 80 }),
        ].join('');
    }

    const profile = profiles[state.currentRole];
    return [
        field({ label: 'Full Name', name: 'name', value: profile.name }),
        field({ label: 'Email', name: 'email', type: 'email', value: profile.email }),
        field({ label: 'Phone Number', name: 'phone', value: profile.phone }),
        field({ label: 'Department', name: 'department', value: profile.department }),
        field({ label: 'Address', name: 'address', value: profile.address }),
    ].join('');
}

function saveModal(form) {
    const data = Object.fromEntries(new FormData(form).entries());
    const { type, id } = state.modal;

    if (type === 'student') saveStudent(id, data);
    if (type === 'course') saveCourse(id, data);
    if (type === 'enrollment') saveEnrollment(id, data);
    if (type === 'user') saveUser(id, data);
    if (type === 'grade') saveGrade(data);
    if (type === 'profile') Object.assign(profiles[state.currentRole], data);

    closeModal();
    renderAll();
}

function saveStudent(id, data) {
    const existing = studentById(id);
    if (existing) {
        const user = userById(existing.userId);
        Object.assign(existing, { phone: data.phone, address: data.address, dob: data.dob });
        Object.assign(user, { fullName: data.fullName, email: data.email });
        return;
    }

    const userId = Math.max(...users.map((user) => user.id)) + 1;
    users.push({ id: userId, fullName: data.fullName, email: data.email, role: 'student', createdAt: new Date().toISOString().slice(0, 10) });
    students.push({ studentId: nextId('S', students, 'studentId'), userId, phone: data.phone, address: data.address, dob: data.dob });
}

function saveCourse(id, data) {
    const payload = {
        instructorId: Number(data.instructorId),
        courseName: data.courseName,
        description: data.description,
        duration: Number(data.duration),
    };
    const existing = courseById(id);
    if (existing) Object.assign(existing, payload);
    else courses.push({ courseId: nextId('C', courses, 'courseId'), ...payload });
}

function saveEnrollment(id, data) {
    const payload = {
        studentId: data.studentId,
        courseId: data.courseId,
        enrollmentDate: data.enrollmentDate,
        status: data.status,
    };
    const existing = enrollments.find((item) => item.enrollmentId === id);
    if (existing) Object.assign(existing, payload);
    else enrollments.push({ enrollmentId: nextId('E', enrollments, 'enrollmentId'), ...payload });
}

function saveUser(id, data) {
    const existing = userById(id);
    if (existing) Object.assign(existing, { fullName: data.fullName, email: data.email, role: data.role });
    else users.push({ id: Math.max(...users.map((user) => user.id)) + 1, fullName: data.fullName, email: data.email, role: data.role, createdAt: new Date().toISOString().slice(0, 10) });
}

function saveGrade(data) {
    const score = Number(data.score);
    const payload = {
        studentId: data.studentId,
        courseId: data.courseId,
        score,
        grade: scoreToLetter(score),
        gradedBy: profiles.instructor.instructorId,
        gradedAt: new Date().toISOString().slice(0, 10),
    };
    const existing = grades.find((item) => item.studentId === data.studentId && item.courseId === data.courseId);
    if (existing) Object.assign(existing, payload);
    else grades.push({ gradeId: nextId('G', grades, 'gradeId'), ...payload });
}

function deleteRecord(type, id) {
    if (!window.confirm('Delete this sample record?')) return;
    if (type === 'student') {
        const index = students.findIndex((item) => item.studentId === id);
        if (index >= 0) students.splice(index, 1);
    }
    if (type === 'course') {
        const index = courses.findIndex((item) => item.courseId === id);
        if (index >= 0) courses.splice(index, 1);
    }
    if (type === 'enrollment') {
        const index = enrollments.findIndex((item) => item.enrollmentId === id);
        if (index >= 0) enrollments.splice(index, 1);
    }
    if (type === 'user') {
        const index = users.findIndex((item) => item.id === Number(id));
        if (index >= 0) users.splice(index, 1);
    }
    renderAll();
}

function enrollInCourse(courseId) {
    const studentId = profiles.student.studentId;
    if (enrollments.some((item) => item.studentId === studentId && item.courseId === courseId)) return;
    enrollments.push({
        enrollmentId: nextId('E', enrollments, 'enrollmentId'),
        studentId,
        courseId,
        enrollmentDate: new Date().toISOString().slice(0, 10),
        status: 'pending',
    });
    renderAll();
}

function signIn(role) {
    state.currentRole = role;
    renderAll();
    showScreen('app');
    showPanel(panelByRole[role]);
}

function roleFromEmail(email) {
    if (email.toLowerCase().includes('instructor')) return 'instructor';
    if (email.toLowerCase().includes('student')) return 'student';
    return 'admin';
}

export function initSisFrontend() {
    renderAll();
    showScreen('landing');
    showPanel('admin-dashboard');

    document.addEventListener('click', (event) => {
        const target = event.target.closest('button');
        if (!target) return;

        if (target.dataset.showScreen) showScreen(target.dataset.showScreen);
        if (target.dataset.view) showPanel(target.dataset.view);
        if (target.dataset.openModal) openModal(target.dataset.openModal);
        if (target.dataset.edit) openModal(target.dataset.edit, target.dataset.id);
        if (target.dataset.delete) deleteRecord(target.dataset.delete, target.dataset.id);
        if (target.dataset.enroll) enrollInCourse(target.dataset.enroll);
        if (target.dataset.gradeStudent) openModal('grade', null, { studentId: target.dataset.gradeStudent, courseId: target.dataset.gradeCourse });
        if (target.dataset.openProfileEditor !== undefined) openModal('profile');
        if (target.dataset.closeModal !== undefined) closeModal();
        if (target.dataset.signOut !== undefined) showScreen('landing');
        if (target.dataset.accountToggle !== undefined) $('[data-account-menu]').classList.toggle('is-open');
    });

    $('[data-signin-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        const form = new FormData(event.currentTarget);
        signIn(roleFromEmail(form.get('email') ?? ''));
    });

    $('[data-entity-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        saveModal(event.currentTarget);
    });

    $('#global-search').addEventListener('input', (event) => {
        state.globalSearch = event.target.value;
        renderAll();
    });

    $$('[data-table-search]').forEach((input) => {
        input.addEventListener('input', (event) => {
            state.tableSearches[event.target.dataset.tableSearch] = event.target.value;
            renderAll();
        });
    });
}
