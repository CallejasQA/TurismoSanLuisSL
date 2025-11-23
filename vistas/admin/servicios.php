<?php cabecera('Servicios de alojamientos'); ?>
<h2>Servicios de alojamientos</h2>
<p>Administra el catálogo de servicios disponibles para los alojamientos.</p>

<section class="card" style="padding:16px; margin-top:12px;">
  <h3>Agregar nuevo servicio</h3>
  <form method="post" action="index.php?ruta=admin/servicios/crear">
    <div style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
      <div style="flex:1 1 260px;">
        <label for="nombre">Nombre del servicio</label>
        <input type="text" id="nombre" name="nombre" required placeholder="Ej. WiFi premium">
      </div>
      <div>
        <button type="submit">Agregar</button>
      </div>
    </div>
  </form>
</section>

<section style="margin-top:18px;">
  <h3>Listado de servicios</h3>
  <?php if (empty($servicios)): ?>
    <div class="empty-state">Aún no hay servicios registrados.</div>
  <?php else: ?>
  <table class="table">
    <tr><th>ID</th><th>Nombre</th><th style="width:260px;">Acciones</th></tr>
    <?php foreach ($servicios as $servicio): ?>
    <tr>
      <td><?= htmlspecialchars($servicio['id']) ?></td>
      <td>
        <form method="post" action="index.php?ruta=admin/servicios/actualizar" style="display:flex; gap:8px; align-items:center;">
          <input type="hidden" name="id" value="<?= htmlspecialchars($servicio['id']) ?>">
          <input type="text" name="nombre" value="<?= htmlspecialchars($servicio['nombre']) ?>" required>
      </td>
      <td>
          <button type="submit" class="btn">Guardar</button>
          <button type="submit" formaction="index.php?ruta=admin/servicios/eliminar" class="btn btn-danger" onclick="return confirm('¿Eliminar este servicio?');">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</section>
<?php pie(); ?>
