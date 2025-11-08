<?php
require_once __DIR__ . '/../modelos/Database.php';

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
?>