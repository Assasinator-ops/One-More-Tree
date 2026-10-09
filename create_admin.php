<?php
/**
 * Create WordPress Admin User
 * Run this script to create a new admin user
 */

$host = 'localhost';
$dbname = 'wordpress';
$username = 'wordpress';
$password = 'wordpress';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Generate WordPress password hash
    $plain_password = 'admin123';
    $password_hash = wp_hash_password($plain_password);

    // Insert user
    $user_login = 'admin';
    $user_email = 'admin@onemoretree.eu';
    $user_nicename = 'Admin';
    $display_name = 'Admin';
    $user_url = '';
    $user_registered = date('Y-m-d H:i:s');
    $user_activation_key = '';
    $user_status = 0;

    $stmt = $pdo->prepare("INSERT INTO omT_users (user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_login, $password_hash, $user_nicename, $user_email, $user_url, $user_registered, $user_activation_key, $user_status, $display_name]);

    $user_id = $pdo->lastInsertId();

    // Assign admin capabilities
    $capabilities = serialize(['administrator' => true]);
    $stmt = $pdo->prepare("INSERT INTO omT_usermeta (user_id, meta_key, meta_value) VALUES (?, 'omT_capabilities', ?)");
    $stmt->execute([$user_id, $capabilities]);

    // Set user level
    $stmt = $pdo->prepare("INSERT INTO omT_usermeta (user_id, meta_key, meta_value) VALUES (?, 'omT_user_level', ?)");
    $stmt->execute([$user_id, 10]);

    echo "Admin user created successfully!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "Email: admin@onemoretree.eu\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// WordPress password hashing function
function wp_hash_password($password) {
    $hash = md5($password . wp_generate_password(20, false));
    return '$P$' . substr($hash, 0, 2) . wp_generate_password(20, false) . substr($hash, 2);
}

function wp_generate_password($length = 12, $special_chars = true) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    if ($special_chars) {
        $chars .= '!@#$%^&*()';
    }
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $password;
}
