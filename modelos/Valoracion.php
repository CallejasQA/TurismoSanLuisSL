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

    public function listar(array $filtros = [], $propietarioId = null) {
        $condiciones = ['1=1'];
        $params = [];

        if ($propietarioId) {
            $condiciones[] = 'a.propietario_id = ?';
            $params[] = (int) $propietarioId;
        }

        if (!empty($filtros['propietario_id'])) {
            $condiciones[] = 'a.propietario_id = ?';
            $params[] = (int) $filtros['propietario_id'];
        }
        if (!empty($filtros['alojamiento_id'])) {
            $condiciones[] = 'v.alojamiento_id = ?';
            $params[] = (int) $filtros['alojamiento_id'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $condiciones[] = 'DATE(v.creado_en) >= ?';
            $params[] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $condiciones[] = 'DATE(v.creado_en) <= ?';
            $params[] = $filtros['fecha_hasta'];
        }

        $sql = "SELECT v.*, a.nombre AS alojamiento_nombre, a.propietario_id, up.email AS propietario_email, up.nombre AS propi"
             . "etario_nombre, cu.email AS cliente_email, COALESCE(cp.primer_nombre, cu.nombre) AS cliente_nombre, cp.primer_ape"
             . "llido AS cliente_apellido "
             . "FROM valoraciones v "
             . "JOIN alojamientos a ON a.id = v.alojamiento_id "
             . "JOIN usuarios up ON up.id = a.propietario_id "
             . "JOIN usuarios cu ON cu.id = v.cliente_id "
             . "LEFT JOIN clientes_perfiles cp ON cp.usuario_id = cu.id "
             . "WHERE " . implode(' AND ', $condiciones)
             . " ORDER BY v.creado_en DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function propietariosConValoraciones() {
        $sql = "SELECT DISTINCT a.propietario_id AS id, u.email FROM valoraciones v "
             . "JOIN alojamientos a ON a.id = v.alojamiento_id "
             . "JOIN usuarios u ON u.id = a.propietario_id "
             . "ORDER BY u.email";
        return $this->db->query($sql)->fetchAll();
    }

    public function alojamientosConValoraciones($propietarioId = null) {
        $condicion = '';
        $params = [];
        if ($propietarioId) {
            $condicion = 'WHERE a.propietario_id = ?';
            $params[] = (int) $propietarioId;
        }
        $sql = "SELECT DISTINCT a.id, a.nombre FROM valoraciones v "
             . "JOIN alojamientos a ON a.id = v.alojamiento_id "
             . $condicion
             . " ORDER BY a.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function actualizarComentario($id, $comentario, $propietarioId = null) {
        $filtro = '';
        $params = [$comentario, (int) $id];
        if ($propietarioId) {
            $filtro = ' AND a.propietario_id = ?';
            $params[] = (int) $propietarioId;
        }
        $sql = "UPDATE valoraciones v JOIN alojamientos a ON a.id = v.alojamiento_id "
             . "SET v.comentario = ? WHERE v.id = ?" . $filtro . " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function eliminar($id, $propietarioId = null) {
        $params = [(int) $id];
        $sql = 'DELETE FROM valoraciones WHERE id = ?';

        if ($propietarioId) {
            $sql .= ' AND EXISTS (SELECT 1 FROM alojamientos a WHERE a.id = valoraciones.alojamiento_id AND a.propietario_id = ?)';
            $params[] = (int) $propietarioId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
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
