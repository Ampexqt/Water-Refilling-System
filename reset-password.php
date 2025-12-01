<?php
// Password Reset Utility
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

echo "<h1>Password Reset Utility</h1>";
echo "<hr>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $newPassword = $_POST['password'];

    $conn = getDBConnection();

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ User with email '$email' not found!</p>";
    } else {
        $user = $result->fetch_assoc();

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $hashedPassword, $email);

        if ($updateStmt->execute()) {
            echo "<p style='color: green;'>✅ Password updated successfully for " . $user['name'] . "!</p>";
            echo "<p><strong>New credentials:</strong></p>";
            echo "<ul>";
            echo "<li>Email: $email</li>";
            echo "<li>Password: $newPassword</li>";
            echo "</ul>";
            echo "<p><a href='auth/login.php'>Go to Login Page</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Error updating password: " . $conn->error . "</p>";
        }

        $updateStmt->close();
    }

    $stmt->close();
    $conn->close();
} else {
?>
    <form method="POST" action="">
        <h2>Reset User Password</h2>
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="admin@aquaflow.com" required style="width: 300px; padding: 8px;">
        </p>
        <p>
            <label>New Password:</label><br>
            <input type="text" name="password" value="password123" required style="width: 300px; padding: 8px;">
        </p>
        <p>
            <button type="submit" style="padding: 10px 20px; background: #7FA489; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Reset Password
            </button>
        </p>
    </form>

    <hr>
    <h3>Quick Actions:</h3>
    <ul>
        <li><a href="diagnostic.php">Run Diagnostic Test</a></li>
        <li><a href="auth/login.php">Go to Login Page</a></li>
    </ul>
<?php
}
?>