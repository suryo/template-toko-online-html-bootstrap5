<?php
require_once 'config/koneksi.php';

echo "<h1>Debug Auth</h1>";

// 1. Check Database Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<p>✅ Database connected</p>";

// 2. Check Admin User
$email = 'admin@tokoonline.com';
$stmt = $conn->prepare("SELECT * FROM member WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "<p>✅ Admin user found: " . htmlspecialchars($admin['nama']) . " (" . htmlspecialchars($admin['email']) . ")</p>";
    echo "<p>Role: " . htmlspecialchars($admin['role']) . "</p>";
    
    // Test Password 'password'
    if (password_verify('password', $admin['password'])) {
        echo "<p>✅ Password 'password' is CORRECT</p>";
    } else {
        echo "<p>❌ Password 'password' is INCORRECT</p>";
    }
    
    // Test Password 'admin123'
    if (password_verify('admin123', $admin['password'])) {
        echo "<p>✅ Password 'admin123' is CORRECT</p>";
    } else {
        echo "<p>❌ Password 'admin123' is INCORRECT</p>";
    }
} else {
    echo "<p>❌ Admin user NOT found</p>";
    
    // Create Admin
    $pass = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO member (nama, email, password, role) VALUES ('Admin Toko', 'admin@tokoonline.com', '$pass', 'admin')";
    if ($conn->query($sql)) {
        echo "<p>✅ Created Admin user with password 'admin123'</p>";
    } else {
        echo "<p>❌ Failed to create Admin: " . $conn->error . "</p>";
    }
}

echo "<hr>";

// 3. Check Test Member
$memberEmail = 'andi@example.com';
$stmt = $conn->prepare("SELECT * FROM member WHERE email = ?");
$stmt->bind_param("s", $memberEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $member = $result->fetch_assoc();
    echo "<p>✅ Test Member found: " . htmlspecialchars($member['nama']) . "</p>";
    
    // Test Password 'pass123'
    if (password_verify('pass123', $member['password'])) {
        echo "<p>✅ Password 'pass123' is CORRECT</p>";
    } else {
        echo "<p>❌ Password 'pass123' is INCORRECT</p>";
    }
} else {
    echo "<p>❌ Test Member NOT found</p>";
    
    // Create Member
    $pass = password_hash('pass123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO member (nama, email, password, role) VALUES ('Andi', 'andi@example.com', '$pass', 'member')";
    if ($conn->query($sql)) {
        echo "<p>✅ Created Test Member with password 'pass123'</p>";
    } else {
        echo "<p>❌ Failed to create Test Member: " . $conn->error . "</p>";
    }
}
?>
