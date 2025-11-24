<?php cabecera('Registro de Cliente'); ?>
<h2>Crea tu cuenta</h2>
<?php if (!empty($msg)) echo '<p style="color:'.($ok? 'green':'crimson').';">'.htmlspecialchars($msg).'</p>'; ?>
<form method="post" action="index.php?ruta=auth/register-cliente">
  <label>Primer nombre:<br><input type="text" name="primer_nombre" value="<?php echo htmlspecialchars($_POST['primer_nombre'] ?? ''); ?>" required></label><br><br>
  <label>Segundo nombre:<br><input type="text" name="segundo_nombre" value="<?php echo htmlspecialchars($_POST['segundo_nombre'] ?? ''); ?>"></label><br><br>
  <label>Primer apellido:<br><input type="text" name="primer_apellido" value="<?php echo htmlspecialchars($_POST['primer_apellido'] ?? ''); ?>" required></label><br><br>
  <label>Cédula:<br><input type="text" name="cedula" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>"></label><br><br>
  <label>Celular:<br>
    <input type="text" name="telefono_codigo" value="<?php echo htmlspecialchars($_POST['telefono_codigo'] ?? '+57'); ?>" style="width:60px" required>
    <input type="text" name="telefono_numero" value="<?php echo htmlspecialchars($_POST['telefono_numero'] ?? ''); ?>" style="width:140px" required>
  </label><br><br>
  <label>Correo electrónico:<br><input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required></label><br><br>
  <label>Municipio de origen:<br><input type="text" name="municipio_origen" value="<?php echo htmlspecialchars($_POST['municipio_origen'] ?? ''); ?>"></label><br><br>
  <label>Contraseña:<br><input type="password" name="password" required></label><br><br>
  <button type="submit">Registrarme</button>
</form>
<p><a href="index.php?ruta=auth/login">¿Ya tienes cuenta? Inicia sesión</a></p>
<?php pie(); ?>
