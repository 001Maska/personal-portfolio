<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbHost = '127.0.0.1';
$dbName = 'portfolio';
$dbUser = 'root';
$dbPass = '';
$dbOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
];

function connect_db()
{
    global $dbHost, $dbName, $dbUser, $dbPass, $dbOptions;
    return new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, $dbOptions);
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function requireAdmin(): void
{
    if (empty($_SESSION['admin'])) {
        header('Location: login.php');
        exit;
    }
}
