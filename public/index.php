<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controladores/AuthController.php';
require_once __DIR__ . '/../controladores/SitioController.php';
require_once __DIR__ . '/../controladores/AdminController.php';
require_once __DIR__ . '/../controladores/ClienteController.php';
require_once __DIR__ . '/../controladores/ReservaController.php';
require_once __DIR__ . '/../controladores/ValoracionController.php';
require_once __DIR__ . '/../modelos/Setting.php';

$ruta = trim($_GET['ruta'] ?? 'inicio', '/');

function getSetting($key, $default = null) {
    return Setting::get($key, $default);
}

function setSetting($key, $value) {
    return Setting::set($key, $value);
}

function publicUrl(string $path): string {
    $normalized = '/' . ltrim($path, '/');
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if ($base === '' || $base === '/') {
        return $normalized;
    }
    return $base . $normalized;
}

function assetUrl(?string $path): string {
    if (empty($path)) {
        return '';
    }

    if (preg_match('#^(https?:)?//#i', $path) || strpos($path, 'data:') === 0) {
        return $path;
    }

    $normalized = '/' . ltrim($path, '/');

    if (strpos($normalized, '/public/') === 0) {
        $normalized = substr($normalized, strlen('/public'));
        $normalized = '/' . ltrim($normalized, '/');
    }

    return publicUrl($normalized);
}

function getBackgroundImageUrl() {
    $default = '/assets/img/default-bg.jpg';
    $stored = getSetting('background_image', null);

    $candidates = [];

    if (is_string($stored) && $stored !== '') {
        $candidates[] = $stored;
    }

    $candidates[] = $default;

    foreach ($candidates as $relativePath) {
        $absolutePath = __DIR__ . $relativePath;

        if (file_exists($absolutePath)) {
            $url = publicUrl($relativePath);
            $version = filemtime($absolutePath);

            return $version ? $url . '?v=' . $version : $url;
        }
    }

    return publicUrl($default);
}

function cabecera($titulo = 'Turismo San Luis', array $extraCss = [], string $bodyClass = '') {
    $links = [
        ['href' => 'index.php', 'label' => 'Inicio'],
        ['href' => 'index.php?ruta=auth/register', 'label' => 'Afíliate'],
        ['href' => 'index.php?ruta=auth/register-cliente', 'label' => 'Regístrate'],
    ];

    if (isset($_SESSION['usuario_id'])) {
        $rol = $_SESSION['usuario_rol'] ?? '';
        if ($rol === 'propietario') {
            $links[] = ['href' => 'index.php?ruta=propietario/sitios', 'label' => 'Mi Panel'];
            $links[] = ['href' => 'index.php?ruta=propietario/reservas', 'label' => 'Reservas'];
            $links[] = ['href' => 'index.php?ruta=propietario/valoraciones', 'label' => 'Comentarios'];
        }
        if ($rol === 'cliente') {
            $links[] = ['href' => 'index.php?ruta=cliente/reservas', 'label' => 'Mis reservas'];
        }
        if ($rol === 'admin') {
            $links[] = ['href' => 'index.php?ruta=admin/afiliaciones', 'label' => 'Afiliaciones'];
            $links[] = ['href' => 'index.php?ruta=admin/alojamientos', 'label' => 'Alojamientos'];
            $links[] = ['href' => 'index.php?ruta=admin/servicios', 'label' => 'Servicios'];
            $links[] = ['href' => 'index.php?ruta=admin/configuracion', 'label' => 'Configuración'];
            $links[] = ['href' => 'index.php?ruta=admin/clientes', 'label' => 'Clientes'];
            $links[] = ['href' => 'index.php?ruta=admin/reservas', 'label' => 'Reservas'];
            $links[] = ['href' => 'index.php?ruta=admin/valoraciones', 'label' => 'Comentarios'];
        }
        $links[] = ['href' => 'index.php?ruta=auth/logout', 'label' => 'Salir'];
    } else {
        $links[] = ['href' => 'index.php?ruta=auth/login', 'label' => 'Ingresar'];
    }

    $stylesheets = array_merge(['../assets/css/style.css'], $extraCss);
    $cssLinks = array_map(
        fn($css) => '<link rel="stylesheet" href="' . htmlspecialchars($css) . '">',
        $stylesheets
    );

    $bodyAttr = $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass) . '"' : '';

    $linksHtml = array_map(
        fn($link) => '<a href="' . htmlspecialchars($link['href']) . '">' . htmlspecialchars($link['label']) . '</a>',
        $links
    );

    echo '<!doctype html>'
        . '<html lang="es">'
        . '<head>'
        . '<meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . htmlspecialchars($titulo) . '</title>'
        . implode('', $cssLinks)
        . '</head>'
        . '<body' . $bodyAttr . '>'
        . '<header class="site-header"><div class="container">'
        . '<a class="brand" href="index.php">Turismo San Luis</a>'
        . '<button class="nav-toggle" aria-expanded="false" aria-label="Abrir menú" aria-controls="primary-menu">'
        . '<span></span><span></span><span></span>'
        . '</button>'
        . '<nav id="primary-menu" class="nav-menu">'
        . implode('', $linksHtml)
        . '</nav>'
        . '</div></header>'
        . '<script>(function(){const t=document.querySelector(".nav-toggle"),n=document.getElementById("primary-menu");if(!t||!n)return;t.addEventListener("click",function(){var e=n.classList.toggle("is-open");t.setAttribute("aria-expanded",e?"true":"false")});})();</script>'
        . '<main class="container">';
}

function pie() {
    echo '</main><footer class="site-footer"><div class="container">© ' . date('Y') . ' Turismo San Luis</div></footer></body></html>';
}

