<?php
require_once __DIR__ . '/../modelos/Database.php';

try {
    $pdo = Database::conexion();

    // Inserta los usuarios demo solo si no existen, sin modificar cuentas actuales
    $pass = password_hash('123456', PASSWORD_DEFAULT);
    $usuarios = [
        ['Administrador','admin@turismosl.com',$pass,'admin','activo'],
        ['Propietario Demo','propietario@turismosl.com',$pass,'propietario','activo']
    ];

    $stmt = $pdo->prepare('INSERT IGNORE INTO usuarios (nombre,email,password,rol,estado) VALUES (?,?,?,?,?)');

    $insertados = 0;
    foreach ($usuarios as $u) {
        $stmt->execute($u);
        $insertados += $stmt->rowCount();
    }

    if ($insertados > 0) {
        echo 'Usuarios creados: Admin: admin@turismosl.com / 123456  Propietario: propietario@turismosl.com / 123456';
    } else {
        echo 'Usuarios base ya estaban presentes, no se modificaron.';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Error al sembrar usuarios: ' . $e->getMessage();
}
