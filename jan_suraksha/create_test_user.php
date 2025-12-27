<?php
require_once __DIR__ . '/config.php';

// Create a test user
$test_name = 'Test User';
$test_mobile = '9876543210';
$test_email = 'test@jansuraksha.com';
$test_password = 'password123';

// Hash the password
$password_hash = password_hash($test_password, PASSWORD_DEFAULT);

// Insert the user
$stmt = $mysqli->prepare('INSERT INTO users (name, mobile, email, password_hash) VALUES (?, ?, ?, ?)');
if (!$stmt) {
    echo "Prepare failed: " . $mysqli->error;
    exit;
}

$stmt->bind_param('ssss', $test_name, $test_mobile, $test_email, $password_hash);

if ($stmt->execute()) {
    echo "Test user created successfully!<br>";
    echo "Email: " . htmlspecialchars($test_email) . "<br>";
    echo "Mobile: " . htmlspecialchars($test_mobile) . "<br>";
    echo "Password: " . htmlspecialchars($test_password) . "<br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    // Check if it's a duplicate entry
    if ($mysqli->errno == 1062) {
        echo "Test user already exists!<br>";
        echo "Email: " . htmlspecialchars($test_email) . "<br>";
        echo "Mobile: " . htmlspecialchars($test_mobile) . "<br>";
        echo "Password: " . htmlspecialchars($test_password) . "<br>";
        echo "<a href='login.php'>Go to Login</a>";
    } else {
        echo "Insert failed: " . $stmt->error;
    }
}
?>
