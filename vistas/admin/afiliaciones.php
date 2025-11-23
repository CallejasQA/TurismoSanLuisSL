<?php cabecera('Afiliaciones pendientes'); ?>
<h2>Afiliaciones registradas</h2>
<?php if (empty($pendientes)): ?>
  <p>No hay solicitudes pendientes.</p>
<?php else: ?>
<table class="table">
<tr><th>ID</th><th>Negocio</th><th>Email</th><th>Tipo</th><th>Dirección</th><th>Estado</th><th>Acciones</th></tr>
<?php foreach($pendientes as $p): ?>
<tr>
<td><?= htmlspecialchars($p['id']) ?></td>
<td><?= htmlspecialchars($p['nombre_negocio']) ?></td>
<td><?= htmlspecialchars($p['correo_propietario'] ?? '') ?></td>
<td><?= htmlspecialchars($p['tipo']) ?></td>
<td><?= htmlspecialchars($p['direccion']) ?></td>
<td><?= htmlspecialchars($p['estado']) ?></td>
<td>
<?php if ($p['estado']==='pendiente'): ?>
  <a class="btn" href="index.php?ruta=admin/afiliaciones/aprobar&id=<?= $p['id'] ?>">Aprobar</a>
  <a class="btn btn-danger" href="index.php?ruta=admin/afiliaciones/rechazar&id=<?= $p['id'] ?>">Rechazar</a>
<?php else: ?>
  <em>—</em>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php pie(); ?>
