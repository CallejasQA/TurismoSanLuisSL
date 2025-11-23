<?php
require_once __DIR__ . '/Database.php';

class Reserva {
    private $db;

    public function __construct() {
        $this->db = Database::conexion();
    }

    public function crear($alojamientoId, $clienteId, $fechaInicio, $fechaFin, $total) {
        $stmt = $this->db->prepare(
            'INSERT INTO reservas (alojamiento_id, cliente_id, fecha_inicio, fecha_fin, total, estado) VALUES (?,?,?,?,?,?)'
        );
        return $stmt->execute([$alojamientoId, $clienteId, $fechaInicio, $fechaFin, $total, 'pendiente']);
    }

    public function porCliente($clienteId) {
        $sql = "SELECT r.*, a.nombre AS alojamiento, a.ubicacion "
             . "FROM reservas r JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "WHERE r.cliente_id = ? ORDER BY r.creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }
}
?>
