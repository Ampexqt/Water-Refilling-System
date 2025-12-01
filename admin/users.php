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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = sanitizeInput($_POST['role']);
            $status = sanitizeInput($_POST['status']);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $password, $role, $status);

            if ($stmt->execute()) {
                $message = 'User created successfully!';
                $messageType = 'success';
            } else {
                $message = 'Error creating user: ' . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
            break;

        case 'update':
            $id = intval($_POST['id']);
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $role = sanitizeInput($_POST['role']);
            $status = sanitizeInput($_POST['status']);

            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $name, $email, $password, $role, $status, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $email, $role, $status, $id);
            }

            if ($stmt->execute()) {
                $message = 'User updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Error updating user: ' . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
            break;

        case 'delete':
            $id = intval($_POST['id']);

            if ($id === getUserId()) {
                $message = 'You cannot delete your own account!';
                $messageType = 'error';
            } else {
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $message = 'User deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error deleting user: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
            }
            break;
    }
}

// Get all users
$usersQuery = "SELECT * FROM users ORDER BY created_at DESC";
$users = $conn->query($usersQuery);

renderHeader('User Management');
?>

<div class="dashboard-layout">
    <?php renderSidebar('users'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">User Management</h1>
                    <p class="page-subtitle">Manage system users and permissions</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('addUserModal')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add User
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="card">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td class="font-medium"><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($user['status']); ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['last_login'] ? formatDateTime($user['last_login']) : 'Never'; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="icon-btn" onclick='editUser(<?php echo json_encode($user); ?>)' title="Edit">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                            <?php if ($user['id'] !== getUserId()): ?>
                                                <button class="icon-btn icon-btn-delete" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>')" title="Delete">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Add New User</h2>
            <button class="modal-close" data-close-modal="addUserModal">
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
                    <label class="form-label" for="add_name">Name</label>
                    <input type="text" id="add_name" name="name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_email">Email</label>
                    <input type="email" id="add_email" name="email" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_password">Password</label>
                    <input type="password" id="add_password" name="password" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_role">Role</label>
                    <select id="add_role" name="role" class="input" required>
                        <option value="cashier">Cashier</option>
                        <option value="admin">Admin</option>
                    </select>
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
                <button type="button" class="btn btn-secondary" data-close-modal="addUserModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Edit User</h2>
            <button class="modal-close" data-close-modal="editUserModal">
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
                    <label class="form-label" for="edit_name">Name</label>
                    <input type="text" id="edit_name" name="name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_password">Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password" class="input">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_role">Role</label>
                    <select id="edit_role" name="role" class="input" required>
                        <option value="cashier">Cashier</option>
                        <option value="admin">Admin</option>
                    </select>
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
                <button type="button" class="btn btn-secondary" data-close-modal="editUserModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="delete_id" name="id">
</form>

<script>
    function editUser(user) {
        document.getElementById('edit_id').value = user.id;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_status').value = user.status;
        openModal('editUserModal');
    }

    function deleteUser(id, name) {
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