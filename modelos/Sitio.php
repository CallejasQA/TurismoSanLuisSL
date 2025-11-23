<?php
require_once __DIR__ . '/Database.php';

class Sitio {
    private $db;

    public function __construct() {
        $this->db = Database::conexion();
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

    public function alojamientosPublicos() {
        $sql = "SELECT a.id, a.nombre, a.descripcion, a.ubicacion, a.precio_noche, a.rango_precio, a.imagen, a.estado, "
             . "GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios "
             . "FROM alojamientos a "
             . "LEFT JOIN alojamiento_servicio als ON als.alojamiento_id = a.id "
             . "LEFT JOIN servicios s ON s.id = als.servicio_id "
             . "WHERE a.estado IN ('aprobado','activo') "
             . "GROUP BY a.id "
             . "ORDER BY a.creado_en DESC";
        $stmt = $this->db->query($sql);
        return array_map([$this, 'adjuntarServicios'], $stmt->fetchAll());
    }

    public function alojamientoPublicado($id) {
        $sql = "SELECT a.id, a.nombre, a.descripcion, a.ubicacion, a.precio_noche, a.rango_precio, a.imagen, a.estado, "
             . "GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios "
             . "FROM alojamientos a "
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
        $sql = "SELECT a.*, u.email AS correo_propietario, GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '||') AS servicios "
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
}
?>
