const TOKEN_KEY = 'sis_api_token';

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.errors = errors;
    }
}

export const getToken = () => localStorage.getItem(TOKEN_KEY);
export const saveToken = (token) => localStorage.setItem(TOKEN_KEY, token);
export const clearToken = () => localStorage.removeItem(TOKEN_KEY);
const pagePath = (path, page = 1) => `${path}?page=${Math.max(1, Number(page) || 1)}`;

const validationMessage = (payload) => {
    const firstError = Object.values(payload?.errors ?? {}).flat()[0];
    return firstError ?? payload?.message ?? 'The request could not be completed.';
};

export async function apiRequest(
    path,
    { method = 'GET', body, authenticated = true } = {}
) {
    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (body !== undefined) {
        headers['Content-Type'] = 'application/json';
    }

    const token = getToken();

    if (authenticated && token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(`/api/${path.replace(/^\/+/, '')}`, {
        method,
        headers,
        body: body === undefined ? undefined : JSON.stringify(body),
    });

    const payload = response.status === 204
        ? null
        : await response.json().catch(() => null);

    if (!response.ok) {
        if (response.status === 401 && authenticated) {
            clearToken();
            window.dispatchEvent(
                new CustomEvent('sis:unauthorized')
            );
        }

        throw new ApiError(
            validationMessage(payload),
            response.status,
            payload?.errors
        );
    }

    return payload;
}

export const api = {
    login: (credentials) => apiRequest('auth/login', {
        method: 'POST',
        body: credentials,
        authenticated: false,
    }),
    changePassword: (payload) => apiRequest('auth/change-password', {
        method: 'POST',
        body: payload,
        authenticated: false,
    }),
    me: () => apiRequest('auth/me'),
    logout: () => apiRequest('auth/logout', { method: 'POST' }),
    updateProfile: (payload) => apiRequest('profile', { method: 'PATCH', body: payload }),
    users: (page = 1) => apiRequest(pagePath('users', page)),
    createUser: (payload) => apiRequest('users', { method: 'POST', body: payload }),
    updateUser: (id, payload) => apiRequest(`users/${id}`, { method: 'PATCH', body: payload }),
    deleteUser: (id) => apiRequest(`users/${id}`, { method: 'DELETE' }),
    students: (page = 1) => apiRequest(pagePath('students', page)),
    studentMe: () => apiRequest('students/me'),
    createStudent: (payload) => apiRequest('students', { method: 'POST', body: payload }),
    updateStudent: (id, payload) => apiRequest(`students/${id}`, { method: 'PATCH', body: payload }),
    updateStudentMe: (payload) => apiRequest('students/me', { method: 'PATCH', body: payload }),
    deleteStudent: (id) => apiRequest(`students/${id}`, { method: 'DELETE' }),
    courses: (page = 1) => apiRequest(pagePath('courses', page)),
    createCourse: (payload) => apiRequest('courses', { method: 'POST', body: payload }),
    updateCourse: (id, payload) => apiRequest(`courses/${id}`, { method: 'PATCH', body: payload }),
    deleteCourse: (id) => apiRequest(`courses/${id}`, { method: 'DELETE' }),
    enrollments: (page = 1) => apiRequest(pagePath('enrollments', page)),
    createEnrollment: (payload) => apiRequest('enrollments', { method: 'POST', body: payload }),
    updateEnrollment: (id, payload) => apiRequest(`enrollments/${id}`, { method: 'PATCH', body: payload }),
    deleteEnrollment: (id) => apiRequest(`enrollments/${id}`, { method: 'DELETE' }),
    grades: (page = 1) => apiRequest(pagePath('grades', page)),
    createGrade: (payload) => apiRequest('grades', { method: 'POST', body: payload }),
    updateGrade: (id, payload) => apiRequest(`grades/${id}`, { method: 'PATCH', body: payload }),
    deleteGrade: (id) => apiRequest(`grades/${id}`, { method: 'DELETE' }),
};
