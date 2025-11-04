<?php
$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die("❌ No se encontró el archivo .env en: $envPath");
}

$env = parse_ini_file($envPath, true);

define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    die("❌ Error al conectar con la base de datos: " . $e->getMessage());
}
?>


