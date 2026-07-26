export const state = {
    currentRole: 'admin',
    globalSearch: '',
    tableSearches: {},
    modal: null,
    authenticatedUser: null,
    activePaginationKey: null,
};

const emptyProfile = (role) => ({
    name: '',
    email: '',
    role,
    phone: '',
    department: '',
    address: '',
    studentId: null,
    instructorId: null,
    employeeId: null,
    dateOfBirth: '',
    position: '',
});

export const profiles = {
    admin: emptyProfile('employee'),
    student: emptyProfile('student'),
    instructor: emptyProfile('instructor'),
};

// These collections are populated from the API after authentication.
export const users = [];
export const students = [];
export const instructors = [];
export const courses = [];
export const enrollments = [];
export const grades = [];
export const pagination = {};

const replace = (target, records) => {
    target.splice(0, target.length, ...records);
};

const dateOnly = (value) => value ? String(value).slice(0, 10) : '';

export const normalizeUser = (user) => ({
    id: Number(user.id),
    fullName: user.full_name,
    email: user.email,
    role: user.role,
    createdAt: dateOnly(user.created_at),
    student: user.student ?? null,
    instructor: user.instructor ?? null,
    employee: user.employee ?? null,
});

export const normalizeStudent = (student) => ({
    studentId: Number(student.student_id),
    userId: Number(student.user_id),
    phone: student.phone_number ?? '',
    address: student.address ?? '',
    dob: dateOnly(student.date_of_birth),
});

export const normalizeInstructor = (instructor) => ({
    instructorId: Number(instructor.instructor_id),
    userId: Number(instructor.user_id),
    phone: instructor.phone_number ?? '',
    department: instructor.department ?? '',
});

export const normalizeCourse = (course) => ({
    courseId: Number(course.course_id),
    instructorId: Number(course.instructor_id),
    courseName: course.course_name,
    description: course.description ?? '',
    duration: Number(course.duration),
    enrollmentsCount: Number(course.enrollments_count ?? 0),
});

export const normalizeEnrollment = (enrollment) => ({
    enrollmentId: Number(enrollment.enrollment_id),
    studentId: Number(enrollment.student_id),
    courseId: Number(enrollment.course_id),
    employeeId: enrollment.employee_id ? Number(enrollment.employee_id) : null,
    enrollmentDate: dateOnly(enrollment.enrollment_date),
    status: enrollment.status,
});

export const normalizeGrade = (grade) => ({
    gradeId: Number(grade.grade_id),
    studentId: Number(grade.student_id),
    courseId: Number(grade.course_id),
    score: Number(grade.score),
    grade: grade.grade,
    gradedBy: Number(grade.graded_by),
    gradedAt: dateOnly(grade.graded_at),
});

const upsertUser = (user) => {
    if (!user) return;
    const normalized = normalizeUser(user);
    const definedAttributes = Object.fromEntries(
        Object.entries(normalized).filter(([, value]) => value !== undefined),
    );
    const index = users.findIndex((item) => item.id === normalized.id);
    if (index >= 0) users[index] = { ...users[index], ...definedAttributes };
    else users.push(definedAttributes);
};

const upsertStudent = (student) => {
    if (!student) return;
    upsertUser(student.user);
    const normalized = normalizeStudent(student);
    const index = students.findIndex((item) => item.studentId === normalized.studentId);
    if (index >= 0) students[index] = normalized;
    else students.push(normalized);
};

const upsertInstructor = (instructor) => {
    if (!instructor) return;
    upsertUser(instructor.user);
    const normalized = normalizeInstructor(instructor);
    const index = instructors.findIndex((item) => item.instructorId === normalized.instructorId);
    if (index >= 0) instructors[index] = normalized;
    else instructors.push(normalized);
};

const upsertCourse = (course) => {
    if (!course) return;
    upsertInstructor(course.instructor);
    const normalized = normalizeCourse(course);
    const index = courses.findIndex((item) => item.courseId === normalized.courseId);
    if (index >= 0) courses[index] = normalized;
    else courses.push(normalized);
};

