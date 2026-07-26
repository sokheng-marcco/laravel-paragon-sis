import { api, clearToken, getToken, saveToken } from './api';
import {
    clearAcademicData,
    courseById,
    courses,
    enrollments,
    grades,
    instructors,
    loadCourses,
    loadEnrollments,
    loadGrades,
    loadStudents,
    loadUsers,
    profiles,
    replacePageRecords,
    setPagination,
    setCurrentProfile,
    state,
    studentById,
    students,
    users,
    userById,
} from './data';
import { renderAll } from './render';

const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => [...document.querySelectorAll(selector)];

const dashboardByRole = {
    admin: '/admin',
    student: '/student',
    instructor: '/instructor',
};

const roleProfileFor = (user) => {
    if (user.role === 'student') return user.student;
    if (user.role === 'instructor') return user.instructor;
    return user.employee;
};

const paginationKeyByPage = {
    'admin-students': 'students',
    'admin-courses': 'courses',
    'admin-enrollments': 'enrollments',
    'admin-grades': 'grades',
    'admin-users': 'users',
    'student-browse': 'courses',
    'student-enrollments': 'enrollments',
    'student-grades': 'grades',
    'instructor-courses': 'courses',
    'instructor-grades': 'enrollments',
};

const recordsFrom = (response) => response.data ?? response;

const modalTitles = {
    student: { add: 'Add Student', edit: 'Edit Student' },
    course: { add: 'Add Course', edit: 'Edit Course' },
    enrollment: { add: 'Add Enrollment', edit: 'Edit Enrollment' },
    user: { add: 'Add User Account', edit: 'Edit User Account' },
    grade: { add: 'Assign Grade', edit: 'Update Grade' },
    profile: { add: 'Edit Profile', edit: 'Edit Profile' },
};

const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
}[character]));

const selectOptions = (items, valueKey, labelKey) => items
    .map((item) => `<option value="${escapeHtml(item[valueKey])}">${escapeHtml(item[labelKey])}</option>`)
    .join('');

const studentOptions = () => students
    .map((student) => ({
        value: student.studentId,
        label: `${student.studentId} - ${userById(student.userId)?.fullName ?? 'Unknown'}`,
    }))
    .map((item) => `<option value="${item.value}">${escapeHtml(item.label)}</option>`)
    .join('');

const instructorOptions = () => instructors
    .map((instructor) => ({
        value: instructor.instructorId,
        label: `${userById(instructor.userId)?.fullName ?? 'Unknown'} - ${instructor.department || 'Instructor'}`,
    }))
    .map((item) => `<option value="${item.value}">${escapeHtml(item.label)}</option>`)
    .join('');

const courseOptions = () => courses
    .map((course) => `<option value="${course.courseId}">${escapeHtml(course.courseName)}</option>`)
    .join('');

const field = ({
    label,
    name,
    type = 'text',
    value = '',
    options = '',
    required = true,
    readonly = false,
}) => {
    const attributes = [
        `name="${name}"`,
        required ? 'required' : '',
        readonly ? 'readonly' : '',
    ].filter(Boolean).join(' ');
    const input = type === 'select'
        ? `<select ${attributes}>${options}</select>`
        : `<input type="${type}" value="${escapeHtml(value)}" ${attributes}>`;

    return `<label><span>${escapeHtml(label)}</span>${input}</label>`;
};

const setSelectValue = (name, value) => {
    const element = $(`[data-modal-fields] [name="${name}"]`);
    if (element && value !== undefined && value !== null) element.value = String(value);
};

const setFormMessage = (selector, message = '') => {
    const element = $(selector);
    if (!element) return;
    element.textContent = message;
    element.classList.toggle('is-hidden', !message);
};

const setSubmitting = (form, submitting) => {
    const button = form.querySelector('[type="submit"]');
    if (!button) return;
    button.dataset.defaultLabel ??= button.textContent;
    button.disabled = submitting;
    button.textContent = submitting ? 'Please wait…' : button.dataset.defaultLabel;
};

function openModal(type, id = null, extra = {}) {
    const hasGrade = type === 'grade' && grades.some(
        (grade) => grade.studentId === Number(extra.studentId)
            && grade.courseId === Number(extra.courseId),
    );
    const mode = id !== null || type === 'profile' || hasGrade ? 'edit' : 'add';
    state.modal = { type, id: id === null ? null : Number(id), ...extra };
    $('[data-modal-title]').textContent = modalTitles[type][mode];
    $('[data-modal-fields]').innerHTML = modalFields(type, state.modal.id, extra);
    setFormMessage('[data-modal-error]');
    $('[data-modal-backdrop]').classList.remove('is-hidden');
}

