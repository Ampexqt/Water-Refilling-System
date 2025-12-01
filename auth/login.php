<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/session.php';

$error = '';
$success = '';

if (isLoggedIn()) {
    $role = getUserRole();
    header('Location: ' . BASE_URL . '/' . $role . '/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Enhanced validation
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $conn = getDBConnection();

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? AND status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password using secure password_verify function
            // This uses bcrypt hashing which is resistant to brute force attacks
            if (password_verify($password, $user['password'])) {
                // Additional security: Check if password hash needs rehashing
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $rehashStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $rehashStmt->bind_param("si", $newHash, $user['id']);
                    $rehashStmt->execute();
                    $rehashStmt->close();
                }

                // Update last login timestamp
                $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->bind_param("i", $user['id']);
                $updateStmt->execute();
                $updateStmt->close();

                // Set user session
                setUserSession($user);

                // Redirect to appropriate dashboard based on role
                header('Location: ' . BASE_URL . '/' . $user['role'] . '/index.php');
                exit;
            } else {
                // Invalid password - use generic error message to prevent user enumeration
                $error = 'Invalid email or password';

                // Optional: Log failed login attempt for security monitoring
                error_log("Failed login attempt for email: " . $email);
            }
        } else {
            // User not found - use same generic error message
            $error = 'Invalid email or password';
        }

        $stmt->close();
        closeDBConnection($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <style>
        .password-input-group {
            position: relative;
        }

        .password-input {
            padding-right: 45px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: color 0.2s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .password-toggle:focus {
            outline: none;
        }

        .eye-icon {
            display: block;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                    </svg>
                </div>
                <h1 class="login-title"><?php echo APP_NAME; ?></h1>
                <p class="login-subtitle"><?php echo APP_DESCRIPTION; ?></p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-group">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m2 7 10 7 10-7" />
                        </svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="input"
                            placeholder="Enter your email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group password-input-group">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="input password-input"
                            placeholder="Enter your password"
                            minlength="6"
                            required>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                            <!-- Eye Icon (Show) -->
                            <svg class="eye-icon eye-show" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <!-- Eye Off Icon (Hide) -->
                            <svg class="eye-icon eye-hide" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    Sign In
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeShow = document.querySelector('.eye-show');
            const eyeHide = document.querySelector('.eye-hide');

            togglePassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle eye icons
                if (type === 'text') {
                    eyeShow.style.display = 'none';
                    eyeHide.style.display = 'block';
                } else {
                    eyeShow.style.display = 'block';
                    eyeHide.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>