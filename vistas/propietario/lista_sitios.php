<?php cabecera('Mis Alojamientos'); ?>
<h2>Mis Alojamientos</h2>
<p><a class="btn" href="index.php?ruta=propietario/sitios/crear">+ Nuevo alojamiento</a></p>
<table class="table">
<tr><th>ID</th><th>Imagen</th><th>Nombre</th><th>Ubicación</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr>
<?php if (!empty($sitios)): foreach($sitios as $s): ?>
<tr>
<td><?= htmlspecialchars($s['id']) ?></td>
<td><?php if (!empty($s['imagen'])): ?><img src="<?= htmlspecialchars($s['imagen']) ?>" style="width:100px;border-radius:6px;"><?php endif; ?></td>
<td><?= htmlspecialchars($s['nombre']) ?></td>
<td><?= htmlspecialchars($s['ubicacion']) ?></td>
<td>$<?= number_format($s['precio_noche'],2) ?></td>
<td><?= htmlspecialchars($s['estado']) ?></td>
<td>
<a class="btn" href="index.php?ruta=propietario/sitios/editar&id=<?= $s['id'] ?>">Editar</a>
<a class="btn btn-danger" href="index.php?ruta=propietario/sitios/eliminar&id=<?= $s['id'] ?>" onclick="return confirm('Eliminar?')">Eliminar</a>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="7">No hay alojamientos.</td></tr>
<?php endif; ?>
</table>
<?php pie(); ?>
