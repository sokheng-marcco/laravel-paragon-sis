@if ($portalRole === 'admin')
    <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"></path></svg></span>
        Dashboard
    </a>
    <a class="nav-item {{ request()->routeIs('admin.students*') ? 'is-active' : '' }}" href="{{ route('admin.students') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4"></path><circle cx="12" cy="9" r="3"></circle></svg></span>
        Students
    </a>
    <a class="nav-item {{ request()->routeIs('admin.courses*') ? 'is-active' : '' }}" href="{{ route('admin.courses') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg></span>
        Courses
    </a>
    <a class="nav-item {{ request()->routeIs('admin.enrollments*') ? 'is-active' : '' }}" href="{{ route('admin.enrollments') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M8 12.5 11 15l5-6"></path><path d="M5 4h14v16H5z"></path></svg></span>
        Enrollments
    </a>
    <a class="nav-item {{ request()->routeIs('admin.grades*') ? 'is-active' : '' }}" href="{{ route('admin.grades') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="m12 4 2.2 4.5 4.8.7-3.5 3.4.8 4.8-4.3-2.3-4.3 2.3.8-4.8L5 9.2l4.8-.7L12 4Z"></path></svg></span>
        Grades
    </a>
    <a class="nav-item {{ request()->routeIs('admin.users*') ? 'is-active' : '' }}" href="{{ route('admin.users') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M7 20v-2a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v2"></path><circle cx="12" cy="8" r="4"></circle></svg></span>
        User Accounts
    </a>
@elseif ($portalRole === 'student')
    <a class="nav-item {{ request()->routeIs('student.dashboard') ? 'is-active' : '' }}" href="{{ route('student.dashboard') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"></path></svg></span>
        Dashboard
    </a>
    <a class="nav-item {{ request()->routeIs('student.courses') ? 'is-active' : '' }}" href="{{ route('student.courses') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg></span>
        Browse Courses
    </a>
    <a class="nav-item {{ request()->routeIs('student.enrollments') ? 'is-active' : '' }}" href="{{ route('student.enrollments') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M8 12.5 11 15l5-6"></path><path d="M5 4h14v16H5z"></path></svg></span>
        My Enrollments
    </a>
    <a class="nav-item {{ request()->routeIs('student.grades*') ? 'is-active' : '' }}" href="{{ route('student.grades') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="m12 4 2.2 4.5 4.8.7-3.5 3.4.8 4.8-4.3-2.3-4.3 2.3.8-4.8L5 9.2l4.8-.7L12 4Z"></path></svg></span>
        My Grades
    </a>
@elseif ($portalRole === 'instructor')
    <a class="nav-item {{ request()->routeIs('instructor.dashboard') ? 'is-active' : '' }}" href="{{ route('instructor.dashboard') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"></path></svg></span>
        Dashboard
    </a>
    <a class="nav-item {{ request()->routeIs('instructor.courses') ? 'is-active' : '' }}" href="{{ route('instructor.courses') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg></span>
        My Courses
    </a>
    <a class="nav-item {{ request()->routeIs('instructor.grades*') ? 'is-active' : '' }}" href="{{ route('instructor.grades') }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M4 6h16M6 10h12M8 14h8M10 18h4"></path></svg></span>
        Grade Management
    </a>
@endif

<a class="nav-item {{ request()->routeIs($portalRole.'.profile*') ? 'is-active' : '' }}" href="{{ route($portalRole.'.profile') }}">
    <span class="nav-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.8-3.2 3.2-5 7-5s6.2 1.8 7 5"></path></svg></span>
    Profile
</a>
