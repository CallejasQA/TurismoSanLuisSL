<?php
require_once __DIR__ . '/../modelos/Database.php';
require_once __DIR__ . '/../modelos/Cliente.php';

function iniciar_sesion($email,$password) {
    $pdo = Database::conexion();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if (!$u || !password_verify($password, $u['password'])) {
        return ['success' => false, 'message' => 'Credenciales inválidas'];
    }

    if (($u['rol'] ?? '') === 'propietario') {
        $afiliadoStmt = $pdo->prepare('SELECT estado FROM afiliados WHERE usuario_id = ? LIMIT 1');
        $afiliadoStmt->execute([$u['id']]);
        $afiliacion = $afiliadoStmt->fetch();

        if ($afiliacion && $afiliacion['estado'] !== 'aprobado') {
            $mensaje = $afiliacion['estado'] === 'pendiente'
                ? 'Tu solicitud de afiliación aún no ha sido aprobada por el administrador.'
                : 'Tu solicitud de afiliación fue rechazada. Contacta al administrador para más información.';

            return ['success' => false, 'message' => $mensaje];
        }
    }

    $_SESSION['usuario_id'] = $u['id'];
    $_SESSION['usuario_email'] = $u['email'];
    $_SESSION['usuario_rol'] = $u['rol'];

    return ['success' => true, 'message' => ''];
}

 function manejar_registro_afiliado() {
     if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;

     $pdo = Database::conexion();

     $sanitize = fn(string $value) => trim($value);
     $inputs = [
         'nombre_negocio' => $sanitize($_POST['nombre_negocio'] ?? ''),
         'tipo' => trim($_POST['tipo'] ?? ''),
         'direccion' => $sanitize($_POST['direccion'] ?? ''),
         'descripcion' => $sanitize($_POST['descripcion'] ?? ''),
         'email' => preg_replace('/\s+/', '', $_POST['email'] ?? ''),
         'password' => $_POST['password'] ?? ''
     ];

     $errores = [];

     $length = function (string $value) {
         return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
     };

     $validarCampo = function (string $campo, callable $validator) use (&$errores, $inputs) {
         $resultado = $validator($inputs[$campo]);
         if ($resultado !== null) {
             $errores[] = $resultado;
         }
     };

     $validarCampo('nombre_negocio', function (string $valor) use ($length) {
         $len = $length($valor);
         if ($len < 3 || $len > 30) {
             return 'El nombre del negocio debe tener entre 3 y 30 caracteres.';
         }
         return null;
     });

     $validarCampo('direccion', function (string $valor) use ($length) {
         $len = $length($valor);
         if ($len < 3 || $len > 80) {
             return 'La dirección debe tener entre 3 y 80 caracteres.';
         }
         return null;
     });

     $validarCampo('descripcion', function (string $valor) use ($length) {
         $len = $length($valor);
         if ($len < 3 || $len > 300) {
             return 'La descripción debe tener entre 3 y 300 caracteres.';
         }
         return null;
     });

     $validarCampo('email', function (string $valor) use ($length) {
         if ($valor === '') {
             return 'El email es obligatorio.';
         }
         if (preg_match('/\s/', $valor)) {
             return 'El formato del correo no es válido.';
         }
         $len = $length($valor);
         if ($len < 6 || $len > 70) {
             return 'El correo debe tener entre 6 y 70 caracteres.';
         }
         if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
             return 'El formato del correo no es válido.';
         }
         return null;
     });

     $validarCampo('password', function (string $valor) use ($length) {
         if ($valor === '') {
             return 'La contraseña es obligatoria.';
         }
         $len = $length($valor);
         if ($len < 6 || $len > 20) {
             return 'La contraseña debe tener entre 6 y 20 caracteres.';
         }
         return null;
     });

     $validarCampo('tipo', function (string $valor) {
         $tiposPermitidos = ['Finca', 'Glamping', 'Hotel', 'Cabaña', 'Ecohotel'];
         if ($valor === '') {
             return 'Debe seleccionar un tipo de alojamiento.';
         }
         if (!in_array($valor, $tiposPermitidos, true)) {
             return 'El tipo de alojamiento seleccionado no es válido.';
         }
         return null;
     });

     if (!empty($errores)) {
         return ['success' => false, 'message' => implode(' ', $errores)];
     }

     $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1');
     $stmt->execute([$inputs['email']]);
     if ($stmt->fetch()) return ['success'=>false,'message'=>'El correo ya está registrado'];
     try {
         $hash = password_hash($inputs['password'],PASSWORD_DEFAULT);
         $stmt = $pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol,estado) VALUES (?,?,?,?,?)');
         $stmt->execute([$inputs['nombre_negocio'],$inputs['email'],$hash,'propietario','activo']);
         $usuario_id = $pdo->lastInsertId();
         $stmt = $pdo->prepare('INSERT INTO afiliados (usuario_id,nombre_negocio,tipo,descripcion,direccion,estado) VALUES (?,?,?,?,?,?)');
         $stmt->execute([$usuario_id,$inputs['nombre_negocio'],$inputs['tipo'],$inputs['descripcion'],$inputs['direccion'],'pendiente']);
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
    } elseif (!ctype_digit($valores['telefono_numero'])) {
        $errores[] = 'El número de celular solo puede contener números.';
    } elseif ($telefonoLen < 7 || $telefonoLen > 20) {
        $errores[] = 'El número de celular debe tener entre 7 y 20 caracteres.';
    }
    if ($municipioOrigenLen > 100) {
        $errores[] = 'El municipio de origen no puede exceder 100 caracteres.';
    }
    if ($clienteModelo->existeEmail($valores['email'])) {
        $errores[] = 'El correo ya está registrado.';
    }
    if ($valores['cedula'] === '') {
        $errores[] = 'La cédula es obligatoria.';
    } else {
        if (!ctype_digit($valores['cedula'])) {
            $errores[] = 'La cédula solo puede contener números.';
        }
        if ($cedulaLen < 6 || $cedulaLen > 20) {
            $errores[] = 'La cédula debe tener entre 6 y 20 caracteres.';
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
