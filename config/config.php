<?php
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath) && file_exists(__DIR__ . '/../.env.example')) {
    copy(__DIR__ . '/../.env.example', $envPath);
}
if (!file_exists($envPath)) {
    die('.env no encontrado. Copia .env.example a .env y configura.');
}
$env = parse_ini_file($envPath);
define('DB_HOST', $env['DB_HOST'] ?? '127.0.0.1');
define('DB_NAME', $env['DB_NAME'] ?? 'turismo_sanluis_db');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('APP_URL', $env['APP_URL'] ?? 'http://localhost');
?>