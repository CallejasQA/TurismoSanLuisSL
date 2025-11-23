<?php
require_once __DIR__ . '/../modelos/Sitio.php';
class AdminController {
    private $modelo;
    public function __construct() { $this->modelo = new Sitio(); }

    public function lista_afiliaciones() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $pendientes = $this->modelo->afiliacionesPendientes();
        require __DIR__ . '/../vistas/admin/afiliaciones.php';
    }

    public function alojamientos() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $alojamientos = $this->modelo->alojamientosParaRevision();
        require __DIR__ . '/../vistas/admin/alojamientos.php';
    }

    public function aprobar() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $id = $_GET['id'] ?? null;
        if ($id) $this->modelo->cambiarEstadoAfiliacion($id,'aprobado');
        header('Location: index.php?ruta=admin/afiliaciones'); exit;
    }

    public function rechazar() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $id = $_GET['id'] ?? null;
        if ($id) $this->modelo->cambiarEstadoAfiliacion($id,'rechazado');
        header('Location: index.php?ruta=admin/afiliaciones'); exit;
    }

    public function aprobarAlojamiento() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $id = $_GET['id'] ?? null;
        if ($id) $this->modelo->cambiarEstadoAlojamiento($id,'aprobado');
        header('Location: index.php?ruta=admin/alojamientos'); exit;
    }

    public function rechazarAlojamiento() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $id = $_GET['id'] ?? null;
        if ($id) $this->modelo->cambiarEstadoAlojamiento($id,'rechazado');
        header('Location: index.php?ruta=admin/alojamientos'); exit;
    }

    public function activarAlojamiento() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $id = $_GET['id'] ?? null;
        if ($id) $this->modelo->cambiarEstadoAlojamiento($id,'activo');
        header('Location: index.php?ruta=admin/alojamientos'); exit;
    }

    public function desactivarAlojamiento() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $id = $_GET['id'] ?? null;
        if ($id) $this->modelo->cambiarEstadoAlojamiento($id,'inactivo');
        header('Location: index.php?ruta=admin/alojamientos'); exit;
    }
}
?>
