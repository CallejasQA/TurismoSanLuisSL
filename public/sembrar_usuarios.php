<?php
require_once __DIR__ . '/../modelos/Database.php';
$pdo = Database::conexion();
$stmt = $pdo->query("SELECT COUNT(*) as c FROM usuarios");
$row = $stmt->fetch();
if ($row && $row['c']>0) { echo 'Ya existen usuarios. Elimina este archivo si deseas reseed.'; exit; }
$pass = password_hash('123456', PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol,estado) VALUES (?,?,?,?,?)')->execute(['Administrador','admin@turismosl.com',$pass,'admin','activo']);
$pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol,estado) VALUES (?,?,?,?,?)')->execute(['Propietario Demo','propietario@turismosl.com',$pass,'propietario','activo']);
echo 'Usuarios sembrados. Admin: admin@turismosl.com / 123456  Propietario: propietario@turismosl.com / 123456';
