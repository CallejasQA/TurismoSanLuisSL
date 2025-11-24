<?php
require_once __DIR__ . '/../modelos/Sitio.php';
require_once __DIR__ . '/../modelos/Servicio.php';
class AdminController {
    private $modelo;
    private $serviciosModelo;
    private $serviciosBase = [
        'WiFi','Parqueadero','Piscina','Cocina','Baño privado','Agua caliente','Televisión','Aire acondicionado','Ventilador','Desayuno incluido',
        'Recepción 24 horas','Zona BBQ','Jacuzzi','Sauna','Restaurante','Bar','Terraza','Jardín','Servicio de transporte','Permitido fumar (zonas designadas)',
        'Zona de juegos infantiles','Senderos ecológicos','Actividades guiadas','Hamacas','Zona de camping','Mascotas permitidas (con/sin costo)','Cocina compartida'
    ];
    public function __construct() { $this->modelo = new Sitio(); $this->serviciosModelo = new Servicio(); }

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

    public function sliderAlojamiento() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $id = $_GET['id'] ?? null;
        $accion = $_GET['accion'] ?? '';
        if ($id) {
            $activar = $accion === 'agregar';
            $this->modelo->marcarEnSlider($id, $activar);
        }
        header('Location: index.php?ruta=admin/alojamientos'); exit;
    }

    public function servicios() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        $this->serviciosModelo->sembrarInicial($this->serviciosBase);
        $servicios = $this->serviciosModelo->todos();
        require __DIR__ . '/../vistas/admin/servicios.php';
    }

    public function crearServicio() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $nombre = preg_replace('/\s+/', ' ', $nombre);
            if ($nombre !== '' && !$this->serviciosModelo->existeNombre($nombre)) {
                $this->serviciosModelo->crear($nombre);
            }
        }
        header('Location: index.php?ruta=admin/servicios'); exit;
    }

    public function actualizarServicio() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
            $nombre = trim($_POST['nombre'] ?? '');
            $nombre = preg_replace('/\s+/', ' ', $nombre);
            if ($id && $nombre !== '' && $this->serviciosModelo->encontrarPorId($id) && !$this->serviciosModelo->existeNombreEnOtro($nombre, $id)) {
                $this->serviciosModelo->actualizar($id, $nombre);
            }
        }
        header('Location: index.php?ruta=admin/servicios'); exit;
    }

    public function eliminarServicio() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
            if ($id && $this->serviciosModelo->encontrarPorId($id)) {
                $this->serviciosModelo->eliminar($id);
            }
        }
        header('Location: index.php?ruta=admin/servicios'); exit;
    }
}
?>
