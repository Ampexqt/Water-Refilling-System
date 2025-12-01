<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal modal-logout">
        <button class="modal-close-logout" data-close-modal="logoutModal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </button>

        <div class="logout-modal-content">
            <div class="logout-title-badge">
                <h2>Logout</h2>
            </div>

            <p class="logout-message">You are going to log out your account.<br>Are you sure?</p>

            <div class="logout-actions">
                <a href="/Water-Refilling-System/auth/logout.php" class="btn-logout-confirm">
                    Log out
                </a>
                <button type="button" class="btn-logout-cancel" data-close-modal="logoutModal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>