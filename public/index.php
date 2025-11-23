<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controladores/AuthController.php';
require_once __DIR__ . '/../controladores/SitioController.php';
require_once __DIR__ . '/../controladores/AdminController.php';
require_once __DIR__ . '/../controladores/ClienteController.php';
require_once __DIR__ . '/../controladores/ReservaController.php';

$ruta = $_GET['ruta'] ?? 'inicio';

function cabecera($titulo = 'Turismo San Luis') {
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . htmlspecialchars($titulo) . '</title>'
        . '<link rel="stylesheet" href="../assets/css/style.css"></head><body>'
        . '<header class="site-header"><div class="container"><a class="brand" href="index.php">Turismo San Luis</a><nav>'
        . '<a href="index.php">Inicio</a> | <a href="index.php?ruta=auth/register">Afíliate</a>';

    if (isset($_SESSION['usuario_id'])) {
        $rol = $_SESSION['usuario_rol'] ?? '';
        if ($rol === 'propietario') {
            echo ' | <a href="index.php?ruta=propietario/sitios">Mi Panel</a>';
            echo ' | <a href="index.php?ruta=propietario/reservas">Reservas</a>';
        }
        if ($rol === 'cliente') {
            echo ' | <a href="index.php?ruta=cliente/reservas">Mis reservas</a>';
        }
        if ($rol === 'admin') {
            echo ' | <a href="index.php?ruta=admin/afiliaciones">Afiliaciones</a>';
            echo ' | <a href="index.php?ruta=admin/alojamientos">Alojamientos</a>';
            echo ' | <a href="index.php?ruta=admin/servicios">Servicios</a>';
            echo ' | <a href="index.php?ruta=admin/clientes">Clientes</a>';
            echo ' | <a href="index.php?ruta=admin/reservas">Reservas</a>';
        }
        echo ' | <a href="index.php?ruta=auth/logout">Salir</a>';
    } else {
        echo ' | <a href="index.php?ruta=auth/login">Ingresar</a>';
    }

    echo '</nav></div></header><main class="container">';
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
    case 'admin/reservas':
        $ctrl = new ReservaController();
        $ctrl->adminIndex();
        break;
    case 'admin/reservas/estado':
        $ctrl = new ReservaController();
        $ctrl->adminEstado();
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
    case 'auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $pass = $_POST['password'] ?? '';
            if (iniciar_sesion($email, $pass)) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'Credenciales incorrectas';
            }
        }
        require __DIR__ . '/../vistas/auth/login.php';
        break;
    case 'auth/logout':
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    case 'auth/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = manejar_registro_afiliado();
            $msg = $res['message'] ?? '';
            $ok = $res['success'] ?? false;
            require __DIR__ . '/../vistas/auth/register_afiliado.php';
        } else {
            require __DIR__ . '/../vistas/auth/register_afiliado.php';
        }
        break;
    case 'sembrar':
        require __DIR__ . '/sembrar_usuarios.php';
        break;
    default:
        $ctrl = new SitioController();
        $ctrl->inicio();
        break;
}

?>
=======
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
    case 'admin/reservas':
        $ctrl = new ReservaController();
        $ctrl->adminIndex();
        break;
    case 'admin/reservas/estado':
        $ctrl = new ReservaController();
        $ctrl->adminEstado();
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
>>>>>>> theirs
    case 'auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $pass = $_POST['password'] ?? '';
            if (iniciar_sesion($email, $pass)) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'Credenciales incorrectas';
            }
        }
        require __DIR__ . '/../vistas/auth/login.php';
        break;
    case 'auth/logout':
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    case 'auth/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = manejar_registro_afiliado();
            $msg = $res['message'] ?? '';
            $ok = $res['success'] ?? false;
            require __DIR__ . '/../vistas/auth/register_afiliado.php';
        } else {
            require __DIR__ . '/../vistas/auth/register_afiliado.php';
        }
        break;
<<<<<<< ours

    case 'sembrar': require __DIR__ . '/sembrar_usuarios.php'; break;
    default: $ctrl = new SitioController(); $ctrl->inicio(); break;
=======
    case 'sembrar':
        require __DIR__ . '/sembrar_usuarios.php';
        break;
    default:
        $ctrl = new SitioController();
        $ctrl->inicio();
        break;
>>>>>>> theirs
}

?>
