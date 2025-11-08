<?php cabecera(isset($sitio)? 'Editar alojamiento':'Nuevo alojamiento'); ?>
<h2><?= isset($sitio)? 'Editar' : 'Nuevo' ?> alojamiento</h2>
<form method="post" enctype="multipart/form-data" action="">
  <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(16)); ?>">
  <label>Nombre:<br><input type="text" name="nombre" value="<?= htmlspecialchars($sitio['nombre'] ?? '') ?>" required></label><br><br>
  <label>Descripción:<br><textarea name="descripcion"><?= htmlspecialchars($sitio['descripcion'] ?? '') ?></textarea></label><br><br>
  <label>Ubicación:<br><input type="text" name="ubicacion" value="<?= htmlspecialchars($sitio['ubicacion'] ?? '') ?>"></label><br><br>
  <label>Precio por noche:<br><input type="number" step="0.01" name="precio_noche" value="<?= htmlspecialchars($sitio['precio_noche'] ?? '') ?>" required></label><br><br>
  <label>Rango de precio:<br><input type="text" name="rango_precio" value="<?= htmlspecialchars($sitio['rango_precio'] ?? '') ?>"></label><br><br>
  <label>Imagen:<br><input type="file" name="imagen" accept="image/*"></label><br><br>
  <button type="submit">Guardar</button>
</form>
<p><a href="index.php?ruta=propietario/sitios">Cancelar</a></p>
<?php pie(); ?>