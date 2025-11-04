<?php
require_once __DIR__ . '/../models/Database.php';

class AlojamientoController {
    public static function obtenerTodos() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM alojamientos WHERE aprobado = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
