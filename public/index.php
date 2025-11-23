<?php
session_start();

<<<<<<< ours

=======
>>>>>>> theirs
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controladores/AuthController.php';
require_once __DIR__ . '/../controladores/SitioController.php';
require_once __DIR__ . '/../controladores/AdminController.php';
require_once __DIR__ . '/../controladores/ClienteController.php';
require_once __DIR__ . '/../controladores/ReservaController.php';
<<<<<<< ours
require_once __DIR__ . '/../controladores/ClienteController.php';
require_once __DIR__ . '/../controladores/ReservaController.php';
=======

$ruta = trim($_GET['ruta'] ?? 'inicio', '/');
>>>>>>> theirs

function cabecera($titulo = 'Turismo San Luis') {
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . htmlspecialchars($titulo) . '</title>'
        . '<link rel="stylesheet" href="../assets/css/style.css"></head><body>'
        . '<header class="site-header"><div class="container"><a class="brand" href="index.php">Turismo San Luis</a><nav>'
        . '<a href="index.php">Inicio</a> | <a href="index.php?ruta=auth/register">Afíliate</a>';

<<<<<<< ours
function cabecera($titulo = 'Turismo San Luis') {
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . htmlspecialchars($titulo) . '</title>'
        . '<link rel="stylesheet" href="../assets/css/style.css"></head><body>'
        . '<header class="site-header"><div class="container"><a class="brand" href="index.php">Turismo San Luis</a><nav>'
        . '<a href="index.php">Inicio</a> | <a href="index.php?ruta=auth/register">Afíliate</a>';

=======
>>>>>>> theirs
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
<<<<<<< ours
        $rol = $_SESSION['usuario_rol'] ?? '';
        if ($rol === 'propietario') {
            echo ' | <a href="index.php?ruta=propietario/sitios">Mi Panel</a>';
            echo ' | <a href="index.php?ruta=propietario/reservas">Reservas</a>';
        }
        if ($rol === 'cliente') {
            echo ' | <a href="index.php?ruta=cliente/reservas">Mis reservas</a>';
        }
        if ($rol === 'admin') {
=======
>>>>>>> theirs
            echo ' | <a href="index.php?ruta=admin/afiliaciones">Afiliaciones</a>';
            echo ' | <a href="index.php?ruta=admin/alojamientos">Alojamientos</a>';
            echo ' | <a href="index.php?ruta=admin/servicios">Servicios</a>';
            echo ' | <a href="index.php?ruta=admin/clientes">Clientes</a>';
            echo ' | <a href="index.php?ruta=admin/reservas">Reservas</a>';
<<<<<<< ours
            echo ' | <a href="index.php?ruta=admin/servicios">Servicios</a>';
            echo ' | <a href="index.php?ruta=admin/clientes">Clientes</a>';
            echo ' | <a href="index.php?ruta=admin/reservas">Reservas</a>';
=======
>>>>>>> theirs
        }
        echo ' | <a href="index.php?ruta=auth/logout">Salir</a>';
    } else {
        echo ' | <a href="index.php?ruta=auth/login">Ingresar</a>';
    }

<<<<<<< ours

=======
>>>>>>> theirs
    echo '</nav></div></header><main class="container">';
}

function pie() {
    echo '</main><footer class="site-footer"><div class="container">© ' . date('Y') . ' Turismo San Luis</div></footer></body></html>';
}
<<<<<<< ours

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
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
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
=======

$rutasProtegidas = [
    'propietario/sitios' => function () { (new SitioController())->lista_propietario(); },
    'propietario/sitios/crear' => function () { (new SitioController())->crear(); },
    'propietario/sitios/editar' => function () { (new SitioController())->editar(); },
    'propietario/sitios/eliminar' => function () { (new SitioController())->eliminar(); },
    'propietario/reservas' => function () { (new ReservaController())->propietarioIndex(); },
    'propietario/reservas/estado' => function () { (new ReservaController())->propietarioEstado(); },
    'admin/afiliaciones' => function () { (new AdminController())->lista_afiliaciones(); },
    'admin/afiliaciones/aprobar' => function () { (new AdminController())->aprobar(); },
    'admin/afiliaciones/rechazar' => function () { (new AdminController())->rechazar(); },
    'admin/alojamientos' => function () { (new AdminController())->alojamientos(); },
    'admin/alojamientos/aprobar' => function () { (new AdminController())->aprobarAlojamiento(); },
    'admin/alojamientos/rechazar' => function () { (new AdminController())->rechazarAlojamiento(); },
    'admin/alojamientos/activar' => function () { (new AdminController())->activarAlojamiento(); },
    'admin/alojamientos/desactivar' => function () { (new AdminController())->desactivarAlojamiento(); },
    'admin/servicios' => function () { (new AdminController())->servicios(); },
    'admin/servicios/crear' => function () { (new AdminController())->crearServicio(); },
    'admin/servicios/actualizar' => function () { (new AdminController())->actualizarServicio(); },
    'admin/servicios/eliminar' => function () { (new AdminController())->eliminarServicio(); },
    'admin/reservas' => function () { (new ReservaController())->adminIndex(); },
    'admin/reservas/estado' => function () { (new ReservaController())->adminEstado(); },
    'admin/clientes' => function () { (new ClienteController())->adminIndex(); },
    'admin/clientes/crear' => function () { (new ClienteController())->adminCrear(); },
    'admin/clientes/editar' => function () { (new ClienteController())->adminEditar(); },
    'admin/clientes/eliminar' => function () { (new ClienteController())->adminEliminar(); },
    'cliente/reservar' => function () { (new ClienteController())->reservar(); },
    'cliente/reservas' => function () { (new ClienteController())->misReservas(); },
    'alojamiento/ver' => function () { (new SitioController())->ver(); },
];

if (isset($rutasProtegidas[$ruta])) {
    $rutasProtegidas[$ruta]();
    exit;
}

switch ($ruta) {
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
<<<<<<< ours
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $pass = $_POST['password'] ?? '';
            if (iniciar_sesion($email, $pass)) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'Credenciales incorrectas';
            }
=======
>>>>>>> theirs
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
