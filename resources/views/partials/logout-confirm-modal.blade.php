@once
    <div class="logout-confirm-modal" id="logoutConfirmModal" aria-hidden="true">
        <button type="button" class="logout-confirm-modal__backdrop" data-logout-confirm-cancel
            aria-label="Cancel sign out"></button>

        <section class="logout-confirm-modal__dialog" role="dialog" aria-modal="true"
            aria-labelledby="logoutConfirmTitle" aria-describedby="logoutConfirmMessage" tabindex="-1">
            <div class="logout-confirm-modal__icon" aria-hidden="true">
                <i class="ri-logout-box-r-line"></i>
            </div>

            <h2 id="logoutConfirmTitle">Sign out?</h2>
            <p id="logoutConfirmMessage">You will need to sign in again to access your account.</p>

            <div class="logout-confirm-modal__actions">
                <button type="button" class="logout-confirm-modal__btn logout-confirm-modal__btn--secondary"
                    data-logout-confirm-cancel>
                    Stay signed in
                </button>
                <button type="button" class="logout-confirm-modal__btn logout-confirm-modal__btn--danger"
                    id="logoutConfirmSubmit">
                    Sign out
                </button>
            </div>
        </section>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('logoutConfirmModal');
            const confirmBtn = document.getElementById('logoutConfirmSubmit');
            const dialog = modal?.querySelector('.logout-confirm-modal__dialog');
            let pendingLogoutForm = null;
            let previouslyFocused = null;

            if (!modal || !confirmBtn || modal.dataset.bound === 'true') return;
            modal.dataset.bound = 'true';

            function openLogoutConfirm(form) {
                pendingLogoutForm = form;
                previouslyFocused = document.activeElement;

                document.querySelector('.user-dropdown.show')?.classList.remove('show');
                document.body.classList.add('logout-confirm-open');
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');

                window.setTimeout(() => dialog?.focus(), 0);
            }

            function closeLogoutConfirm() {
                pendingLogoutForm = null;
                confirmBtn.disabled = false;
                document.body.classList.remove('logout-confirm-open');
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');

                if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
                    previouslyFocused.focus();
                }
            }

            document.addEventListener('submit', function(event) {
                const form = event.target?.closest?.('form[data-logout-confirm]');
                if (!form || form.dataset.logoutConfirmed === 'true') return;

                event.preventDefault();
                openLogoutConfirm(form);
            }, true);

            modal.querySelectorAll('[data-logout-confirm-cancel]').forEach((button) => {
                button.addEventListener('click', closeLogoutConfirm);
            });

            confirmBtn.addEventListener('click', function() {
                if (!pendingLogoutForm) return;

                confirmBtn.disabled = true;
                pendingLogoutForm.dataset.logoutConfirmed = 'true';

                try {
                    sessionStorage.setItem('tkLoadingMode', 'brand');
                } catch (error) {
                    // Navigation should still continue if storage is blocked.
                }

                HTMLFormElement.prototype.submit.call(pendingLogoutForm);
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeLogoutConfirm();
                }
            });
        })();
    </script>
@endonce
