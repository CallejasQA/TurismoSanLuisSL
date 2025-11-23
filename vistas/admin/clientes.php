<?php cabecera('Clientes'); ?>
<div class="toolbar">
  <div>
    <h1>Clientes</h1>
    <p class="muted">Gestiona los huéspedes que pueden reservar en la plataforma.</p>
  </div>
  <div class="toolbar__actions">
    <a class="btn" href="index.php?ruta=admin/clientes/crear">Nuevo cliente</a>
  </div>
</div>

<div class="card">
  <div class="card__body">
    <?php if (empty($clientes)): ?>
      <p>No hay clientes registrados aún.</p>
    <?php else: ?>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Celular</th>
              <th>Cédula</th>
              <th>Municipio</th>
              <th>Estado</th>
              <th>Creado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clientes as $c): ?>
              <tr>
                <td><?= htmlspecialchars(trim($c['primer_nombre'].' '.($c['segundo_nombre'] ?? '').' '.$c['primer_apellido'])) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['telefono_codigo'].' '.$c['telefono_numero']) ?></td>
                <td><?= htmlspecialchars($c['cedula'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['municipio_origen'] ?? '') ?></td>
                <td><span class="pill pill--<?= $c['estado']==='activo' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($c['estado'])) ?></span></td>
                <td><?= htmlspecialchars(date('Y-m-d', strtotime($c['creado_en']))) ?></td>
                <td class="table__actions">
                  <a class="btn btn-small" href="index.php?ruta=admin/clientes/editar&id=<?= (int)$c['id'] ?>">Editar</a>
                  <form method="post" action="index.php?ruta=admin/clientes/eliminar" onsubmit="return confirm('¿Eliminar cliente?');">
                    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                    <button class="btn btn-small btn-danger" type="submit">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php pie(); ?>