function closeModal() {
    const returnUrl = document.body.dataset.modalReturnUrl;
    window.location.assign(returnUrl || dashboardByRole[state.currentRole]);
}

function openDeleteModal(type, id) {
    const labels = {
        student: ['Delete Student', 'delete this student'],
        course: ['Delete Course', 'delete this course'],
        enrollment: ['Remove Enrollment', 'remove this enrollment'],
        grade: ['Delete Grade', 'delete this grade'],
        user: ['Delete User', 'delete this user account'],
    };
    const [title, action] = labels[type] ?? ['Delete Record', 'delete this record'];

    state.modal = { type, id: Number(id), mode: 'delete' };
    $('[data-delete-title]').textContent = title;
    $('[data-delete-message]').textContent = `Are you sure you want to ${action}? This action cannot be undone.`;
    setFormMessage('[data-delete-error]');
    $('[data-delete-backdrop]').classList.remove('is-hidden');
}

function modalFields(type, id, extra) {
    if (type === 'student') {
        const record = studentById(id);
        if (!record) {
            return [
                field({ label: 'Full Name', name: 'fullName' }),
                field({ label: 'Email', name: 'email', type: 'email' }),
                field({ label: 'Phone Number', name: 'phone', required: false }),
                field({ label: 'Address', name: 'address', required: false }),
                field({ label: 'Date of Birth', name: 'dob', type: 'date', required: false }),
                '<p class="form-hint">Default password: <strong>11112222</strong></p>',
            ].join('');
        }

        const user = userById(record.userId) ?? {};
        return [
            field({ label: 'Full Name', name: 'fullName', value: user.fullName }),
            field({ label: 'Email', name: 'email', type: 'email', value: user.email }),
            field({ label: 'Phone Number', name: 'phone', value: record.phone, required: false }),
            field({ label: 'Address', name: 'address', value: record.address, required: false }),
            field({ label: 'Date of Birth', name: 'dob', type: 'date', value: record.dob, required: false }),
        ].join('');
    }

    if (type === 'course') {
        const record = courseById(id) ?? {};
        const fields = [
            field({ label: 'Course Name', name: 'courseName', value: record.courseName }),
            field({ label: 'Description', name: 'description', value: record.description, required: false }),
            field({ label: 'Instructor', name: 'instructorId', type: 'select', options: instructorOptions() }),
            field({ label: 'Duration (weeks)', name: 'duration', type: 'number', value: record.duration ?? 12 }),
        ].join('');
        setTimeout(() => setSelectValue('instructorId', record.instructorId));
        return fields;
    }

    if (type === 'enrollment') {
        const record = enrollments.find((item) => item.enrollmentId === Number(id)) ?? {};
        const fields = [
            field({ label: 'Student', name: 'studentId', type: 'select', options: studentOptions() }),
            field({ label: 'Course', name: 'courseId', type: 'select', options: courseOptions() }),
            field({ label: 'Enrollment Date', name: 'enrollmentDate', type: 'date', value: record.enrollmentDate ?? new Date().toISOString().slice(0, 10), required: false }),
            field({
                label: 'Status',
                name: 'status',
                type: 'select',
                options: selectOptions([
                    { value: 'enrolled', label: 'Enrolled' },
                    { value: 'completed', label: 'Completed' },
                    { value: 'dropped', label: 'Dropped' },
                ], 'value', 'label'),
            }),
        ].join('');
        setTimeout(() => {
            setSelectValue('studentId', record.studentId);
            setSelectValue('courseId', record.courseId);
            setSelectValue('status', record.status ?? 'enrolled');
        });
        return fields;
    }

    if (type === 'user') {
        const record = userById(id) ?? {};
        const fields = [
            field({ label: 'Full Name', name: 'fullName', value: record.fullName }),
            field({ label: 'Email', name: 'email', type: 'email', value: record.email }),
            field({
                label: 'Role',
                name: 'role',
                type: 'select',
                options: selectOptions([
                    { value: 'employee', label: 'Administrator' },
                    { value: 'instructor', label: 'Instructor' },
                    { value: 'student', label: 'Student' },
                ], 'value', 'label'),
            }),
        ].join('');
        setTimeout(() => setSelectValue('role', record.role ?? 'student'));
        return fields + (id ? '' : '<p class="form-hint">Default password: <strong>11112222</strong></p>');
    }

    if (type === 'grade') {
        if (state.currentRole === 'admin') {
            const record = grades.find((item) => item.gradeId === Number(id)) ?? {};
            const fields = [
                field({ label: 'Student', name: 'studentId', type: 'select', options: studentOptions() }),
                field({ label: 'Course', name: 'courseId', type: 'select', options: courseOptions() }),
                field({ label: 'Score', name: 'score', type: 'number', value: record.score ?? 80 }),
            ].join('');
            setTimeout(() => {
                setSelectValue('studentId', record.studentId);
                setSelectValue('courseId', record.courseId);
            });
            return fields;
        }

        const studentId = Number(extra.studentId);
        const courseId = Number(extra.courseId);
        const record = grades.find((item) => item.studentId === studentId && item.courseId === courseId) ?? {};
        return [
            field({ label: 'Student ID', name: 'studentId', value: studentId, readonly: true }),
            field({ label: 'Course ID', name: 'courseId', value: courseId, readonly: true }),
            field({ label: 'Score', name: 'score', type: 'number', value: record.score ?? 80 }),
        ].join('');
    }

    const profile = profiles[state.currentRole];
    const commonFields = [
        field({ label: 'Full Name', name: 'name', value: profile.name }),
        field({ label: 'Email', name: 'email', type: 'email', value: profile.email }),
    ];

    if (state.currentRole === 'student') {
        commonFields.push(
            field({ label: 'Phone Number', name: 'phone', value: profile.phone, required: false }),
            field({ label: 'Date of Birth', name: 'dateOfBirth', type: 'date', value: profile.dateOfBirth, required: false }),
            field({ label: 'Address', name: 'address', value: profile.address, required: false }),
        );
    } else if (state.currentRole === 'instructor') {
        commonFields.push(
            field({ label: 'Phone Number', name: 'phone', value: profile.phone, required: false }),
            field({ label: 'Department', name: 'department', value: profile.department, required: false }),
        );
    } else {
        commonFields.push(
            field({ label: 'Phone Number', name: 'phone', value: profile.phone, required: false }),
            field({ label: 'Position', name: 'position', value: profile.position, required: false }),
        );
    }

    return commonFields.join('');
}

