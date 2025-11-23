<?php
require_once __DIR__ . '/Database.php';
class Sitio {
    private $db;
    public function __construct() {
        $this->db = Database::conexion();
    }
    public function obtenerPorPropietario($propietario_id) {
        $stmt = $this->db->prepare('SELECT * FROM alojamientos WHERE propietario_id = ? ORDER BY creado_en DESC');
        $stmt->execute([$propietario_id]);
        return $stmt->fetchAll();
    }
    public function buscarAlojamiento($id) {
        $stmt = $this->db->prepare('SELECT * FROM alojamientos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function crearAlojamiento($data) {
        $stmt = $this->db->prepare('INSERT INTO alojamientos (propietario_id,nombre,descripcion,ubicacion,precio_noche,rango_precio,imagen,estado) VALUES (?,?,?,?,?,?,?,?)');
        return $stmt->execute([$data['propietario_id'],$data['nombre'],$data['descripcion'],$data['ubicacion'],$data['precio_noche'],$data['rango_precio'],$data['imagen'],$data['estado']]);
    }
    public function actualizarAlojamiento($id,$data) {
        $stmt = $this->db->prepare('UPDATE alojamientos SET nombre=?, descripcion=?, ubicacion=?, precio_noche=?, rango_precio=?, imagen=?, estado=? WHERE id=?');
        return $stmt->execute([$data['nombre'],$data['descripcion'],$data['ubicacion'],$data['precio_noche'],$data['rango_precio'],$data['imagen'],$data['estado'],$id]);
    }
    public function eliminarAlojamiento($id) {
        $stmt = $this->db->prepare('DELETE FROM alojamientos WHERE id=?');
        return $stmt->execute([$id]);
    }

    public function alojamientosPublicos() {
        $stmt = $this->db->query("SELECT id, nombre, descripcion, ubicacion, precio_noche, rango_precio, imagen, estado FROM alojamientos WHERE estado IN ('aprobado','activo') ORDER BY creado_en DESC");
        return $stmt->fetchAll();
    }

    public function alojamientoPublicado($id) {
        $stmt = $this->db->prepare("SELECT id, nombre, descripcion, ubicacion, precio_noche, rango_precio, imagen, estado FROM alojamientos WHERE id = ? AND estado IN ('aprobado','activo') LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function alojamientosParaRevision() {
        $sql = "SELECT a.*, u.email AS correo_propietario FROM alojamientos a "
             . "JOIN usuarios u ON u.id = a.propietario_id "
             . "ORDER BY a.creado_en DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
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
}
?>
