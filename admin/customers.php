<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('admin');

$conn = getDBConnection();
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = sanitizeInput($_POST['name']);
                $phone = sanitizeInput($_POST['phone']);
                $address = sanitizeInput($_POST['address']);
                $status = sanitizeInput($_POST['status']);

                $stmt = $conn->prepare("INSERT INTO customers (name, phone, address, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $phone, $address, $status);

                if ($stmt->execute()) {
                    $message = 'Customer added successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error adding customer: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;

            case 'update':
                $id = intval($_POST['id']);
                $name = sanitizeInput($_POST['name']);
                $phone = sanitizeInput($_POST['phone']);
                $address = sanitizeInput($_POST['address']);
                $status = sanitizeInput($_POST['status']);

                $stmt = $conn->prepare("UPDATE customers SET name = ?, phone = ?, address = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $phone, $address, $status, $id);

                if ($stmt->execute()) {
                    $message = 'Customer updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating customer: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;

            case 'delete':
                $id = intval($_POST['id']);

                $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $message = 'Customer deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error deleting customer: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;
        }
    }
}

// Get all customers
$customersQuery = "SELECT * FROM customers ORDER BY created_at DESC";
$customers = $conn->query($customersQuery);

renderHeader('Customers');
?>

<div class="dashboard-layout">
    <?php renderSidebar('customers'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Customers</h1>
                    <p class="page-subtitle">Manage your customer database</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('addCustomerModal')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Customer
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Search -->
            <div class="card mb-6">
                <div class="input-group">
                    <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="search" id="searchInput" class="input" placeholder="Search customers...">
                </div>
            </div>

            <!-- Customers Table -->
            <div class="card">
                <div class="table-container">
                    <table class="table" id="customersTable">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Total Orders</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($customers->num_rows > 0): ?>
                                <?php while ($customer = $customers->fetch_assoc()): ?>
                                    <tr>
                                        <td class="font-medium"><?php echo htmlspecialchars($customer['name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['address']); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($customer['status']); ?>">
                                                <?php echo ucfirst($customer['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($customer['total_orders']); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="icon-btn" onclick="editCustomer(<?php echo htmlspecialchars(json_encode($customer)); ?>)" title="Edit">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                </button>
                                                <button class="icon-btn icon-btn-delete" onclick="deleteCustomer(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars($customer['name']); ?>')" title="Delete">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div class="empty-state-title">No customers found</div>
                                            <div class="empty-state-description">Add your first customer to get started</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Add New Customer</h2>
            <button class="modal-close" data-close-modal="addCustomerModal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="add_name">Customer Name</label>
                    <input type="text" id="add_name" name="name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_phone">Phone Number</label>
                    <input type="tel" id="add_phone" name="phone" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_address">Address</label>
                    <textarea id="add_address" name="address" class="input" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_status">Status</label>
                    <select id="add_status" name="status" class="input" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-close-modal="addCustomerModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="editCustomerModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Edit Customer</h2>
            <button class="modal-close" data-close-modal="editCustomerModal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_id" name="id">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit_name">Customer Name</label>
                    <input type="text" id="edit_name" name="name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_phone">Phone Number</label>
                    <input type="tel" id="edit_phone" name="phone" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_address">Address</label>
                    <textarea id="edit_address" name="address" class="input" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="input" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-close-modal="editCustomerModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="delete_id" name="id">
</form>

<script>
    function editCustomer(customer) {
        document.getElementById('edit_id').value = customer.id;
        document.getElementById('edit_name').value = customer.name;
        document.getElementById('edit_phone').value = customer.phone;
        document.getElementById('edit_address').value = customer.address;
        document.getElementById('edit_status').value = customer.status;
        openModal('editCustomerModal');
    }

    function deleteCustomer(id, name) {
        if (confirmDelete(name)) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>

<?php
closeDBConnection($conn);
renderFooter();
?>