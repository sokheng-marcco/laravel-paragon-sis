<main class="auth-page">
    <section class="signin-wrap">
        <span class="signin-logo">
            <svg viewBox="0 0 24 24"><path d="M2.5 8.4 12 4l9.5 4.4L12 12.8 2.5 8.4Z"></path><path d="M6.8 10.6v4.1c0 1.4 2.3 2.7 5.2 2.7s5.2-1.3 5.2-2.7v-4.1"></path></svg>
        </span>
        <div class="page-heading auth-heading">
            <h1>Sign in to EduManage</h1>
            <p>Welcome back to the Student Information System</p>
        </div>
        <form class="signin-card" data-signin-form>
            <label>
                <span>Email address</span>
                <input name="email" type="email" placeholder="you@university.edu" value="admin@university.edu" required>
            </label>
            <label>
                <span>Password</span>
                <input name="password" type="password" required>
            </label>
            <div class="signin-row">
                <label class="check-label"><input name="remember" type="checkbox"> Remember me</label>
                <a class="link-button" href="{{ route('password.change') }}">Change password?</a>
            </div>
            <p class="form-success is-hidden" data-signin-success role="status"></p>
            <p class="form-error is-hidden" data-signin-error role="alert"></p>
            <button class="primary-action full-button" type="submit">Sign in</button>
        </form>
        <a class="link-button back-link" href="{{ route('home') }}">Back to landing</a>
    </section>
</main>
