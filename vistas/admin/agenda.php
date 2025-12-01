<?php cabecera('Agenda de reservas'); ?>
  <div class="toolbar">
    <div>
      <h1>Agenda de reservas</h1>
      <p class="muted">Filtra por mes y propietario para ver la ocupación consolidada.</p>
    </div>
    <form class="toolbar__actions" method="get" action="index.php">
      <input type="hidden" name="ruta" value="admin/agenda">
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

  <?php
    $nombresDias = ['Sun' => 'Dom', 'Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié', 'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb'];
  ?>

  <?php if (empty($hayReservas)): ?>
    <div class="empty-state">
      <strong>Sin datos.</strong> Ajusta los filtros para ver la agenda.
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
                <div class="agenda-card__meta">Propietario: <?= htmlspecialchars($r['propietario_email'] ?? '') ?></div>
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
