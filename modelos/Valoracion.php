<?php
require_once __DIR__ . '/Database.php';

class Valoracion {
    private $db;

    public function __construct() {
        $this->db = Database::conexion();
        $this->asegurarTablaValoraciones();
    }

    public function crear($reservaId, $alojamientoId, $clienteId, $estrellas, $comentario) {
        $stmt = $this->db->prepare(
            'INSERT INTO valoraciones (reserva_id, alojamiento_id, cliente_id, estrellas, comentario) VALUES (?,?,?,?,?)'
        );
        return $stmt->execute([(int) $reservaId, (int) $alojamientoId, (int) $clienteId, (int) $estrellas, $comentario]);
    }

    public function porClienteYAlojamiento($clienteId, $alojamientoId) {
        $stmt = $this->db->prepare('SELECT * FROM valoraciones WHERE cliente_id = ? AND alojamiento_id = ? LIMIT 1');
        $stmt->execute([(int) $clienteId, (int) $alojamientoId]);
        return $stmt->fetch();
    }

    public function existeParaReserva($reservaId, $clienteId) {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM valoraciones WHERE reserva_id = ? AND cliente_id = ?');
        $stmt->execute([(int) $reservaId, (int) $clienteId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function promedioYConteo($alojamientoId) {
        $stmt = $this->db->prepare('SELECT AVG(estrellas) AS promedio, COUNT(*) AS total FROM valoraciones WHERE alojamiento_id = ?');
        $stmt->execute([(int) $alojamientoId]);
        $row = $stmt->fetch();
        return [
            'promedio' => $row && $row['promedio'] !== null ? round((float) $row['promedio'], 1) : 0,
            'total' => $row ? (int) $row['total'] : 0,
        ];
    }

    private function asegurarTablaValoraciones() {
        $stmt = $this->db->query("SHOW TABLES LIKE 'valoraciones'");
        if ($stmt->fetch()) { return; }

        $sql = "CREATE TABLE IF NOT EXISTS valoraciones ("
             . "id INT AUTO_INCREMENT PRIMARY KEY,"
             . "reserva_id INT NOT NULL,"
             . "alojamiento_id INT NOT NULL,"
             . "cliente_id INT NOT NULL,"
             . "estrellas TINYINT NOT NULL CHECK (estrellas BETWEEN 1 AND 5),"
             . "comentario TEXT NULL,"
             . "creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
             . "UNIQUE KEY uniq_reserva (reserva_id),"
             . "KEY idx_alojamiento (alojamiento_id),"
             . "KEY idx_cliente (cliente_id),"
             . "CONSTRAINT fk_val_reserva FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,"
             . "CONSTRAINT fk_val_aloj FOREIGN KEY (alojamiento_id) REFERENCES alojamientos(id) ON DELETE CASCADE,"
             . "CONSTRAINT fk_val_cliente FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE"
             . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->exec($sql);
    }
}
?>