async function loadApiData() {
    const user = await api.me();
    clearAcademicData();
    setCurrentProfile(user, roleProfileFor(user));
    const activeKey = paginationKeyByPage[document.body.dataset.page] ?? null;
    const requestedPage = Math.max(
        1,
        Number(new URLSearchParams(window.location.search).get('page')) || 1,
    );
    const pageFor = (key) => activeKey === key ? requestedPage : 1;
    const loadPage = (key, response, loader) => {
        loader(recordsFrom(response));
        setPagination(key, response);
    };
    const responses = {};
    state.activePaginationKey = activeKey;

    if (user.role === 'employee') {
        const [userRecords, studentRecords, courseRecords, enrollmentRecords, gradeRecords] = await Promise.all([
            api.users(pageFor('users')),
            api.students(pageFor('students')),
            api.courses(pageFor('courses')),
            api.enrollments(pageFor('enrollments')),
            api.grades(pageFor('grades')),
        ]);
        Object.assign(responses, {
            users: userRecords,
            students: studentRecords,
            courses: courseRecords,
            enrollments: enrollmentRecords,
            grades: gradeRecords,
        });
        loadPage('users', userRecords, loadUsers);
        loadPage('students', studentRecords, loadStudents);
        loadPage('courses', courseRecords, loadCourses);
        loadPage('enrollments', enrollmentRecords, loadEnrollments);
        loadPage('grades', gradeRecords, loadGrades);
    } else if (user.role === 'student') {
        const [studentProfile, courseRecords, enrollmentRecords, gradeRecords] = await Promise.all([
            api.studentMe(),
            api.courses(pageFor('courses')),
            api.enrollments(pageFor('enrollments')),
            api.grades(pageFor('grades')),
        ]);
        Object.assign(responses, {
            courses: courseRecords,
            enrollments: enrollmentRecords,
            grades: gradeRecords,
        });
        loadStudents([studentProfile]);
        loadPage('courses', courseRecords, loadCourses);
        loadPage('enrollments', enrollmentRecords, loadEnrollments);
        loadPage('grades', gradeRecords, loadGrades);
        setCurrentProfile(user, studentProfile);
    } else {
        const [courseRecords, enrollmentRecords, gradeRecords] = await Promise.all([
            api.courses(pageFor('courses')),
            api.enrollments(pageFor('enrollments')),
            api.grades(pageFor('grades')),
        ]);
        Object.assign(responses, {
            courses: courseRecords,
            enrollments: enrollmentRecords,
            grades: gradeRecords,
        });
        loadPage('courses', courseRecords, loadCourses);
        loadPage('enrollments', enrollmentRecords, loadEnrollments);
        loadPage('grades', gradeRecords, loadGrades);
        const instructor = recordsFrom(courseRecords)
            .map((course) => course.instructor)
            .find((record) => Number(record?.user_id) === Number(user.id));
        setCurrentProfile(user, user.instructor ?? instructor);
    }

    if (activeKey && responses[activeKey]) {
        replacePageRecords(activeKey, recordsFrom(responses[activeKey]));
    }

    renderAll();
}

