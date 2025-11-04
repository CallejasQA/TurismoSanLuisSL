<?php
class Database {
    private static $instance = null;
    public static function getConnection() {
        include __DIR__ . '/../../config/config.php';
        return $pdo;
    }
}
?>
