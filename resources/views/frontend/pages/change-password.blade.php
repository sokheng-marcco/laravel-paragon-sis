<main class="auth-page">
    <section class="signin-wrap">
        <span class="signin-logo">
            <svg viewBox="0 0 24 24"><path d="M2.5 8.4 12 4l9.5 4.4L12 12.8 2.5 8.4Z"></path><path d="M6.8 10.6v4.1c0 1.4 2.3 2.7 5.2 2.7s5.2-1.3 5.2-2.7v-4.1"></path></svg>
        </span>
        <div class="page-heading auth-heading">
            <h1>Change your password</h1>
            <p>Verify your current password before choosing a new one.</p>
        </div>
        <form class="signin-card" data-change-password-form>
            <label>
                <span>Email address</span>
                <input name="email" type="email" placeholder="you@university.edu" required>
            </label>
            <label>
                <span>Current password</span>
                <input name="current_password" type="password" autocomplete="current-password" required>
            </label>
            <label>
                <span>New password</span>
                <input name="password" type="password" minlength="8" autocomplete="new-password" required>
            </label>
            <label>
                <span>Confirm new password</span>
                <input name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
            </label>
            <p class="form-error is-hidden" data-change-password-error role="alert"></p>
            <button class="primary-action full-button" type="submit">Change password</button>
        </form>
        <a class="link-button back-link" href="{{ route('signin') }}">Back to sign in</a>
    </section>
</main>
