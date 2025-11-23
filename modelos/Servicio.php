<?php
require_once __DIR__ . '/Database.php';

class Servicio {
    private $db;
    public function __construct() {
        $this->db = Database::conexion();
    }

    public function todos() {
        $stmt = $this->db->query('SELECT * FROM servicios ORDER BY nombre');
        return $stmt->fetchAll();
    }

    public function crear($nombre) {
        $stmt = $this->db->prepare('INSERT INTO servicios (nombre) VALUES (?)');
        return $stmt->execute([$nombre]);
    }

    public function encontrarPorId($id) {
        $stmt = $this->db->prepare('SELECT * FROM servicios WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function actualizar($id, $nombre) {
        $stmt = $this->db->prepare('UPDATE servicios SET nombre = ? WHERE id = ?');
        return $stmt->execute([$nombre, $id]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare('DELETE FROM servicios WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function existeNombre($nombre) {
        $stmt = $this->db->prepare('SELECT id FROM servicios WHERE LOWER(nombre) = LOWER(?) LIMIT 1');
        $stmt->execute([$nombre]);
        return (bool) $stmt->fetchColumn();
    }

    public function existeNombreEnOtro($nombre, $excluirId) {
        $stmt = $this->db->prepare('SELECT id FROM servicios WHERE LOWER(nombre) = LOWER(?) AND id != ? LIMIT 1');
        $stmt->execute([$nombre, $excluirId]);
        return (bool) $stmt->fetchColumn();
    }

    public function sembrarInicial(array $servicios) {
        foreach ($servicios as $nombre) {
            if (!$this->existeNombre($nombre)) {
                $this->crear($nombre);
            }
        }
    }
}
?>
