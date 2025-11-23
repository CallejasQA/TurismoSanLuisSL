<?php
require_once __DIR__ . '/Database.php';

class Cliente {
    private $db;

    public function __construct() {
        $this->db = Database::conexion();
        $this->asegurarTablaClientes();
    }

    public function todos() {
        $sql = "SELECT u.id, u.email, u.estado, u.creado_en, "
             . "cp.primer_nombre, cp.segundo_nombre, cp.primer_apellido, cp.cedula, cp.telefono_codigo, cp.telefono_numero, cp.municipio_origen "
             . "FROM usuarios u JOIN clientes_perfiles cp ON cp.usuario_id = u.id "
             . "WHERE u.rol = 'cliente' ORDER BY u.creado_en DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function encontrar($id) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.email, u.estado, cp.primer_nombre, cp.segundo_nombre, cp.primer_apellido, cp.cedula, "
          . "cp.telefono_codigo, cp.telefono_numero, cp.municipio_origen "
          . "FROM usuarios u JOIN clientes_perfiles cp ON cp.usuario_id = u.id WHERE u.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function crear(array $data) {
        try {
            $this->db->beginTransaction();
            $nombreCompuesto = trim($data['primer_nombre'] . ' ' . $data['primer_apellido']);
            $stmt = $this->db->prepare('INSERT INTO usuarios (nombre,email,password,rol,estado) VALUES (?,?,?,?,?)');
            $stmt->execute([$nombreCompuesto, $data['email'], $data['password_hash'], 'cliente', $data['estado']]);
            $usuarioId = (int) $this->db->lastInsertId();

            $stmtPerfil = $this->db->prepare(
                'INSERT INTO clientes_perfiles (usuario_id, primer_nombre, segundo_nombre, primer_apellido, cedula, telefono_codigo, telefono_numero, municipio_origen) '
              . 'VALUES (?,?,?,?,?,?,?,?)'
            );
            $stmtPerfil->execute([
                $usuarioId,
                $data['primer_nombre'],
                $data['segundo_nombre'],
                $data['primer_apellido'],
                $data['cedula'],
                $data['telefono_codigo'],
                $data['telefono_numero'],
                $data['municipio_origen']
            ]);

            $this->db->commit();
            return $usuarioId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function actualizar($id, array $data) {
        try {
            $this->db->beginTransaction();
            $nombreCompuesto = trim($data['primer_nombre'] . ' ' . $data['primer_apellido']);
            $baseSql = 'UPDATE usuarios SET nombre = ?, email = ?, estado = ?';
            $params = [$nombreCompuesto, $data['email'], $data['estado']];
            if (!empty($data['password_hash'])) {
                $baseSql .= ', password = ?';
                $params[] = $data['password_hash'];
            }
            $baseSql .= ' WHERE id = ?';
            $params[] = $id;
            $stmt = $this->db->prepare($baseSql);
            $stmt->execute($params);

            $stmtPerfil = $this->db->prepare(
                'UPDATE clientes_perfiles SET primer_nombre=?, segundo_nombre=?, primer_apellido=?, cedula=?, telefono_codigo=?, telefono_numero=?, municipio_origen=? '
              . 'WHERE usuario_id=?'
            );
            $stmtPerfil->execute([
                $data['primer_nombre'],
                $data['segundo_nombre'],
                $data['primer_apellido'],
                $data['cedula'],
                $data['telefono_codigo'],
                $data['telefono_numero'],
                $data['municipio_origen'],
                $id
            ]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare('DELETE FROM usuarios WHERE id = ? AND rol = "cliente"');
        return $stmt->execute([$id]);
    }

    public function existeEmail($email, $ignorarId = null) {
        $sql = 'SELECT id FROM usuarios WHERE email = ?';
        $params = [$email];
        if ($ignorarId) {
            $sql .= ' AND id <> ?';
            $params[] = $ignorarId;
        }
        $stmt = $this->db->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }

    public function existeCedula($cedula, $ignorarId = null) {
        if (empty($cedula)) { return false; }
        $sql = 'SELECT usuario_id FROM clientes_perfiles WHERE cedula = ?';
        $params = [$cedula];
        if ($ignorarId) {
            $sql .= ' AND usuario_id <> ?';
            $params[] = $ignorarId;
        }
        $stmt = $this->db->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }

    private function asegurarTablaClientes() {
        $stmt = $this->db->query("SHOW TABLES LIKE 'clientes_perfiles'");
        if ($stmt->fetch()) { return; }

        $sql = "CREATE TABLE IF NOT EXISTS clientes_perfiles ("
             . "id INT AUTO_INCREMENT PRIMARY KEY,"
             . "usuario_id INT NOT NULL,"
             . "primer_nombre VARCHAR(50) NOT NULL,"
             . "segundo_nombre VARCHAR(50),"
             . "primer_apellido VARCHAR(50) NOT NULL,"
             . "cedula VARCHAR(40),"
             . "telefono_codigo VARCHAR(8) DEFAULT '+57',"
             . "telefono_numero VARCHAR(40) NOT NULL,"
             . "municipio_origen VARCHAR(100),"
             . "creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
             . "UNIQUE KEY uniq_cedula_cliente (cedula),"
             . "CONSTRAINT fk_cliente_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE"
             . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->db->exec($sql);
    }
}
?>
