<?php
require_once __DIR__ . '/Database.php';

class Sitio {
    private $db;

    public function __construct() {
        $this->db = Database::conexion();
        $this->asegurarTablaServiciosRelacion();
        $this->asegurarColumnaSlider();
    }

    public function obtenerPorPropietario($propietario_id) {
        $sql = "SELECT a.*, GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios "
             . "FROM alojamientos a "
             . "LEFT JOIN alojamiento_servicio als ON als.alojamiento_id = a.id "
             . "LEFT JOIN servicios s ON s.id = als.servicio_id "
             . "WHERE a.propietario_id = ? "
             . "GROUP BY a.id "
             . "ORDER BY a.creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$propietario_id]);
        return array_map([$this, 'adjuntarServicios'], $stmt->fetchAll());
    }

    public function buscarAlojamiento($id) {
        $sql = "SELECT a.*, GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios "
             . "FROM alojamientos a "
             . "LEFT JOIN alojamiento_servicio als ON als.alojamiento_id = a.id "
             . "LEFT JOIN servicios s ON s.id = als.servicio_id "
             . "WHERE a.id = ? GROUP BY a.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->adjuntarServicios($row) : null;
    }

    public function crearAlojamiento($data, array $servicios = []) {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('INSERT INTO alojamientos (propietario_id,nombre,descripcion,ubicacion,precio_noche,rango_precio,imagen,estado) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$data['propietario_id'],$data['nombre'],$data['descripcion'],$data['ubicacion'],$data['precio_noche'],$data['rango_precio'],$data['imagen'],$data['estado']]);
            $alojamientoId = (int) $this->db->lastInsertId();
            $this->sincronizarServicios($alojamientoId, $servicios);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function actualizarAlojamiento($id, $data, ?array $servicios = null) {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('UPDATE alojamientos SET nombre=?, descripcion=?, ubicacion=?, precio_noche=?, rango_precio=?, imagen=?, estado=? WHERE id=?');
            $stmt->execute([$data['nombre'],$data['descripcion'],$data['ubicacion'],$data['precio_noche'],$data['rango_precio'],$data['imagen'],$data['estado'],$id]);
            if ($servicios !== null) {
                $this->sincronizarServicios($id, $servicios);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function eliminarAlojamiento($id) {
        $stmt = $this->db->prepare('DELETE FROM alojamientos WHERE id=?');
        return $stmt->execute([$id]);
    }

    public function alojamientosPublicos(array $filtros = []) {
        $condiciones = ["a.estado IN ('aprobado','activo')"];
        $having = [];
        $params = [];
        $havingParams = [];

        if (!empty($filtros['ubicacion'])) {
            $condiciones[] = 'a.ubicacion LIKE ?';
            $params[] = '%' . $filtros['ubicacion'] . '%';
        }

        if (!empty($filtros['operador'])) {
            $condiciones[] = 'COALESCE(af.nombre_negocio, u.nombre) LIKE ?';
            $params[] = '%' . $filtros['operador'] . '%';
        }

        if (!empty($filtros['min_estrellas'])) {
            $having[] = 'AVG(v.estrellas) >= ?';
            $havingParams[] = max(1, min(5, (int) $filtros['min_estrellas']));
        }

        $sql = "SELECT a.id, a.nombre, a.descripcion, a.ubicacion, a.precio_noche, a.rango_precio, a.imagen, a.estado, a.destacado_slider, "
             . "COALESCE(af.nombre_negocio, u.nombre) AS nombre_negocio, "
             . "AVG(v.estrellas) AS promedio_estrellas, "
             . "COUNT(v.id) AS total_valoraciones, "
             . "GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios "
             . "FROM alojamientos a "
             . "JOIN usuarios u ON u.id = a.propietario_id "
             . "LEFT JOIN afiliados af ON af.usuario_id = a.propietario_id AND af.estado = 'aprobado' "
             . "LEFT JOIN alojamiento_servicio als ON als.alojamiento_id = a.id "
             . "LEFT JOIN servicios s ON s.id = als.servicio_id "
             . "LEFT JOIN valoraciones v ON v.alojamiento_id = a.id "
             . "WHERE " . implode(' AND ', $condiciones) . " "
             . "GROUP BY a.id "
             . (!empty($having) ? 'HAVING ' . implode(' AND ', $having) . ' ' : '')
             . "ORDER BY a.creado_en DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge($params, $havingParams));
        return array_map([$this, 'adjuntarServicios'], $stmt->fetchAll());
    }

    public function marcarEnSlider($id, $activo) {
        $stmt = $this->db->prepare("UPDATE alojamientos SET destacado_slider = ? WHERE id = ? AND estado IN ('aprobado','activo')");
        $stmt->execute([(int) $activo, $id]);
        return $stmt->rowCount() > 0;
    }

    public function alojamientosSlider() {
        $sql = "SELECT a.id, a.nombre, a.descripcion, a.ubicacion, a.precio_noche, a.imagen, a.estado, "
             . "COALESCE(af.nombre_negocio, u.nombre) AS nombre_negocio "
             . "FROM alojamientos a "
             . "JOIN usuarios u ON u.id = a.propietario_id "
             . "LEFT JOIN afiliados af ON af.usuario_id = a.propietario_id AND af.estado = 'aprobado' "
             . "WHERE a.estado IN ('aprobado','activo') AND a.destacado_slider = 1 "
             . "ORDER BY a.creado_en DESC LIMIT 10";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function alojamientoPublicado($id) {
        $sql = "SELECT a.id, a.nombre, a.descripcion, a.ubicacion, a.precio_noche, a.rango_precio, a.imagen, a.estado, "
             . "COALESCE(af.nombre_negocio, u.nombre) AS nombre_negocio, "
             . "GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios "
             . "FROM alojamientos a "
             . "JOIN usuarios u ON u.id = a.propietario_id "
             . "LEFT JOIN afiliados af ON af.usuario_id = a.propietario_id AND af.estado = 'aprobado' "
             . "LEFT JOIN alojamiento_servicio als ON als.alojamiento_id = a.id "
             . "LEFT JOIN servicios s ON s.id = als.servicio_id "
             . "WHERE a.id = ? AND a.estado IN ('aprobado','activo') "
             . "GROUP BY a.id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->adjuntarServicios($row) : null;
    }

    public function alojamientosParaRevision() {
        $sql = "SELECT a.*, u.email AS correo_propietario, GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios  "
             . "FROM alojamientos a "
             . "JOIN usuarios u ON u.id = a.propietario_id "
             . "LEFT JOIN alojamiento_servicio als ON als.alojamiento_id = a.id "
             . "LEFT JOIN servicios s ON s.id = als.servicio_id "
             . "GROUP BY a.id "
             . "ORDER BY a.creado_en DESC";
        $stmt = $this->db->query($sql);
        return array_map([$this, 'adjuntarServicios'], $stmt->fetchAll());
    }

    public function cambiarEstadoAlojamiento($id, $estado) {
        $stmt = $this->db->prepare('UPDATE alojamientos SET estado = ? WHERE id = ?');
        return $stmt->execute([$estado, $id]);
    }

    public function afiliacionesPendientes() {
        $stmt = $this->db->query("SELECT a.*, u.email as correo_propietario FROM afiliados a JOIN usuarios u ON u.id = a.usuario_id WHERE a.estado='pendiente' ORDER BY a.solicitado_en DESC");
        return $stmt->fetchAll();
    }

    public function cambiarEstadoAfiliacion($id,$estado) {
        $stmt = $this->db->prepare('UPDATE afiliados SET estado=? WHERE id=?');
        return $stmt->execute([$estado,$id]);
    }

    public function serviciosIds($alojamientoId) {
        $stmt = $this->db->prepare('SELECT servicio_id FROM alojamiento_servicio WHERE alojamiento_id = ?');
        $stmt->execute([$alojamientoId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function sincronizarServicios($alojamientoId, array $servicios) {
        $ids = array_unique(array_filter(array_map('intval', $servicios), fn($id) => $id > 0));
        $this->db->prepare('DELETE FROM alojamiento_servicio WHERE alojamiento_id = ?')->execute([$alojamientoId]);
        if (empty($ids)) { return; }
        $stmt = $this->db->prepare('INSERT IGNORE INTO alojamiento_servicio (alojamiento_id, servicio_id) VALUES (?, ?)');
        foreach ($ids as $id) {
            $stmt->execute([$alojamientoId, $id]);
        }
    }

    private function adjuntarServicios($row) {
        $row['servicios'] = $this->parseServiciosCadena($row['servicios'] ?? '');
        return $row;
    }

    private function parseServiciosCadena($cadena) {
        if (empty($cadena)) { return []; }
        $valores = array_filter(array_map('trim', explode('||', $cadena)));
        return array_values($valores);
    }

    private function asegurarTablaServiciosRelacion() {
        $stmt = $this->db->query("SHOW TABLES LIKE 'alojamiento_servicio'");
        if ($stmt->fetch()) { return; }

        $sql = "CREATE TABLE IF NOT EXISTS alojamiento_servicio ("
             . "id INT AUTO_INCREMENT PRIMARY KEY,"
             . "alojamiento_id INT NOT NULL,"
             . "servicio_id INT NOT NULL,"
             . "UNIQUE KEY uniq_aloj_serv (alojamiento_id, servicio_id),"
             . "KEY idx_servicio_id (servicio_id),"
             . "CONSTRAINT fk_aloj_serv_aloj FOREIGN KEY (alojamiento_id) REFERENCES alojamientos(id) ON DELETE CASCADE,"
             . "CONSTRAINT fk_aloj_serv_serv FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE"
             . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->exec($sql);
    }

    private function asegurarColumnaSlider() {
        $stmt = $this->db->query("SHOW COLUMNS FROM alojamientos LIKE 'destacado_slider'");
        if ($stmt->fetch()) { return; }
        $this->db->exec("ALTER TABLE alojamientos ADD COLUMN destacado_slider TINYINT(1) NOT NULL DEFAULT 0 AFTER imagen");
    }
}
?>
