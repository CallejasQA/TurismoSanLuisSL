<?php
class Database {
    private static $pdo = null;
    public static function conexion() {
        if (self::$pdo === null) {
            require_once __DIR__ . '/../config/config.php';
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Error conexión BD: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>