<?php cabecera('Alojamientos'); ?>
<h2>Alojamientos registrados</h2>
<?php if (empty($alojamientos)): ?>
  <p>No hay alojamientos cargados.</p>
<?php else: ?>
<table class="table">
  <tr><th>ID</th><th>Nombre</th><th>Propietario</th><th>Estado</th><th>Creado</th><th>Acciones</th></tr>
  <?php foreach($alojamientos as $a): ?>
  <tr>
    <td><?= htmlspecialchars($a['id']) ?></td>
    <td><?= htmlspecialchars($a['nombre']) ?></td>
    <td><?= htmlspecialchars($a['correo_propietario'] ?? '') ?></td>
    <td><?= htmlspecialchars($a['estado']) ?></td>
    <td><?= htmlspecialchars($a['creado_en'] ?? '') ?></td>
    <td>
      <?php if ($a['estado']==='pendiente' || $a['estado']==='rechazado'): ?>
        <a class="btn" href="index.php?ruta=admin/alojamientos/aprobar&id=<?= $a['id'] ?>">Aprobar</a>
      <?php endif; ?>
      <?php if ($a['estado']!=='rechazado'): ?>
        <a class="btn btn-danger" href="index.php?ruta=admin/alojamientos/rechazar&id=<?= $a['id'] ?>">Rechazar</a>
      <?php endif; ?>
      <?php if ($a['estado']==='aprobado' || $a['estado']==='pendiente'): ?>
        <a class="btn" href="index.php?ruta=admin/alojamientos/activar&id=<?= $a['id'] ?>">Activar</a>
      <?php endif; ?>
      <?php if ($a['estado']==='activo'): ?>
        <a class="btn btn-danger" href="index.php?ruta=admin/alojamientos/desactivar&id=<?= $a['id'] ?>">Desactivar</a>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
<?php pie(); ?>
