<?php cabecera('Reservas'); ?>
  <div class="toolbar">
    <div>
      <h1>Reservas</h1>
      <p class="muted">Gestiona todas las reservas, filtra por mes y propietario.</p>
    </div>
    <form class="toolbar__actions" method="get" action="index.php">
      <input type="hidden" name="ruta" value="admin/reservas">
      <input type="month" name="mes" value="<?= htmlspecialchars($mesSeleccionado ?? '') ?>">
      <select name="propietario">
        <option value="">Todos los propietarios</option>
        <?php foreach ($propietarios as $p): $sel = ($propietarioSeleccionado ?? null) !== null && (int)$propietarioSeleccionado === (int)$p['id']; ?>
          <option value="<?= (int)$p['id'] ?>" <?= $sel ? 'selected' : '' ?>><?= htmlspecialchars($p['email']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Filtrar</button>
    </form>
  </div>

  <?php if (empty($reservas)): ?>
    <div class="empty-state">
      <strong>Sin reservas.</strong> Ajusta los filtros para ver resultados.
    </div>
  <?php else: ?>
    <div class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>Alojamiento</th>
            <th>Propietario</th>
            <th>Cliente</th>
            <th>Fechas</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservas as $r): ?>
            <tr>
              <td>
                <div><strong><?= htmlspecialchars($r['alojamiento']) ?></strong></div>
                <div class="muted"><?= htmlspecialchars($r['ubicacion']) ?></div>
              </td>
              <td><?= htmlspecialchars($r['propietario_email']) ?></td>
              <td>
                <div><strong><?= htmlspecialchars(trim(($r['cliente_nombre'] ?? '') . ' ' . ($r['cliente_apellido'] ?? ''))) ?></strong></div>
                <div class="muted"><?= htmlspecialchars($r['cliente_email']) ?></div>
              </td>
              <td><?= htmlspecialchars($r['fecha_inicio']) ?> → <?= htmlspecialchars($r['fecha_fin']) ?></td>
              <td>$<?= number_format($r['total'], 0, ',', '.') ?></td>
              <td>
                <?php $class = $r['estado']==='confirmada' ? 'pill--success' : ($r['estado']==='cancelada' ? 'pill--danger' : 'pill--warning'); ?>
                <span class="pill <?= $class ?>"><?= ucfirst($r['estado']) ?></span>
              </td>
              <td>
                <form method="post" action="index.php?ruta=admin/reservas/estado" class="table__actions">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <select name="estado">
<<<<<<< ours
                    <?php foreach (['pendiente'=>'Pendiente','confirmada'=>'Confirmada','finalizada'=>'Finalizada','cancelada'=>'Cancelada'] as $key=>$label): ?>
                      <option value="<?= $key ?>" <?= ($estado===$key) ? 'selected' : '' ?>><?= $label ?></option>
=======
                    <?php foreach (['pendiente'=>'Pendiente','confirmada'=>'Confirmada','cancelada'=>'Cancelada'] as $key=>$label): ?>
                      <option value="<?= $key ?>" <?= $r['estado']===$key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn btn-small">Actualizar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php pie(); ?>
