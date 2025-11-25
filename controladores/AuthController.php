<?php
require_once __DIR__ . '/../modelos/Database.php';
require_once __DIR__ . '/../modelos/Cliente.php';

function iniciar_sesion($email,$password) {
    $pdo = Database::conexion();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($password,$u['password'])) {
        $_SESSION['usuario_id'] = $u['id'];
        $_SESSION['usuario_email'] = $u['email'];
        $_SESSION['usuario_rol'] = $u['rol'];
        return true;
    }
    return false;
}

function manejar_registro_afiliado() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;
    $pdo = Database::conexion();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $nombre_negocio = trim($_POST['nombre_negocio'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $direccion = trim($_POST['direccion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    if ($email==''||$password==''||$nombre_negocio=='') return ['success'=>false,'message'=>'Faltan campos obligatorios'];
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1'); $stmt->execute([$email]);
    if ($stmt->fetch()) return ['success'=>false,'message'=>'El correo ya está registrado'];
    try {
        $hash = password_hash($password,PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol,estado) VALUES (?,?,?,?,?)');
        $stmt->execute([$nombre_negocio,$email,$hash,'propietario','activo']);
        $usuario_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO afiliados (usuario_id,nombre_negocio,tipo,descripcion,direccion,estado) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$usuario_id,$nombre_negocio,$tipo,$descripcion,$direccion,'pendiente']);
        return ['success'=>true,'message'=>'Solicitud enviada. Un administrador la revisará.'];
    } catch (Exception $e) {
        return ['success'=>false,'message'=>'Error: '.$e->getMessage()];
    }
}

function manejar_registro_cliente() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;

    $clienteModelo = new Cliente();
    $valores = [
        'primer_nombre' => trim($_POST['primer_nombre'] ?? ''),
        'segundo_nombre' => trim($_POST['segundo_nombre'] ?? ''),
        'primer_apellido' => trim($_POST['primer_apellido'] ?? ''),
        'cedula' => trim($_POST['cedula'] ?? ''),
        'telefono_codigo' => trim($_POST['telefono_codigo'] ?? '+57'),
        'telefono_numero' => trim($_POST['telefono_numero'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'municipio_origen' => trim($_POST['municipio_origen'] ?? ''),
        'estado' => 'activo'
    ];

    $errores = [];
    $length = function (string $value) {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    };

    $primerNombreLen = $length($valores['primer_nombre']);
    $segundoNombreLen = $length($valores['segundo_nombre']);
    $primerApellidoLen = $length($valores['primer_apellido']);
    $cedulaLen = $length($valores['cedula']);
    $telefonoLen = $length($valores['telefono_numero']);
    $municipioOrigenLen = $length($valores['municipio_origen']);

    if ($primerNombreLen < 3 || $primerNombreLen > 30) {
        $errores[] = 'El primer nombre debe tener entre 3 y 30 caracteres.';
    }
    if ($segundoNombreLen > 30) {
        $errores[] = 'El segundo nombre no puede exceder 30 caracteres.';
    }
    if ($primerApellidoLen < 3 || $primerApellidoLen > 30) {
        $errores[] = 'El primer apellido debe tener entre 3 y 30 caracteres.';
    }
    if (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }
    if ($valores['telefono_numero'] === '') {
        $errores[] = 'El número de celular es obligatorio.';
    } elseif ($telefonoLen > 15) {
        $errores[] = 'El número de celular no puede exceder 15 caracteres.';
    }
    if ($municipioOrigenLen > 100) {
        $errores[] = 'El municipio de origen no puede exceder 100 caracteres.';
    }
    if ($clienteModelo->existeEmail($valores['email'])) {
        $errores[] = 'El correo ya está registrado.';
    }
    if ($valores['cedula'] !== '') {
        if ($cedulaLen > 30) {
            $errores[] = 'La cédula no puede exceder 30 caracteres.';
        }
        if ($clienteModelo->existeCedula($valores['cedula'])) {
            $errores[] = 'La cédula ya está registrada.';
        }
    }

    $passwordPlano = trim($_POST['password'] ?? '');
    if (strlen($passwordPlano) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if (!empty($errores)) {
        return ['success' => false, 'message' => implode(' ', $errores)];
    }

    try {
        $valores['password_hash'] = password_hash($passwordPlano, PASSWORD_DEFAULT);
        $clienteModelo->crear($valores);
        return ['success' => true, 'message' => 'Registro exitoso. Ahora puedes iniciar sesión.'];
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
    }
}
?>
