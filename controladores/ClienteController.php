<?php
require_once __DIR__ . '/../modelos/Cliente.php';
require_once __DIR__ . '/../modelos/Reserva.php';
require_once __DIR__ . '/../modelos/Sitio.php';

class ClienteController {
    private $clientesModelo;
    private $reservaModelo;
    private $sitioModelo;

    public function __construct() {
        $this->clientesModelo = new Cliente();
        $this->reservaModelo = new Reserva();
        $this->sitioModelo = new Sitio();
    }

    public function adminIndex() {
        $this->soloAdmin();
        $clientes = $this->clientesModelo->todos();
        require __DIR__ . '/../vistas/admin/clientes.php';
    }

    public function adminCrear() {
        $this->soloAdmin();
        $errores = [];
        $valores = $this->valoresBase();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$valores, $errores, $passwordHash] = $this->procesarFormulario($valores);
            if (empty($errores)) {
                try {
                    $valores['password_hash'] = $passwordHash;
                    $this->clientesModelo->crear($valores);
                    header('Location: index.php?ruta=admin/clientes'); exit;
                } catch (Throwable $e) {
                    $errores[] = 'Error al guardar: ' . $e->getMessage();
                }
            }
        }

        $esEdicion = false;
        require __DIR__ . '/../vistas/admin/form_cliente.php';
    }

    public function adminEditar() {
        $this->soloAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id) { header('Location: index.php?ruta=admin/clientes'); exit; }
        $cliente = $this->clientesModelo->encontrar($id);
        if (!$cliente) { header('Location: index.php?ruta=admin/clientes'); exit; }

        $errores = [];
        $valores = array_merge($this->valoresBase(), $cliente);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$valores, $errores, $passwordHash] = $this->procesarFormulario($valores, $id);
            if (empty($errores)) {
                try {
                    $valores['password_hash'] = $passwordHash;
                    $this->clientesModelo->actualizar($id, $valores);
                    header('Location: index.php?ruta=admin/clientes'); exit;
                } catch (Throwable $e) {
                    $errores[] = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }

        $esEdicion = true;
        require __DIR__ . '/../vistas/admin/form_cliente.php';
    }

    public function adminEliminar() {
        $this->soloAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
            if ($id) {
                $this->clientesModelo->eliminar($id);
            }
        }
        header('Location: index.php?ruta=admin/clientes'); exit;
    }

    public function reservar() {
        $this->soloCliente();
        $alojamientoId = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$alojamientoId) { header('Location: index.php'); exit; }
        $sitio = $this->sitioModelo->alojamientoPublicado($alojamientoId);
        if (!$sitio) { header('Location: index.php'); exit; }

        $errores = [];
        $exito = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $fechaFin = $_POST['fecha_fin'] ?? '';
            $errores = $this->validarFechas($fechaInicio, $fechaFin);
            if (empty($errores)) {
                $noches = (strtotime($fechaFin) - strtotime($fechaInicio)) / 86400;
                $total = $noches * (float) $sitio['precio_noche'];
                if ($this->reservaModelo->crear($sitio['id'], $_SESSION['usuario_id'], $fechaInicio, $fechaFin, $total)) {
                    $exito = true;
                    header('Location: index.php?ruta=cliente/reservas'); exit;
                } else {
                    $errores[] = 'No se pudo registrar la reserva. Intenta nuevamente.';
                }
            }
        }

        require __DIR__ . '/../vistas/cliente/form_reserva.php';
    }

    public function misReservas() {
        $this->soloCliente();
        $reservas = $this->reservaModelo->porCliente($_SESSION['usuario_id']);
        require __DIR__ . '/../vistas/cliente/reservas.php';
    }

    private function valoresBase() {
        return [
            'primer_nombre' => '',
            'segundo_nombre' => '',
            'primer_apellido' => '',
            'cedula' => '',
            'telefono_codigo' => '+57',
            'telefono_numero' => '',
            'email' => '',
            'municipio_origen' => '',
            'estado' => 'activo'
        ];
    }

    private function procesarFormulario(array $valores, $id = null) {
        $errores = [];
        $valores['primer_nombre'] = trim($_POST['primer_nombre'] ?? $valores['primer_nombre']);
        $valores['segundo_nombre'] = trim($_POST['segundo_nombre'] ?? $valores['segundo_nombre']);
        $valores['primer_apellido'] = trim($_POST['primer_apellido'] ?? $valores['primer_apellido']);
        $valores['cedula'] = trim($_POST['cedula'] ?? $valores['cedula']);
        $valores['telefono_codigo'] = trim($_POST['telefono_codigo'] ?? $valores['telefono_codigo']);
        $valores['telefono_numero'] = trim($_POST['telefono_numero'] ?? $valores['telefono_numero']);
        $valores['email'] = trim($_POST['email'] ?? $valores['email']);
        $valores['municipio_origen'] = trim($_POST['municipio_origen'] ?? $valores['municipio_origen']);
        $estado = $_POST['estado'] ?? $valores['estado'];
        $valores['estado'] = in_array($estado, ['activo','inactivo']) ? $estado : 'activo';

        if (strlen($valores['primer_nombre']) < 3 || strlen($valores['primer_nombre']) > 20) {
            $errores[] = 'El primer nombre debe tener entre 3 y 20 caracteres.';
        }
        if (strlen($valores['primer_apellido']) < 3 || strlen($valores['primer_apellido']) > 20) {
            $errores[] = 'El primer apellido debe tener entre 3 y 20 caracteres.';
        }
        if (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }
        if ($valores['telefono_numero'] === '') {
            $errores[] = 'El número de celular es obligatorio.';
        }
        if ($this->clientesModelo->existeEmail($valores['email'], $id)) {
            $errores[] = 'El correo ya está registrado.';
        }
        if ($this->clientesModelo->existeCedula($valores['cedula'], $id)) {
            $errores[] = 'La cédula ya está registrada.';
        }

        $passwordPlano = trim($_POST['password'] ?? '');
        $passwordHash = '';
        if ($passwordPlano !== '') {
            if (strlen($passwordPlano) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
            } else {
                $passwordHash = password_hash($passwordPlano, PASSWORD_DEFAULT);
            }
        } elseif (!$id) {
            $passwordHash = password_hash('123456', PASSWORD_DEFAULT);
        }

        return [$valores, $errores, $passwordHash];
    }

    private function validarFechas($inicio, $fin) {
        $errores = [];
        if (empty($inicio) || empty($fin)) {
            $errores[] = 'Selecciona la fecha de inicio y fin.';
            return $errores;
        }
        $ini = strtotime($inicio);
        $f = strtotime($fin);
        if ($ini === false || $f === false) {
            $errores[] = 'Las fechas no son válidas.';
            return $errores;
        }
        if ($ini >= $f) {
            $errores[] = 'La fecha de fin debe ser posterior a la de inicio.';
        }
        return $errores;
    }

    private function soloAdmin() {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') { header('Location: index.php'); exit; }
    }

    private function soloCliente() {
        if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'cliente') {
            header('Location: index.php?ruta=auth/login'); exit;
        }
    }
}
?>
