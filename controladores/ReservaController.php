<?php
require_once __DIR__ . '/../modelos/Reserva.php';

class ReservaController {
    private $reservaModelo;

    public function __construct() {
        $this->reservaModelo = new Reserva();
    }

    public function adminIndex() {
        $this->soloAdmin();
        $mes = $_GET['mes'] ?? '';
        $propietario = isset($_GET['propietario']) && $_GET['propietario'] !== '' ? (int) $_GET['propietario'] : null;
        $reservas = $this->reservaModelo->listarParaAdmin($mes, $propietario);
        $propietarios = $this->reservaModelo->propietariosConReservas();
        require __DIR__ . '/../vistas/admin/reservas.php';
    }

    public function adminEstado() {
        $this->soloAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
            $estado = $_POST['estado'] ?? '';
            if ($id) {
                $this->reservaModelo->cambiarEstado($id, $estado);
            }
        }
        header('Location: index.php?ruta=admin/reservas'); exit;
    }

    public function propietarioIndex() {
        $this->soloPropietario();
        $mes = $_GET['mes'] ?? '';
        $reservas = $this->reservaModelo->listarParaPropietario($_SESSION['usuario_id'], $mes);
        require __DIR__ . '/../vistas/propietario/reservas.php';
    }

    public function propietarioEstado() {
        $this->soloPropietario();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
            $estado = $_POST['estado'] ?? '';
            if ($id) {
                $this->reservaModelo->cambiarEstado($id, $estado, $_SESSION['usuario_id']);
            }
        }
        header('Location: index.php?ruta=propietario/reservas'); exit;
    }

    private function soloAdmin() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
    }

    private function soloPropietario() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'propietario') { header('Location: index.php?ruta=auth/login'); exit; }
    }
}
?>
