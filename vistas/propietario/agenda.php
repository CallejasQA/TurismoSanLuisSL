<?php cabecera('Mi agenda'); ?>
  <div class="toolbar">
    <div>
      <h1>Agenda de ocupación</h1>
      <p class="muted">Visualiza tus reservas por día y filtra por alojamiento y mes.</p>
    </div>
    <form class="toolbar__actions" method="get" action="index.php">
      <input type="hidden" name="ruta" value="propietario/agenda">
      <select name="alojamiento">
        <option value="">Todos mis alojamientos</option>
        <?php foreach ($alojamientos as $a): $sel = ($alojamientoSeleccionado ?? null) !== null && (int)$alojamientoSeleccionado === (int)$a['id']; ?>
          <option value="<?= (int)$a['id'] ?>" <?= $sel ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="month" name="mes" value="<?= htmlspecialchars($mesSeleccionado ?? '') ?>">
      <button type="submit">Filtrar</button>
    </form>
  </div>

  <?php
    $nombresDias = ['Sun' => 'Dom', 'Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié', 'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb'];
  ?>

  <?php if (empty($hayReservas)): ?>
    <div class="empty-state">
      <strong>Sin datos.</strong> Aún no hay reservas para el filtro aplicado.
    </div>
  <?php else: ?>
    <div class="agenda-grid">
      <?php foreach ($agenda as $dia => $items): $timestamp = strtotime($dia); ?>
        <div class="agenda-day">
          <div class="agenda-day__header">
            <span class="agenda-day__date"><?= date('d', $timestamp) ?></span>
            <span class="agenda-day__weekday"><?= $nombresDias[date('D', $timestamp)] ?? date('D', $timestamp) ?></span>
          </div>
          <?php if (empty($items)): ?>
            <div class="agenda-day__empty">Sin reservas</div>
          <?php else: ?>
            <?php foreach ($items as $r): ?>
              <?php
                $estado = $r['estado'] ?? 'pendiente';
                $pillClass = ($estado === 'confirmada' || $estado === 'finalizada') ? 'pill--success' : (($estado === 'cancelada') ? 'pill--danger' : 'pill--warning');
              ?>
              <div class="agenda-card">
                <div class="agenda-card__title"><?= htmlspecialchars($r['alojamiento']) ?></div>
                <div class="agenda-card__meta">Huésped: <?= htmlspecialchars(trim(($r['cliente_nombre'] ?? '') . ' ' . ($r['cliente_apellido'] ?? ''))) ?></div>
                <div class="agenda-card__range">Del <?= htmlspecialchars($r['fecha_inicio']) ?> al <?= htmlspecialchars($r['fecha_fin']) ?></div>
                <span class="pill <?= htmlspecialchars($pillClass) ?> agenda-card__status"><?= htmlspecialchars(ucfirst($estado)) ?></span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php pie(); ?>