export function clearAcademicData() {
    replace(users, []);
    replace(students, []);
    replace(instructors, []);
    replace(courses, []);
    replace(enrollments, []);
    replace(grades, []);
    Object.keys(pagination).forEach((key) => delete pagination[key]);
    state.activePaginationKey = null;
}

export function setPagination(key, response) {
    pagination[key] = {
        currentPage: Number(response.current_page ?? 1),
        lastPage: Number(response.last_page ?? 1),
        perPage: Number(response.per_page ?? 10),
        total: Number(response.total ?? response.data?.length ?? 0),
        from: response.from,
        to: response.to,
    };
}

export function replacePageRecords(key, records) {
    if (key === 'users') {
        replace(users, []);
        loadUsers(records);
    }

    if (key === 'students') {
        replace(students, []);
        loadStudents(records);
    }

    if (key === 'courses') {
        replace(courses, []);
        loadCourses(records);
    }

    if (key === 'enrollments') {
        replace(enrollments, records.map(normalizeEnrollment));
    }

    if (key === 'grades') {
        replace(grades, records.map(normalizeGrade));
    }
}

export function loadUsers(records) {
    records.forEach((record) => {
        upsertUser(record);
        if (record.student) upsertStudent({ ...record.student, user: record });
        if (record.instructor) upsertInstructor({ ...record.instructor, user: record });
    });
}

export function loadStudents(records) {
    records.forEach(upsertStudent);
}

export function loadCourses(records) {
    records.forEach(upsertCourse);
}

export function loadEnrollments(records) {
    records.forEach((record) => {
        upsertStudent(record.student);
        upsertCourse(record.course);
        upsertInstructor(record.course?.instructor);
    });
    replace(enrollments, records.map(normalizeEnrollment));
}

export function loadGrades(records) {
    records.forEach((record) => {
        upsertStudent(record.student);
        upsertInstructor(record.grader);
    });
    replace(grades, records.map(normalizeGrade));
}

export function setCurrentProfile(user, relatedProfile = null) {
    const uiRole = user.role === 'employee' ? 'admin' : user.role;
    const profile = profiles[uiRole];

    Object.assign(profile, emptyProfile(user.role), {
        name: user.full_name,
        email: user.email,
        role: user.role,
    });

    if (uiRole === 'student' && relatedProfile) {
        Object.assign(profile, {
            phone: relatedProfile.phone_number ?? '',
            address: relatedProfile.address ?? '',
            studentId: Number(relatedProfile.student_id),
            dateOfBirth: dateOnly(relatedProfile.date_of_birth),
        });
    }

    if (uiRole === 'admin' && relatedProfile) {
        Object.assign(profile, {
            phone: relatedProfile.phone_number ?? '',
            department: relatedProfile.position ?? '',
            position: relatedProfile.position ?? '',
            employeeId: Number(relatedProfile.employee_id),
        });
    }

    if (uiRole === 'instructor' && relatedProfile) {
        Object.assign(profile, {
            phone: relatedProfile.phone_number ?? '',
            department: relatedProfile.department ?? '',
            instructorId: Number(relatedProfile.instructor_id),
        });
    }

    state.authenticatedUser = normalizeUser(user);
    state.currentRole = uiRole;
}

export const formatDate = (date) => {
    if (!date) return '—';
    const parsed = new Date(`${dateOnly(date)}T00:00:00`);
    return Number.isNaN(parsed.valueOf()) ? '—' : new Intl.DateTimeFormat('en-CA').format(parsed);
};

export const userById = (id) => users.find((user) => user.id === Number(id));
export const studentById = (id) => students.find((student) => student.studentId === Number(id));
export const courseById = (id) => courses.find((course) => course.courseId === Number(id));
export const instructorById = (id) => instructors.find((instructor) => instructor.instructorId === Number(id));
export const userName = (id) => userById(id)?.fullName ?? 'Unknown';
export const studentName = (id) => userName(studentById(id)?.userId);
export const courseName = (id) => courseById(id)?.courseName ?? 'Unknown course';
export const instructorName = (id) => userName(instructorById(id)?.userId);
export const matchesSearch = (values, search) => values
    .filter((value) => value !== null && value !== undefined)
    .join(' ')
    .toLowerCase()
    .includes((search ?? '').trim().toLowerCase());