async function saveModal(form) {
    const data = Object.fromEntries(new FormData(form).entries());
    const { type, id } = state.modal;

    if (type === 'student') {
        const payload = {
            phone_number: data.phone || null,
            address: data.address || null,
            date_of_birth: data.dob || null,
        };
        if (id) {
            await api.updateStudent(id, {
                ...payload,
                full_name: data.fullName,
                email: data.email,
            });
        } else {
            const user = await api.createUser({
                full_name: data.fullName,
                email: data.email,
                role: 'student',
            });
            try {
                await api.updateStudent(user.student.student_id, payload);
            } catch (error) {
                await api.deleteUser(user.id);
                throw error;
            }
        }
    }

    if (type === 'course') {
        const payload = {
            instructor_id: Number(data.instructorId),
            course_name: data.courseName,
            description: data.description || null,
            duration: Number(data.duration),
        };
        if (id) await api.updateCourse(id, payload);
        else await api.createCourse(payload);
    }

    if (type === 'enrollment') {
        const payload = {
            student_id: Number(data.studentId),
            course_id: Number(data.courseId),
            enrollment_date: data.enrollmentDate || null,
            status: data.status,
        };
        if (id) await api.updateEnrollment(id, payload);
        else await api.createEnrollment(payload);
    }

    if (type === 'user') {
        const payload = {
            full_name: data.fullName,
            email: data.email,
            role: data.role,
        };
        if (id) await api.updateUser(id, payload);
        else await api.createUser(payload);
    }

    if (type === 'grade') {
        const studentId = Number(data.studentId);
        const courseId = Number(data.courseId);
        const payload = {
            student_id: studentId,
            course_id: courseId,
            score: Number(data.score),
        };
        if (state.currentRole === 'admin' && id) {
            await api.updateGrade(id, payload);
        } else {
            const existing = grades.find((item) => item.studentId === studentId && item.courseId === courseId);
            if (existing) await api.updateGrade(existing.gradeId, { score: payload.score });
            else await api.createGrade(payload);
        }
    }

    if (type === 'profile') {
        const payload = {
            full_name: data.name,
            email: data.email,
            phone_number: data.phone || null,
        };
        if (state.currentRole === 'student') {
            payload.address = data.address || null;
            payload.date_of_birth = data.dateOfBirth || null;
        }
        if (state.currentRole === 'instructor') payload.department = data.department || null;
        if (state.currentRole === 'admin') payload.position = data.position || null;
        await api.updateProfile(payload);
    }

    closeModal();
}

async function deleteRecord(type, id) {
    if (type === 'user' && Number(id) === state.authenticatedUser?.id) {
        setFormMessage('[data-delete-error]', 'You cannot delete the account you are currently using.');
        return;
    }

    try {
        const button = $('[data-confirm-delete]');
        button.disabled = true;
        button.textContent = 'Deleting…';
        if (type === 'student') await api.deleteStudent(id);
        if (type === 'course') await api.deleteCourse(id);
        if (type === 'enrollment') await api.deleteEnrollment(id);
        if (type === 'grade') await api.deleteGrade(id);
        if (type === 'user') await api.deleteUser(id);
        closeModal();
    } catch (error) {
        setFormMessage('[data-delete-error]', error.message);
        const button = $('[data-confirm-delete]');
        button.disabled = false;
        button.textContent = 'Delete';
    }
}

function openRouteModal() {
    const { modalType, modalMode, modalId, modalCourseId } = document.body.dataset;
    if (!modalType || !modalMode) return;

    if (modalMode === 'delete') {
        openDeleteModal(modalType, modalId);
        return;
    }

    if (modalType === 'grade' && document.body.dataset.portalRole === 'instructor') {
        openModal('grade', null, {
            studentId: Number(modalId),
            courseId: Number(modalCourseId),
        });
        return;
    }

    openModal(modalType, modalId ? Number(modalId) : null);
}

