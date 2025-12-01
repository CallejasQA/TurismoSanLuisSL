<?php
require_once __DIR__ . '/Database.php';

class Reserva {
    private $db;
    private $estadosPermitidos = ['pendiente','confirmada','cancelada','finalizada'];

    public function __construct() {
        $this->db = Database::conexion();
        $this->asegurarTablaReservas();
    }

    public function crear($alojamientoId, $clienteId, $fechaInicio, $fechaFin, $total) {
        $stmt = $this->db->prepare(
            'INSERT INTO reservas (alojamiento_id, cliente_id, fecha_inicio, fecha_fin, total, estado) VALUES (?,?,?,?,?,?)'
        );
        return $stmt->execute([$alojamientoId, $clienteId, $fechaInicio, $fechaFin, $total, 'pendiente']);
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare('SELECT * FROM reservas WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $id]);
        return $stmt->fetch();
    }

    public function porCliente($clienteId) {
        $sql = "SELECT r.*, a.nombre AS alojamiento, a.ubicacion "
             . "FROM reservas r JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "WHERE r.cliente_id = ? ORDER BY r.creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }

    public function listarParaPropietario($propietarioId, $mes = '') {
        [$inicio, $fin] = $this->rangoMes($mes);
        $params = [$propietarioId];
        $filtroMes = '';
        if ($inicio && $fin) {
            $filtroMes = ' AND r.fecha_inicio BETWEEN ? AND ?';
            $params[] = $inicio;
            $params[] = $fin;
        }

        $sql = "SELECT r.*, a.nombre AS alojamiento, a.ubicacion, cu.email AS cliente_email, "
             . "COALESCE(cp.primer_nombre, cu.nombre) AS cliente_nombre, cp.primer_apellido AS cliente_apellido "
             . "FROM reservas r "
             . "JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "JOIN usuarios cu ON cu.id = r.cliente_id "
             . "LEFT JOIN clientes_perfiles cp ON cp.usuario_id = cu.id "
             . "WHERE a.propietario_id = ?" . $filtroMes
             . " ORDER BY r.fecha_inicio DESC, r.creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarAgendaPropietario($propietarioId, $mes = '', $alojamientoId = null) {
        [$inicio, $fin] = $this->rangoMes($mes);
        $params = [$propietarioId];
        $filtros = '';

        if ($alojamientoId) {
            $filtros .= ' AND a.id = ?';
            $params[] = $alojamientoId;
        }

        if ($inicio && $fin) {
            $filtros .= ' AND r.fecha_inicio <= ? AND r.fecha_fin >= ?';
            $params[] = $fin;
            $params[] = $inicio;
        }

        $sql = "SELECT r.*, a.nombre AS alojamiento, a.ubicacion, a.id AS alojamiento_id, cu.email AS cliente_email, "
             . "COALESCE(cp.primer_nombre, cu.nombre) AS cliente_nombre, cp.primer_apellido AS cliente_apellido "
             . "FROM reservas r "
             . "JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "JOIN usuarios cu ON cu.id = r.cliente_id "
             . "LEFT JOIN clientes_perfiles cp ON cp.usuario_id = cu.id "
             . "WHERE a.propietario_id = ?" . $filtros
             . " ORDER BY r.fecha_inicio ASC, r.creado_en DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarParaAdmin($mes = '', $propietarioId = null) {
        [$inicio, $fin] = $this->rangoMes($mes);
        $params = [];
        $filtro = '';
        if ($propietarioId) {
            $filtro .= ' AND a.propietario_id = ?';
            $params[] = $propietarioId;
        }
        if ($inicio && $fin) {
            $filtro .= ' AND r.fecha_inicio BETWEEN ? AND ?';
            $params[] = $inicio;
            $params[] = $fin;
        }

        $sql = "SELECT r.*, a.nombre AS alojamiento, a.ubicacion, a.propietario_id, ap.email AS propietario_email, "
             . "cu.email AS cliente_email, COALESCE(cp.primer_nombre, cu.nombre) AS cliente_nombre, cp.primer_apellido AS cliente_apellido "
             . "FROM reservas r "
             . "JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "JOIN usuarios ap ON ap.id = a.propietario_id "
             . "JOIN usuarios cu ON cu.id = r.cliente_id "
             . "LEFT JOIN clientes_perfiles cp ON cp.usuario_id = cu.id "
             . "WHERE 1=1" . $filtro
             . " ORDER BY r.fecha_inicio DESC, r.creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarAgendaAdmin($mes = '', $propietarioId = null) {
        [$inicio, $fin] = $this->rangoMes($mes);
        $params = [];
        $filtro = '';

        if ($propietarioId) {
            $filtro .= ' AND a.propietario_id = ?';
            $params[] = $propietarioId;
        }

        if ($inicio && $fin) {
            $filtro .= ' AND r.fecha_inicio <= ? AND r.fecha_fin >= ?';
            $params[] = $fin;
            $params[] = $inicio;
        }

        $sql = "SELECT r.*, a.nombre AS alojamiento, a.ubicacion, a.id AS alojamiento_id, a.propietario_id, ap.email AS propietario_email, "
             . "cu.email AS cliente_email, COALESCE(cp.primer_nombre, cu.nombre) AS cliente_nombre, cp.primer_apellido AS cliente_apellido "
             . "FROM reservas r "
             . "JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "JOIN usuarios ap ON ap.id = a.propietario_id "
             . "JOIN usuarios cu ON cu.id = r.cliente_id "
             . "LEFT JOIN clientes_perfiles cp ON cp.usuario_id = cu.id "
             . "WHERE 1=1" . $filtro
             . " ORDER BY r.fecha_inicio ASC, r.creado_en DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function cambiarEstado($id, $estado, $propietarioId = null) {
        $estado = strtolower(trim($estado));
        $estado = in_array($estado, $this->estadosPermitidos, true) ? $estado : 'pendiente';
        $params = [$estado, $id];
        $filtro = '';
        if ($propietarioId) {
            $filtro = ' AND a.propietario_id = ?';
            $params[] = $propietarioId;
        }
        $sql = "UPDATE reservas r JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "SET r.estado = ? WHERE r.id = ?" . $filtro . " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function clienteConReservaFinalizada($alojamientoId, $clienteId) {
        $sql = "SELECT * FROM reservas WHERE alojamiento_id = ? AND cliente_id = ? AND estado = 'finalizada' "
             . "ORDER BY fecha_fin DESC, creado_en DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $alojamientoId, (int) $clienteId]);
        return $stmt->fetch();
    }

    public function propietariosConReservas() {
        $sql = "SELECT DISTINCT a.propietario_id AS id, u.email "
             . "FROM reservas r JOIN alojamientos a ON a.id = r.alojamiento_id "
             . "JOIN usuarios u ON u.id = a.propietario_id "
             . "ORDER BY u.email";
        return $this->db->query($sql)->fetchAll();
    }

    public function alojamientosDePropietario(int $propietarioId): array {
        $stmt = $this->db->prepare("SELECT id, nombre FROM alojamientos WHERE propietario_id = ? ORDER BY nombre");
        $stmt->execute([$propietarioId]);
        return $stmt->fetchAll();
    }

    public function rangoMes($mes) {
        if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
            return [null, null];
        }
        $inicio = $mes . '-01';
        $fin = date('Y-m-t', strtotime($inicio));
        return [$inicio, $fin];
    }

    private function asegurarTablaReservas() {
        $stmt = $this->db->query("SHOW TABLES LIKE 'reservas'");
        if (!$stmt->fetch()) {
            $sql = "CREATE TABLE IF NOT EXISTS reservas ("
                 . "id INT AUTO_INCREMENT PRIMARY KEY,"
                 . "alojamiento_id INT NOT NULL,"
                 . "cliente_id INT NOT NULL,"
                 . "fecha_inicio DATE NOT NULL,"
                 . "fecha_fin DATE NOT NULL,"
                 . "total DECIMAL(12,2) NOT NULL,"
                 . "estado ENUM('pendiente','confirmada','cancelada','finalizada') DEFAULT 'pendiente',"
                 . "creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
                 . "KEY idx_alojamiento (alojamiento_id),"
                 . "KEY idx_cliente (cliente_id),"
                 . "KEY idx_fecha_inicio (fecha_inicio),"
                 . "CONSTRAINT fk_reserva_aloj FOREIGN KEY (alojamiento_id) REFERENCES alojamientos(id) ON DELETE CASCADE,"
                 . "CONSTRAINT fk_reserva_cliente FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE"
                 . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

            $this->db->exec($sql);
            return;
        }

        $columnaEstado = $this->db->query("SHOW COLUMNS FROM reservas LIKE 'estado'")->fetch();
        if ($columnaEstado && isset($columnaEstado['Type'])) {
            $tipo = strtolower($columnaEstado['Type']);
            $faltantes = array_filter($this->estadosPermitidos, function ($estado) use ($tipo) {
                return strpos($tipo, "'{$estado}'") === false;
            });

            if (!empty($faltantes)) {
                $enumLista = "'" . implode("','", $this->estadosPermitidos) . "'";
                $sql = "ALTER TABLE reservas MODIFY estado ENUM({$enumLista}) DEFAULT 'pendiente'";
                $this->db->exec($sql);
            }
        }
    }
}

?>
