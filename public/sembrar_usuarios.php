<?php
require_once __DIR__ . '/../modelos/Database.php';

try {
    $pdo = Database::conexion();

    // Garantiza la tabla de perfiles de clientes para ambientes sin migraciones completas
    $pdo->exec("CREATE TABLE IF NOT EXISTS clientes_perfiles (\n        id INT AUTO_INCREMENT PRIMARY KEY,\n        usuario_id INT NOT NULL,\n        primer_nombre VARCHAR(50) NOT NULL,\n        segundo_nombre VARCHAR(50),\n        primer_apellido VARCHAR(50) NOT NULL,\n        cedula VARCHAR(40),\n        telefono_codigo VARCHAR(8) DEFAULT '+57',\n        telefono_numero VARCHAR(40) NOT NULL,\n        municipio_origen VARCHAR(100),\n        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n        UNIQUE KEY uniq_cedula_cliente (cedula),\n        CONSTRAINT fk_cliente_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE\n    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Inserta los usuarios demo solo si no existen, sin modificar cuentas actuales
    $pass = password_hash('123456', PASSWORD_DEFAULT);
    $usuarios = [
        ['Administrador','admin@turismosl.com',$pass,'admin','activo'],
        ['Propietario Demo','propietario@turismosl.com',$pass,'propietario','activo'],
        ['Cliente Demo','cliente@turismosl.com',$pass,'cliente','activo']
    ];

    $stmt = $pdo->prepare('INSERT IGNORE INTO usuarios (nombre,email,password,rol,estado) VALUES (?,?,?,?,?)');

    $insertados = 0;
    foreach ($usuarios as $u) {
        $stmt->execute($u);
        $insertados += $stmt->rowCount();
    }

    $clienteId = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $clienteId->execute(['cliente@turismosl.com']);
    $clienteRow = $clienteId->fetch();
    if ($clienteRow) {
        $pdo->prepare('INSERT IGNORE INTO clientes_perfiles (usuario_id, primer_nombre, segundo_nombre, primer_apellido, cedula, telefono_codigo, telefono_numero, municipio_origen) VALUES (?,?,?,?,?,?,?,?)')
            ->execute([$clienteRow['id'], 'Cliente', 'Demo', 'Turismo', '123456789', '+57', '3001234567', 'San Luis']);
    }

    if ($insertados > 0) {
        echo 'Usuarios creados: Admin: admin@turismosl.com / 123456  Propietario: propietario@turismosl.com / 123456  Cliente: cliente@turismosl.com / 123456';
    } else {
        echo 'Usuarios base ya estaban presentes, no se modificaron.';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Error al sembrar usuarios: ' . $e->getMessage();
}
