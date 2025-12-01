<?php
// Database Diagnostic Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Water Refilling System - Database Diagnostic</h1>";
echo "<hr>";

// Test 1: Check database connection
echo "<h2>1. Database Connection Test</h2>";
$conn = new mysqli('localhost', 'root', '', 'water_refilling_system');

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>";
    echo "<p><strong>Fix:</strong> Make sure WAMP is running and the database 'water_refilling_system' exists.</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
}

// Test 2: Check if users table exists
echo "<h2>2. Users Table Check</h2>";
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>❌ Users table does not exist!</p>";
    echo "<p><strong>Fix:</strong> Import the database/schema.sql file in phpMyAdmin</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Users table exists!</p>";
}

// Test 3: Check users in database
echo "<h2>3. Users in Database</h2>";
$result = $conn->query("SELECT id, name, email, role, status FROM users");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>❌ No users found in database!</p>";
    echo "<p><strong>Fix:</strong> Run the following SQL to create admin user:</p>";
    echo "<pre>INSERT INTO users (name, email, password, role, status) VALUES
('Admin User', 'admin@aquaflow.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');</pre>";
} else {
    echo "<p style='color: green;'>✅ Found " . $result->num_rows . " user(s):</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    while ($user = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td>" . $user['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 4: Test password verification
echo "<h2>4. Password Verification Test</h2>";
$email = 'admin@aquaflow.com';
$password = 'password123';

$stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p style='color: red;'>❌ User '$email' not found!</p>";
} else {
    $user = $result->fetch_assoc();
    echo "<p>Testing login for: <strong>$email</strong></p>";
    echo "<p>User status: <strong>" . $user['status'] . "</strong></p>";

    if (password_verify($password, $user['password'])) {
        echo "<p style='color: green;'>✅ Password 'password123' is CORRECT!</p>";
        echo "<p><strong>You should be able to login with:</strong></p>";
        echo "<ul>";
        echo "<li>Email: admin@aquaflow.com</li>";
        echo "<li>Password: password123</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Password 'password123' is INCORRECT!</p>";
        echo "<p><strong>Fix:</strong> Update the password hash by running this SQL:</p>";
        $newHash = password_hash('password123', PASSWORD_DEFAULT);
        echo "<pre>UPDATE users SET password = '$newHash' WHERE email = 'admin@aquaflow.com';</pre>";
    }
}

// Test 5: Session test
echo "<h2>5. Session Test</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✅ Sessions are working!</p>";
} else {
    echo "<p style='color: red;'>❌ Sessions are not working!</p>";
}

// Test 6: Check BASE_URL
echo "<h2>6. Configuration Check</h2>";
require_once __DIR__ . '/config/constants.php';
echo "<p>BASE_URL: <strong>" . BASE_URL . "</strong></p>";
echo "<p>Current URL: <strong>" . $_SERVER['REQUEST_URI'] . "</strong></p>";

$conn->close();

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>If all tests pass above, the login should work. If not, follow the fix instructions.</p>";
echo "<p><a href='auth/login.php'>Go to Login Page</a></p>";
