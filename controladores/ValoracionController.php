<?php
require_once __DIR__ . '/../modelos/Valoracion.php';

class ValoracionController {
    private $valoracionModelo;

    public function __construct() {
        $this->valoracionModelo = new Valoracion();
    }

    public function adminIndex() {
        $this->asegurarRol('admin');
        $filtros = $this->leerFiltros();
        $propietarioSeleccionado = $filtros['propietario_id'] ?? null;
        $valoraciones = $this->valoracionModelo->listar($filtros);
        $propietarios = $this->valoracionModelo->propietariosConValoraciones();
        $alojamientos = $this->valoracionModelo->alojamientosConValoraciones($propietarioSeleccionado);
        $esAdmin = true;
        require __DIR__ . '/../vistas/admin/valoraciones.php';
    }

    public function propietarioIndex() {
        $this->asegurarRol('propietario');
        $propietarioId = (int) $_SESSION['usuario_id'];
        $filtros = $this->leerFiltros();
        $valoraciones = $this->valoracionModelo->listar($filtros, $propietarioId);
        $propietarios = [];
        $alojamientos = $this->valoracionModelo->alojamientosConValoraciones($propietarioId);
        $esAdmin = false;
        require __DIR__ . '/../vistas/propietario/valoraciones.php';
    }

    public function actualizar() {
        $rol = $_SESSION['usuario_rol'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !in_array($rol, ['admin','propietario'], true)) { header('Location: index.php'); exit; }
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        $comentario = trim($_POST['comentario'] ?? '');
        $comentario = substr($comentario, 0, 1000);
        $propietarioId = $rol === 'propietario' ? (int) $_SESSION['usuario_id'] : null;
        if ($id && $comentario !== '') {
            $this->valoracionModelo->actualizarComentario($id, $comentario, $propietarioId);
        }
        $destino = $rol === 'admin' ? 'admin/valoraciones' : 'propietario/valoraciones';
        header('Location: index.php?ruta=' . $destino);
        exit;
    }

    public function eliminar() {
        $rol = $_SESSION['usuario_rol'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !in_array($rol, ['admin','propietario'], true)) { header('Location: index.php'); exit; }
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        $propietarioId = $rol === 'propietario' ? (int) $_SESSION['usuario_id'] : null;
        if ($id) {
            $this->valoracionModelo->eliminar($id, $propietarioId);
        }
        $destino = $rol === 'admin' ? 'admin/valoraciones' : 'propietario/valoraciones';
        header('Location: index.php?ruta=' . $destino);
        exit;
    }

    private function leerFiltros() {
        $filtros = [];
        $filtros['propietario_id'] = isset($_GET['propietario_id']) ? (int) $_GET['propietario_id'] : null;
        $filtros['alojamiento_id'] = isset($_GET['alojamiento_id']) ? (int) $_GET['alojamiento_id'] : null;
        $filtros['fecha_desde'] = $_GET['fecha_desde'] ?? '';
        $filtros['fecha_hasta'] = $_GET['fecha_hasta'] ?? '';
        return $filtros;
    }

    private function asegurarRol($rol) {
        if (($_SESSION['usuario_rol'] ?? '') !== $rol) { header('Location: index.php?ruta=auth/login'); exit; }
    }
}
?>
