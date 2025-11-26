<?php
require_once __DIR__ . '/../modelos/Sitio.php';
require_once __DIR__ . '/../modelos/Servicio.php';
require_once __DIR__ . '/../modelos/Reserva.php';
require_once __DIR__ . '/../modelos/Valoracion.php';

class SitioController {
    private $modelo;
    private $serviciosModelo;
    private $reservaModelo;
    private $valoracionModelo;

    public function __construct() {
        $this->modelo = new Sitio();
        $this->serviciosModelo = new Servicio();
        $this->reservaModelo = new Reserva();
        $this->valoracionModelo = new Valoracion();
    }

    public function inicio() {
        $filtros = [
            'ubicacion' => trim($_GET['ubicacion'] ?? ''),
            'operador' => trim($_GET['operador'] ?? ''),
            'min_estrellas' => isset($_GET['estrellas']) ? (int) $_GET['estrellas'] : null,
        ];

        $aplicaFiltros = $filtros['ubicacion'] !== '' || $filtros['operador'] !== '' || $filtros['min_estrellas'] !== null;

        $sitiosBase = $this->modelo->alojamientosPublicos();
        $sitios = $aplicaFiltros ? $this->modelo->alojamientosPublicos($filtros) : $sitiosBase;
        $recomendados = array_slice($sitiosBase, 0, 4);
        $slider = $this->modelo->alojamientosSlider();
        $operadores = $this->modelo->operadoresActivos();
        require __DIR__ . '/../vistas/public/home.php';
    }

    public function ver() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: index.php'); exit; }
        $sitio = $this->modelo->alojamientoPublicado($id);
        if (!$sitio) { header('Location: index.php'); exit; }
        $promedioValoraciones = $this->valoracionModelo->promedioYConteo($id);
        $valoracionCliente = null;
        $reservaFinalizada = null;
        $puedeValorar = false;
        if (isset($_SESSION['usuario_id']) && ($_SESSION['usuario_rol'] ?? '') === 'cliente') {
            $valoracionCliente = $this->valoracionModelo->porClienteYAlojamiento($_SESSION['usuario_id'], $id);
            $reservaFinalizada = $this->reservaModelo->clienteConReservaFinalizada($id, $_SESSION['usuario_id']);
            $puedeValorar = $reservaFinalizada && !$valoracionCliente;
        }
        $mensajeExito = isset($_GET['exito']) ? '¡Gracias por compartir tu experiencia!' : '';
        $mensajeError = $_GET['error'] ?? '';
        require __DIR__ . '/../vistas/public/detalle_sitio.php';
    }

    public function lista_propietario() {
        $propietario = $_SESSION['usuario_id'] ?? null;
        if (!$propietario || ($_SESSION['usuario_rol'] ?? '') !== 'propietario') { header('Location: index.php?ruta=auth/login'); exit; }
        $sitios = $this->modelo->obtenerPorPropietario($propietario);
        require __DIR__ . '/../vistas/propietario/lista_sitios.php';
    }

    public function crear() {
        $propietario = $_SESSION['usuario_id'] ?? null;
        if (!$propietario || ($_SESSION['usuario_rol'] ?? '') !== 'propietario') { header('Location: index.php?ruta=auth/login'); exit; }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token']==='') die('Token CSRF faltante');
            $imagen = $this->procesarSubidaImagen($_FILES['imagen'] ?? null);
            $servicios = array_map('intval', $_POST['servicios'] ?? []);
            $data = [
                'propietario_id'=>$propietario,
                'nombre'=>$_POST['nombre']??'',
                'descripcion'=>$_POST['descripcion']??'',
                'ubicacion'=>$_POST['ubicacion']??'',
                'precio_noche'=>$_POST['precio_noche']??0,
                'rango_precio'=>$_POST['rango_precio']??'',
                'imagen'=>$imagen,
                'estado'=>'pendiente'
            ];
            $this->modelo->crearAlojamiento($data, $servicios);
            header('Location: index.php?ruta=propietario/sitios'); exit;
        }
        $sitio = null;
        $serviciosDisponibles = $this->serviciosModelo->todos();
        $serviciosSeleccionados = [];
        require __DIR__ . '/../vistas/propietario/form_sitio.php';
    }

    public function editar() {
        $propietario = $_SESSION['usuario_id'] ?? null;
        if (!$propietario || ($_SESSION['usuario_rol'] ?? '') !== 'propietario') { header('Location: index.php?ruta=auth/login'); exit; }
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: index.php?ruta=propietario/sitios'); exit; }
        $sitio = $this->modelo->buscarAlojamiento($id);
        if (!$sitio || $sitio['propietario_id'] != $propietario) die('No autorizado');
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token']==='') die('Token CSRF faltante');
            $imagen = $this->procesarSubidaImagen($_FILES['imagen'] ?? null, $sitio['imagen'] ?? null);
            $servicios = array_map('intval', $_POST['servicios'] ?? []);
            $data = [
                'nombre'=>$_POST['nombre']??'',
                'descripcion'=>$_POST['descripcion']??'',
                'ubicacion'=>$_POST['ubicacion']??'',
                'precio_noche'=>$_POST['precio_noche']??0,
                'rango_precio'=>$_POST['rango_precio']??'',
                'imagen'=>$imagen,
                'estado'=>$sitio['estado']
            ];
            $this->modelo->actualizarAlojamiento($id,$data,$servicios);
            header('Location: index.php?ruta=propietario/sitios'); exit;
        }
        $serviciosDisponibles = $this->serviciosModelo->todos();
        $serviciosSeleccionados = $this->modelo->serviciosIds($id);
        require __DIR__ . '/../vistas/propietario/form_sitio.php';
    }

    public function eliminar() {
        $propietario = $_SESSION['usuario_id'] ?? null;
        if (!$propietario || ($_SESSION['usuario_rol'] ?? '') !== 'propietario') { header('Location: index.php?ruta=auth/login'); exit; }
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sitio = $this->modelo->buscarAlojamiento($id);
            if ($sitio && $sitio['propietario_id']==$propietario) $this->modelo->eliminarAlojamiento($id);
        }
        header('Location: index.php?ruta=propietario/sitios'); exit;
    }

    private function procesarSubidaImagen(?array $file, ?string $imagenActual = null): ?string {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE || ($file['name'] ?? '') === '') {
            return $imagenActual;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return $imagenActual;
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg',
            'image/pjpeg'=> 'jpg',
            'image/png'  => 'png',
            'image/x-png'=> 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];

        $mimeType = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mimeType])) {
            return $imagenActual;
        }

        $maxSize = 4 * 1024 * 1024; // 4 MB
        if (($file['size'] ?? 0) > $maxSize) {
            return $imagenActual;
        }

        $dir = __DIR__ . '/../public/storage/subidas/';
        if (!is_dir($dir)) { mkdir($dir, 0755, true); }

        $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
        $slug = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $baseName);
        $slug = trim($slug, '-');
        if ($slug === '') { $slug = 'imagen'; }

        $nombreArchivo = time() . '_' . $slug . '.' . $allowed[$mimeType];
        $destino = $dir . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            return $imagenActual;
        }

        if ($imagenActual) {
            $rutaAnterior = __DIR__ . '/../public/' . ltrim($imagenActual, '/');
            if (is_file($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        return '/storage/subidas/' . $nombreArchivo;
    }
}
?>
