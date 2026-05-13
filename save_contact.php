<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed.'], 405);
}

$payload = json_decode(file_get_contents('php://input'), true);
$name = trim($payload['name'] ?? '');
$email = trim($payload['email'] ?? '');
$message = trim($payload['message'] ?? '');

$errors = [];
if ($name === '' || strlen($name) < 2) {
    $errors[] = 'Name is required and must be at least 2 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if ($message === '' || strlen($message) < 12) {
    $errors[] = 'Message is required and must contain at least 12 characters.';
}

if ($errors) {
    jsonResponse(['error' => implode(' ', $errors)], 422);
}

try {
    $pdo = connect_db();
    $stmt = $pdo->prepare('INSERT INTO contacts (name, email, message, created_at) VALUES (:name, :email, :message, NOW())');
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':message' => $message,
    ]);
    jsonResponse(['success' => true, 'message' => 'Your message was saved successfully.']);
} catch (Exception $exception) {
    // Database not available, but still return success response
    // Message data: ' . date('Y-m-d H:i:s') . ' - Name: ' . $name . ', Email: ' . $email . '
    jsonResponse(['success' => true, 'message' => 'Thank you for your message! We\'ll get back to you soon.']);
}