async function enrollInCourse(courseId) {
    try {
        await api.createEnrollment({ course_id: Number(courseId) });
        await loadApiData();
    } catch (error) {
        window.alert(error.message);
    }
}

async function signIn(form) {
    const data = new FormData(form);
    setFormMessage('[data-signin-error]');
    setSubmitting(form, true);

    try {
        const response = await api.login({
            email: data.get('email'),
            password: data.get('password'),
            remember: data.get('remember') === 'on',
            device_name: 'sis-frontend',
        });
        saveToken(response.token);
        const role = response.user.role === 'employee' ? 'admin' : response.user.role;
        window.location.assign(dashboardByRole[role]);
    } catch (error) {
        clearToken();
        setFormMessage('[data-signin-error]', error.message);
    } finally {
        setSubmitting(form, false);
    }
}

async function changePassword(form) {
    const data = new FormData(form);
    setFormMessage('[data-change-password-error]');
    setSubmitting(form, true);

    try {
        await api.changePassword({
            email: data.get('email'),
            current_password: data.get('current_password'),
            password: data.get('password'),
            password_confirmation: data.get('password_confirmation'),
        });
        clearToken();
        window.location.assign('/signin?password_changed=1');
    } catch (error) {
        setFormMessage('[data-change-password-error]', error.message);
    } finally {
        setSubmitting(form, false);
    }
}

async function signOut() {
    try {
        if (getToken()) await api.logout();
    } catch {
        // A missing or expired token is already effectively signed out.
    } finally {
        clearToken();
        clearAcademicData();
        state.authenticatedUser = null;
        window.location.assign('/');
    }
}

export async function initSisFrontend() {
    const page = document.body.dataset.page;
    const portalRole = document.body.dataset.portalRole;

    if (page === 'landing') return;

    const changePasswordForm = $('[data-change-password-form]');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', (event) => {
            event.preventDefault();
            changePassword(event.currentTarget);
        });
        return;
    }

    window.addEventListener('sis:unauthorized', () => {
        clearAcademicData();
        state.authenticatedUser = null;
        window.location.replace('/signin');
    });

    const signinForm = $('[data-signin-form]');
    if (signinForm) {
        if (new URLSearchParams(window.location.search).has('password_changed')) {
            setFormMessage(
                '[data-signin-success]',
                'Password changed successfully. Sign in with your new password.',
            );
        }
        signinForm.addEventListener('submit', (event) => {
            event.preventDefault();
            signIn(event.currentTarget);
        });

        if (getToken()) {
            try {
                const user = await api.me();
                const role = user.role === 'employee' ? 'admin' : user.role;
                window.location.replace(dashboardByRole[role]);
            } catch {
                clearToken();
            }
        }

        return;
    }

    if (!getToken()) {
        window.location.replace('/signin');
        return;
    }

    document.addEventListener('click', (event) => {
        const target = event.target.closest('button');
        if (!target) return;

        if (target.dataset.enroll) enrollInCourse(target.dataset.enroll);
        if (target.dataset.closeModal !== undefined) closeModal();
        if (target.dataset.confirmDelete !== undefined) deleteRecord(state.modal.type, state.modal.id);
        if (target.dataset.signOut !== undefined) signOut();
        if (target.dataset.accountToggle !== undefined) $('[data-account-menu]').classList.toggle('is-open');
    });

    $('[data-entity-form]')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        setFormMessage('[data-modal-error]');
        setSubmitting(event.currentTarget, true);
        try {
            await saveModal(event.currentTarget);
        } catch (error) {
            setFormMessage('[data-modal-error]', error.message);
        } finally {
            setSubmitting(event.currentTarget, false);
        }
    });

    $('#global-search')?.addEventListener('input', (event) => {
        state.globalSearch = event.target.value;
        renderAll();
    });

    $$('[data-table-search]').forEach((input) => {
        input.addEventListener('input', (event) => {
            state.tableSearches[event.target.dataset.tableSearch] = event.target.value;
            renderAll();
        });
    });

    try {
        await loadApiData();
        if (state.currentRole !== portalRole) {
            window.location.replace(dashboardByRole[state.currentRole]);
            return;
        }
        openRouteModal();
    } catch {
        clearToken();
        window.location.replace('/signin');
    }
}
