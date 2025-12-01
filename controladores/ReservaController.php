<?php
require_once __DIR__ . '/../modelos/Reserva.php';

class ReservaController {
    private $reservaModelo;

    public function __construct() {
        $this->reservaModelo = new Reserva();
    }

    public function adminIndex() {
        $this->soloAdmin();
        $mes = $this->mesConDefault($_GET['mes'] ?? '');
        $propietario = isset($_GET['propietario']) && $_GET['propietario'] !== '' ? (int) $_GET['propietario'] : null;

        $reservas = $this->reservaModelo->listarParaAdmin($mes, $propietario);
        $propietarios = $this->reservaModelo->propietariosConReservas();
        $mesSeleccionado = $mes;
        $propietarioSeleccionado = $propietario;
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
        $mes = $this->mesConDefault($_GET['mes'] ?? '');
        $reservas = $this->reservaModelo->listarParaPropietario($_SESSION['usuario_id'], $mes);
        $mesSeleccionado = $mes;
        require __DIR__ . '/../vistas/propietario/reservas.php';
    }

    public function adminAgenda() {
        $this->soloAdmin();
        $mes = $this->mesConDefault($_GET['mes'] ?? '');
        $propietario = isset($_GET['propietario']) && $_GET['propietario'] !== '' ? (int) $_GET['propietario'] : null;

        $reservas = $this->reservaModelo->listarAgendaAdmin($mes, $propietario);
        [$inicioMes, $finMes] = $this->reservaModelo->rangoMes($mes);
        $agenda = $this->construirAgenda($reservas, $inicioMes, $finMes);
        $hayReservas = array_reduce($agenda, fn($carry, $items) => $carry + count($items), 0) > 0;
        $propietarios = $this->reservaModelo->propietariosConReservas();
        $mesSeleccionado = $mes;
        $propietarioSeleccionado = $propietario;

        require __DIR__ . '/../vistas/admin/agenda.php';
    }

    public function propietarioAgenda() {
        $this->soloPropietario();
        $mes = $this->mesConDefault($_GET['mes'] ?? '');
        $alojamiento = isset($_GET['alojamiento']) && $_GET['alojamiento'] !== '' ? (int) $_GET['alojamiento'] : null;

        $reservas = $this->reservaModelo->listarAgendaPropietario($_SESSION['usuario_id'], $mes, $alojamiento);
        [$inicioMes, $finMes] = $this->reservaModelo->rangoMes($mes);
        $agenda = $this->construirAgenda($reservas, $inicioMes, $finMes);
        $hayReservas = array_reduce($agenda, fn($carry, $items) => $carry + count($items), 0) > 0;
        $alojamientos = $this->reservaModelo->alojamientosDePropietario($_SESSION['usuario_id']);
        $mesSeleccionado = $mes;
        $alojamientoSeleccionado = $alojamiento;

        require __DIR__ . '/../vistas/propietario/agenda.php';
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

    private function mesSeguro($mes) {
        $mes = substr(trim($mes), 0, 7);
        return preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : '';
    }

    private function mesConDefault($mes) {
        $seguro = $this->mesSeguro($mes);
        return $seguro !== '' ? $seguro : date('Y-m');
    }

    private function construirAgenda(array $reservas, string $inicioMes, string $finMes): array {
        $agenda = [];
        $inicio = new DateTime($inicioMes);
        $fin = (new DateTime($finMes))->modify('+1 day');
        $periodo = new DatePeriod($inicio, new DateInterval('P1D'), $fin);

        foreach ($periodo as $dia) {
            $agenda[$dia->format('Y-m-d')] = [];
        }

        foreach ($reservas as $r) {
            $inicioReserva = new DateTime($r['fecha_inicio']);
            $finReserva = new DateTime($r['fecha_fin']);

            if ($finReserva < $inicio || $inicioReserva > $fin) {
                continue;
            }

            $inicioIter = max($inicioReserva, $inicio);
            $finIter = min($finReserva, (clone $fin)->modify('-1 day'));

            $cursor = $inicioIter;
            while ($cursor <= $finIter) {
                $clave = $cursor->format('Y-m-d');
                if (!isset($agenda[$clave])) {
                    $agenda[$clave] = [];
                }
                $agenda[$clave][] = $r;
                $cursor->modify('+1 day');
            }
        }

        return $agenda;
    }
}
?>
