<section class="content-view is-active">
    <div class="page-heading row-heading">
        <div>
            <h1>Profile</h1>
            <p>Review and update your personal account details.</p>
        </div>
        <a class="primary-action" href="{{ route($portalRole.'.profile.edit') }}">Edit Profile</a>
    </div>

    <section class="profile-grid">
        <article class="panel profile-card">
            <span class="profile-avatar" data-profile-initial>S</span>
            <div>
                <h2 data-profile-name>User</h2>
                <p data-profile-email>—</p>
                <span class="status-pill active" data-profile-role>role</span>
            </div>
        </article>

        <article class="panel">
            <h2>Account Details</h2>
            <dl class="detail-list">
                <div><dt data-profile-id-label>Account ID</dt><dd data-profile-id>—</dd></div>
                <div><dt>Phone</dt><dd data-profile-phone>—</dd></div>
                <div><dt data-profile-secondary-label>Department</dt><dd data-profile-secondary>—</dd></div>
                <div data-profile-address-row><dt>Address</dt><dd data-profile-address>—</dd></div>
            </dl>
        </article>
    </section>
</section>