switch ($ruta) {
    case 'propietario/sitios':
        $ctrl = new SitioController();
        $ctrl->lista_propietario();
        break;
    case 'propietario/sitios/crear':
        $ctrl = new SitioController();
        $ctrl->crear();
        break;
    case 'propietario/sitios/editar':
        $ctrl = new SitioController();
        $ctrl->editar();
        break;
    case 'propietario/sitios/eliminar':
        $ctrl = new SitioController();
        $ctrl->eliminar();
        break;
    case 'propietario/reservas':
        $ctrl = new ReservaController();
        $ctrl->propietarioIndex();
        break;
    case 'propietario/reservas/estado':
        $ctrl = new ReservaController();
        $ctrl->propietarioEstado();
        break;
    case 'propietario/valoraciones':
        $ctrl = new ValoracionController();
        $ctrl->propietarioIndex();
        break;
    case 'propietario/valoraciones/actualizar':
        $ctrl = new ValoracionController();
        $ctrl->actualizar();
        break;
    case 'propietario/valoraciones/eliminar':
        $ctrl = new ValoracionController();
        $ctrl->eliminar();
        break;
    case 'alojamiento/ver':
        $ctrl = new SitioController();
        $ctrl->ver();
        break;
    case 'admin/afiliaciones':
        $ctrl = new AdminController();
        $ctrl->lista_afiliaciones();
        break;
    case 'admin/afiliaciones/aprobar':
        $ctrl = new AdminController();
        $ctrl->aprobar();
        break;
    case 'admin/afiliaciones/rechazar':
        $ctrl = new AdminController();
        $ctrl->rechazar();
        break;
    case 'admin/alojamientos':
        $ctrl = new AdminController();
        $ctrl->alojamientos();
        break;
    case 'admin/alojamientos/aprobar':
        $ctrl = new AdminController();
        $ctrl->aprobarAlojamiento();
        break;
    case 'admin/alojamientos/rechazar':
        $ctrl = new AdminController();
        $ctrl->rechazarAlojamiento();
        break;
    case 'admin/alojamientos/activar':
        $ctrl = new AdminController();
        $ctrl->activarAlojamiento();
        break;
    case 'admin/alojamientos/desactivar':
        $ctrl = new AdminController();
        $ctrl->desactivarAlojamiento();
        break;
    case 'admin/alojamientos/slider':
        $ctrl = new AdminController();
        $ctrl->sliderAlojamiento();
        break;
    case 'admin/servicios':
        $ctrl = new AdminController();
        $ctrl->servicios();
        break;
    case 'admin/servicios/crear':
        $ctrl = new AdminController();
        $ctrl->crearServicio();
        break;
    case 'admin/servicios/actualizar':
        $ctrl = new AdminController();
        $ctrl->actualizarServicio();
        break;
    case 'admin/servicios/eliminar':
        $ctrl = new AdminController();
        $ctrl->eliminarServicio();
        break;
    case 'admin/configuracion':
        $ctrl = new AdminController();
        $ctrl->configuracion();
        break;
    case 'admin/configuracion/guardar':
        $ctrl = new AdminController();
        $ctrl->guardarConfiguracion();
        break;
    case 'admin/reservas':
        $ctrl = new ReservaController();
        $ctrl->adminIndex();
        break;
    case 'admin/reservas/estado':
        $ctrl = new ReservaController();
        $ctrl->adminEstado();
        break;
    case 'admin/valoraciones':
        $ctrl = new ValoracionController();
        $ctrl->adminIndex();
        break;
    case 'admin/valoraciones/actualizar':
        $ctrl = new ValoracionController();
        $ctrl->actualizar();
        break;
    case 'admin/valoraciones/eliminar':
        $ctrl = new ValoracionController();
        $ctrl->eliminar();
        break;
    case 'admin/clientes':
        $ctrl = new ClienteController();
        $ctrl->adminIndex();
        break;
    case 'admin/clientes/crear':
        $ctrl = new ClienteController();
        $ctrl->adminCrear();
        break;
    case 'admin/clientes/editar':
        $ctrl = new ClienteController();
        $ctrl->adminEditar();
        break;
    case 'admin/clientes/eliminar':
        $ctrl = new ClienteController();
        $ctrl->adminEliminar();
        break;
    case 'cliente/reservar':
        $ctrl = new ClienteController();
        $ctrl->reservar();
        break;
    case 'cliente/reservas':
        $ctrl = new ClienteController();
        $ctrl->misReservas();
        break;
    case 'cliente/calificar':
        $ctrl = new ClienteController();
        $ctrl->calificar();
        break;
    case 'auth/login':
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if (iniciar_sesion($email, $password)) {
                header('Location: index.php'); exit;
            } else {
                $error = 'Credenciales inválidas';
            }
        }
        require __DIR__ . '/../vistas/auth/login.php';
        break;
    case 'auth/register':
        $msg = '';
        $ok = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = manejar_registro_afiliado();
            if (is_array($res)) { $ok = !empty($res['success']); $msg = $res['message'] ?? ''; }
        }
        require __DIR__ . '/../vistas/auth/register_afiliado.php';
        break;
    case 'auth/register-cliente':
        $msg = '';
        $ok = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = manejar_registro_cliente();
            if (is_array($res)) { $ok = !empty($res['success']); $msg = $res['message'] ?? ''; }
        }
        require __DIR__ . '/../vistas/auth/register_cliente.php';
        break;
    case 'auth/logout':
        session_unset();
        session_destroy();
        header('Location: index.php'); exit;
        break;
    case 'sembrar':
        require __DIR__ . '/sembrar_usuarios.php';
        break;
    case 'inicio':
    default:
        $ctrl = new SitioController();
        $ctrl->inicio();
        break;
}

?>
