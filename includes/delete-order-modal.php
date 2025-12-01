<!-- Delete Confirmation Modal -->
<div id="deleteOrderModal" class="modal-overlay">
    <div class="modal modal-logout">
        <button class="modal-close-logout" data-close-modal="deleteOrderModal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </button>

        <div class="logout-modal-content">
            <div class="logout-title-badge" style="background: #FFEBEE;">
                <h2 style="color: #C62828;">Delete Order</h2>
            </div>

            <p class="logout-message">You are about to delete this order.<br>This action cannot be undone. Are you sure?</p>
            <p class="logout-message" style="font-weight: 600; color: var(--neutral-700); margin-top: 0.5rem;" id="deleteOrderName"></p>

            <div class="logout-actions">
                <button type="button" class="btn-logout-confirm" style="background: #C62828;" onclick="confirmDeleteOrder()">
                    Delete Order
                </button>
                <button type="button" class="btn-logout-cancel" data-close-modal="deleteOrderModal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>