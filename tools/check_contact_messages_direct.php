<?php

$envPath = __DIR__ . '/../.env';
$env = [];

foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db   = $env['DB_DATABASE'] ?? 'studybuddy';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';

$pdo = new PDO(
    "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$count = $pdo->query("SELECT COUNT(*) FROM studybuddy_contact_messages")->fetchColumn();
$latest = $pdo->query("SELECT id, name, email, category, subject, status, priority, created_at FROM studybuddy_contact_messages ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

echo "Contact message count: {$count}\n";
echo "Latest message:\n";
print_r($latest);
