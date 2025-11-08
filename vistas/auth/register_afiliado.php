<?php cabecera('Registro de Afiliado'); ?>
<h2>Registro de Afiliado</h2>
<?php if (!empty($msg)) echo '<p style="color:'.($ok? 'green':'crimson').';">'.htmlspecialchars($msg).'</p>'; ?>
<form method="post" action="index.php?ruta=auth/register">
  <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(16)); ?>">
  <label>Nombre del negocio:<br><input type="text" name="nombre_negocio" required></label><br><br>
  <label>Tipo:<br><select name="tipo" required><option value="">Seleccione...</option><option>Finca</option><option>Glamping</option><option>Hotel</option><option>Cabaña</option><option>Ecohotel</option></select></label><br><br>
  <label>Dirección:<br><input type="text" name="direccion" required></label><br><br>
  <label>Descripción:<br><textarea name="descripcion" required></textarea></label><br><br>
  <label>Email:<br><input type="email" name="email" required></label><br><br>
  <label>Contraseña:<br><input type="password" name="password" required></label><br><br>
  <button type="submit">Enviar solicitud</button>
</form>
<?php pie(); ?>