<div id="sis-app" class="app-shell is-hidden" data-screen="app">
    <aside class="sidebar" aria-label="Primary navigation">
        <div class="brand">
            <span class="brand-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M2.5 8.4 12 4l9.5 4.4L12 12.8 2.5 8.4Z"></path><path d="M6.8 10.6v4.1c0 1.4 2.3 2.7 5.2 2.7s5.2-1.3 5.2-2.7v-4.1"></path></svg>
            </span>
            <strong>EduManage SIS</strong>
        </div>

        <div class="nav-label" data-role-label>admin</div>
        <nav class="nav-list" aria-label="Sections">
            @include('frontend.partials.nav')
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="menu-button" type="button" data-mobile-menu aria-label="Open navigation">
                <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
            </button>

            <label class="search-control">
                <input id="global-search" type="search" placeholder="Search anywhere...">
            </label>

            <div class="topbar-actions">
                <button class="notification-button" type="button" aria-label="Notifications">
                    <svg viewBox="0 0 24 24"><path d="M18 16v-5a6 6 0 0 0-12 0v5l-2 2h16l-2-2Z"></path><path d="M10 20h4"></path></svg>
                    <span></span>
                </button>
                <div class="admin-menu" data-account-menu>
                    <button class="account-button" type="button" data-account-toggle>
                        <span class="admin-avatar">S</span>
                        <span>
                            <strong data-current-name>System Admin</strong>
                            <small data-current-role>admin</small>
                        </span>
                        <svg viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"></path></svg>
                    </button>
                    <div class="account-dropdown">
                        <button type="button" data-open-profile-editor>Edit Profile</button>
                        <button type="button" data-sign-out>Sign out</button>
                    </div>
                </div>
            </div>
        </header>

        @include('frontend.pages.admin')
        @include('frontend.pages.student')
        @include('frontend.pages.instructor')
        @include('frontend.pages.profile')

        <footer class="footer">© 2026 EduManage SIS. All rights reserved.</footer>
    </main>
</div>
