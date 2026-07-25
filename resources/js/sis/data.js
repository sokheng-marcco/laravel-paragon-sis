export const state = {
    screen: 'landing',
    currentRole: 'admin',
    currentPanel: 'admin-dashboard',
    globalSearch: '',
    tableSearches: {},
    modal: null,
};

export const profiles = {
    admin: {
        name: 'System Admin',
        email: 'admin@edumanage.edu.kh',
        role: 'admin',
        phone: '+855 12 443 210',
        department: 'Academic Administration',
        address: 'Phnom Penh, Cambodia',
    },
    student: {
        name: 'Student 1',
        email: 'student1@university.edu',
        role: 'student',
        phone: '555-0100',
        department: 'Bachelor of Information Systems',
        address: 'Phnom Penh, Cambodia',
        studentId: 'S1000',
    },
    instructor: {
        name: 'Instructor 1',
        email: 'instructor1@university.edu',
        role: 'instructor',
        phone: '555-0201',
        department: 'Computer Science',
        address: 'Phnom Penh, Cambodia',
        instructorId: 201,
    },
};

export const users = [
    { id: 1, fullName: 'Student 1', email: 'student1@university.edu', role: 'student', createdAt: '2026-01-08' },
    { id: 2, fullName: 'Student 2', email: 'student2@university.edu', role: 'student', createdAt: '2026-01-10' },
    { id: 3, fullName: 'Student 3', email: 'student3@university.edu', role: 'student', createdAt: '2026-01-12' },
    { id: 4, fullName: 'Student 4', email: 'student4@university.edu', role: 'student', createdAt: '2026-01-14' },
    { id: 5, fullName: 'Student 5', email: 'student5@university.edu', role: 'student', createdAt: '2026-01-16' },
    { id: 6, fullName: 'Instructor 1', email: 'instructor1@university.edu', role: 'instructor', createdAt: '2025-12-20' },
    { id: 7, fullName: 'Instructor 2', email: 'instructor2@university.edu', role: 'instructor', createdAt: '2025-12-22' },
    { id: 8, fullName: 'Registrar Admin', email: 'registrar@university.edu', role: 'admin', createdAt: '2025-12-26' },
    { id: 9, fullName: 'System Admin', email: 'admin@university.edu', role: 'admin', createdAt: '2025-12-27' },
];

export const students = [
    { studentId: 'S1000', userId: 1, phone: '555-0100', address: 'Phnom Penh', dob: '2000-07-15' },
    { studentId: 'S1001', userId: 2, phone: '555-0101', address: 'Phnom Penh', dob: '2000-05-15' },
    { studentId: 'S1002', userId: 3, phone: '555-0102', address: 'Phnom Penh', dob: '2003-07-15' },
    { studentId: 'S1003', userId: 4, phone: '555-0103', address: 'Phnom Penh', dob: '2003-02-15' },
    { studentId: 'S1004', userId: 5, phone: '555-0104', address: 'Phnom Penh', dob: '2000-02-15' },
];

export const instructors = [
    { instructorId: 201, userId: 6, phone: '555-0201', department: 'Computer Science' },
    { instructorId: 202, userId: 7, phone: '555-0202', department: 'Information Systems' },
    { instructorId: 203, userId: 6, phone: '555-0203', department: 'Mathematics' },
    { instructorId: 204, userId: 7, phone: '555-0204', department: 'Business' },
    { instructorId: 205, userId: 6, phone: '555-0205', department: 'English' },
    { instructorId: 206, userId: 7, phone: '555-0206', department: 'Design' },
];

export const courses = [
    { courseId: 'C2000', instructorId: 201, courseName: 'Database Systems', description: 'Relational data design.', duration: 16 },
    { courseId: 'C2001', instructorId: 202, courseName: 'Student Information Systems', description: 'Academic records and workflows.', duration: 14 },
    { courseId: 'C2002', instructorId: 203, courseName: 'Web Application Development', description: 'Frontend and Laravel workflows.', duration: 15 },
    { courseId: 'C2003', instructorId: 204, courseName: 'Business Analytics', description: 'Dashboards and reporting.', duration: 12 },
    { courseId: 'C2004', instructorId: 205, courseName: 'Academic Writing', description: 'Research and writing practice.', duration: 10 },
    { courseId: 'C2005', instructorId: 206, courseName: 'UI Design Fundamentals', description: 'Product interface foundations.', duration: 11 },
];

export const enrollments = Array.from({ length: 12 }, (_, index) => ({
    enrollmentId: `E${3000 + index}`,
    studentId: students[index % students.length].studentId,
    courseId: courses[index % courses.length].courseId,
    enrollmentDate: `2026-02-${String((index % 24) + 1).padStart(2, '0')}`,
    status: index % 5 === 0 ? 'pending' : 'completed',
}));

export const grades = Array.from({ length: 10 }, (_, index) => {
    const score = [82, 76, 91, 88, 72, 95, 69, 84, 79, 86][index];
    return {
        gradeId: `G${4000 + index}`,
        studentId: students[index % students.length].studentId,
        courseId: courses[index % courses.length].courseId,
        score,
        grade: scoreToLetter(score),
        gradedBy: instructors[index % instructors.length].instructorId,
        gradedAt: `2026-05-${String((index % 24) + 1).padStart(2, '0')}`,
    };
});

export const nextId = (prefix, records, key) => {
    const max = records.reduce((highest, item) => {
        const numeric = Number(String(item[key]).replace(/\D/g, ''));
        return Number.isNaN(numeric) ? highest : Math.max(highest, numeric);
    }, 0);
    return `${prefix}${max + 1}`;
};

export function scoreToLetter(score) {
    const value = Number(score);
    if (value >= 90) return 'A';
    if (value >= 80) return 'B+';
    if (value >= 70) return 'B';
    if (value >= 60) return 'C';
    return 'F';
}

export const formatDate = (date) => new Intl.DateTimeFormat('en-CA').format(new Date(date));
export const userById = (id) => users.find((user) => user.id === Number(id));
export const studentById = (id) => students.find((student) => student.studentId === id);
export const courseById = (id) => courses.find((course) => course.courseId === id);
export const instructorById = (id) => instructors.find((instructor) => instructor.instructorId === Number(id));
export const userName = (id) => userById(id)?.fullName ?? 'Unknown';
export const studentName = (id) => userName(studentById(id)?.userId);
export const courseName = (id) => courseById(id)?.courseName ?? 'Unknown course';
export const instructorName = (id) => userName(instructorById(id)?.userId);

export const matchesSearch = (values, search) => values.join(' ').toLowerCase().includes((search ?? '').trim().toLowerCase());
