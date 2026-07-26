<div class="modal-backdrop is-hidden" data-modal-backdrop>
    <form class="modal-panel" data-entity-form>
        <div class="modal-header">
            <h2 data-modal-title>Add Record</h2>
            <button class="icon-button" type="button" data-close-modal aria-label="Close modal">
                <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"></path></svg>
            </button>
        </div>
        <div class="form-grid" data-modal-fields></div>
        <p class="form-error is-hidden" data-modal-error role="alert"></p>
        <div class="modal-actions">
            <button class="secondary-action" type="button" data-close-modal>Cancel</button>
            <button class="primary-action" type="submit">Save</button>
        </div>
    </form>
</div>

<div class="modal-backdrop is-hidden" data-delete-backdrop>
    <section class="modal-panel delete-modal" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
        <div class="modal-header">
            <h2 id="delete-modal-title" data-delete-title>Delete Record</h2>
            <button class="icon-button" type="button" data-close-modal aria-label="Close modal">
                <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"></path></svg>
            </button>
        </div>
        <div class="delete-modal-body">
            <span class="delete-warning-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M12 8v5M12 17h.01"></path><path d="M10.3 4.6 2.8 18a2 2 0 0 0 1.8 3h14.8a2 2 0 0 0 1.8-3L13.7 4.6a2 2 0 0 0-3.4 0Z"></path></svg>
            </span>
            <p data-delete-message>Are you sure you want to delete this record? This action cannot be undone.</p>
            <p class="form-error is-hidden" data-delete-error role="alert"></p>
            <div class="modal-actions">
                <button class="secondary-action" type="button" data-close-modal>Cancel</button>
                <button class="danger-action" type="button" data-confirm-delete>Delete</button>
            </div>
        </div>
    </section>
</div>
