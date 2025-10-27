<?php
session_start();
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$token = $data['credential'] ?? '';

if (!$token) {
    echo json_encode(["success" => false]);
    exit;
}

// Verify Google ID Token
$client_id = "325178256509-b6t40c9rl7u41m2thdspk84ldog38gp6.apps.googleusercontent.com";
$info = json_decode(file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=" . $token), true);

if (!isset($info['email']) || $info['aud'] !== $client_id) {
    echo json_encode(["success" => false]);
    exit;
}

$email = $info['email'];
$name = $info['name'] ?? explode('@', $email)[0];

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    // Create a new user with a random password hash
    $password_hash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password_hash);
    $stmt->execute();

    $user = [
        'id' => $conn->insert_id,
        'username' => $name,
        'email' => $email
    ];
}

$_SESSION['user'] = $user;
echo json_encode(["success" => true]);
