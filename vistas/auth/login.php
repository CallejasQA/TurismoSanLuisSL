<?php cabecera('Ingresar'); ?>
<h2>Iniciar sesión</h2>
<?php if (!empty($error)) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
<form method="post" action="index.php?ruta=auth/login">
  <label>Email:<br><input type="email" name="email" required></label><br><br>
  <label>Contraseña:<br><input type="password" name="password" required></label><br><br>
  <button type="submit">Ingresar</button>
</form>
<p><a href="index.php?ruta=auth/register">¿Eres propietario? Afíliate</a></p>
<p><a href="index.php?ruta=auth/register-cliente">¿Aún no tienes cuenta? Regístrate como cliente</a></p>
<?php pie(); ?>
