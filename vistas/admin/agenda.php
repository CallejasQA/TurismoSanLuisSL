<?php cabecera('Agenda de reservas'); ?>
  <?php
    $nombresDias = ['Sun' => 'Dom', 'Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié', 'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb'];

    $totalDias = is_array($agenda ?? null) ? count($agenda) : 0;
    $diasConReservas = 0;
    $totalReservas = 0;

    if (!empty($agenda)) {
      foreach ($agenda as $items) {
        if (!empty($items)) {
          $diasConReservas++;
          $totalReservas += count($items);
        }
      }
    }

    $diasSinReservas = max($totalDias - $diasConReservas, 0);
    $ocupacion = $totalDias > 0 ? round(($diasConReservas / $totalDias) * 100) : 0;
  ?>

  <section class="agenda-wrapper">
    <header class="agenda-header">
      <h1>Agenda de ocupación</h1>
      <p>Visualiza tus reservas por mes, alojamiento y propietario.</p>
    </header>

    <form class="agenda-filters" method="get" action="index.php" id="formFiltrosAgenda">
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

    <section class="agenda-summary">
      <div class="summary-card">
        <span class="summary-label">Días con reservas</span>
        <span class="summary-value"><?= (int)$diasConReservas ?></span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Días sin reservas</span>
        <span class="summary-value"><?= (int)$diasSinReservas ?></span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Ocupación del mes</span>
        <span class="summary-value"><?= (int)$ocupacion ?>%</span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Reservas totales</span>
        <span class="summary-value"><?= (int)$totalReservas ?></span>
      </div>
    </section>

    <div class="agenda-view-controls">
      <label class="toggle-reserved"><input type="checkbox" id="toggleSoloReservas"> Mostrar sólo días con reservas</label>
    </div>

    <?php if (empty($hayReservas)): ?>
      <div class="empty-state empty-state--agenda">
        <strong>Sin datos.</strong> Ajusta los filtros para ver la agenda.
      </div>
    <?php else: ?>
      <section class="agenda-grid">
        <?php foreach ($agenda as $dia => $items): $timestamp = strtotime($dia); ?>
          <article class="day-card <?= empty($items) ? 'day-card--empty' : 'day-card--reserved' ?>">
            <header class="day-card-header">
              <span class="day-number"><?= date('d', $timestamp) ?></span>
              <span class="day-name"><?= $nombresDias[date('D', $timestamp)] ?? date('D', $timestamp) ?></span>
            </header>

            <?php if (empty($items)): ?>
              <p class="no-reservations">Sin reservas</p>
            <?php else: ?>
              <?php foreach ($items as $r): ?>
                <?php
                  $estado = $r['estado'] ?? 'pendiente';
                  $badgeClass = 'badge-' . strtolower($estado);
                  if ($estado === 'confirmada' || $estado === 'finalizada') {
                    $badgeClass = 'badge-finalizada';
                  } elseif ($estado === 'cancelada') {
                    $badgeClass = 'badge-cancelada';
                  } elseif ($estado === 'pendiente') {
                    $badgeClass = 'badge-pendiente';
                  }
                ?>
                <div class="reservation-item">
                  <div class="reservation-main">
                    <h3 class="reservation-alojamiento"><?= htmlspecialchars($r['alojamiento']) ?></h3>
                    <span class="reservation-propietario"><?= htmlspecialchars($r['propietario_email'] ?? '') ?></span>
                  </div>
                  <p class="reservation-guest">Huésped: <?= htmlspecialchars(trim(($r['cliente_nombre'] ?? '') . ' ' . ($r['cliente_apellido'] ?? ''))) ?></p>
                  <p class="reservation-dates">Del <?= htmlspecialchars($r['fecha_inicio']) ?> al <?= htmlspecialchars($r['fecha_fin']) ?></p>
                  <span class="badge <?= htmlspecialchars($badgeClass) ?>"><?= htmlspecialchars(ucfirst($estado)) ?></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </section>

  <style>
    .agenda-wrapper { max-width: 1200px; margin: 0 auto; padding: 24px 16px 40px; }
    .agenda-header h1 { font-size: 1.6rem; margin-bottom: 4px; }
    .agenda-header p { font-size: 0.9rem; color: #6b7280; margin: 0; }

    .agenda-filters { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin: 16px 0; padding: 12px; background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    .filter-group { display: flex; flex-direction: column; min-width: 180px; gap: 4px; }
    .filter-group label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
    .filter-group select,
    .filter-group input[type="month"] { padding: 8px 10px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 0.85rem; }
    .filter-actions { display: flex; gap: 8px; align-items: center; }
    .btn-primary { background: #16a34a; color: #fff; border: none; padding: 9px 16px; border-radius: 10px; font-weight: 600; cursor: pointer; }
    .btn-secondary { background: #e5f2ea; color: #166534; border: 1px solid #b4dfc2; padding: 9px 14px; border-radius: 10px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #15803d; }
    .btn-secondary:hover { background: #d7ecdf; }

    .agenda-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .summary-card { background: #ffffff; border-radius: 12px; padding: 10px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    .summary-label { font-size: 0.75rem; color: #6b7280; }
    .summary-value { font-size: 1.2rem; font-weight: 700; display: block; margin-top: 2px; color: #111827; }

    .agenda-view-controls { display: flex; justify-content: flex-end; margin: 12px 4px; }
    .toggle-reserved { font-size: 0.9rem; color: #374151; display: flex; align-items: center; gap: 6px; }

    .agenda-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px; }
    .day-card { background: #ffffff; border-radius: 14px; padding: 12px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 6px; }
    .day-card-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 4px; }
    .day-number { font-size: 1.4rem; font-weight: 700; color: #111827; }
    .day-name { font-size: 0.85rem; color: #6b7280; text-transform: capitalize; }
    .no-reservations { font-size: 0.85rem; color: #9ca3af; margin: 0; }
    .reservation-item { border-top: 1px solid #e5e7eb; padding-top: 6px; margin-top: 4px; display: flex; flex-direction: column; gap: 2px; }
    .reservation-main { display: flex; justify-content: space-between; align-items: center; gap: 6px; }
    .reservation-alojamiento { font-size: 0.95rem; margin: 0; color: #111827; }
    .reservation-propietario { font-size: 0.75rem; color: #6b7280; }
    .reservation-guest,
    .reservation-dates { font-size: 0.8rem; margin: 0; color: #4b5563; }

    .badge { padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; align-self: flex-start; }
    .badge-pendiente { background: #fff7e6; color: #d97706; }
    .badge-finalizada,
    .badge-confirmada { background: #e6f4ea; color: #166534; }
    .badge-cancelada { background: #fee2e2; color: #b91c1c; }

    .empty-state--agenda { background: #ffffff; border-radius: 14px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); text-align: center; color: #374151; }

    @media (max-width: 768px) {
      .agenda-filters { align-items: stretch; }
      .filter-actions { width: 100%; display: flex; gap: 8px; }
      .agenda-view-controls { justify-content: flex-start; margin: 12px 0; }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const btnLimpiar = document.getElementById('btnLimpiarFiltros');
      const form = document.getElementById('formFiltrosAgenda');
      const toggleSoloReservas = document.getElementById('toggleSoloReservas');

      if (btnLimpiar && form) {
        btnLimpiar.addEventListener('click', function () {
          const selects = form.querySelectorAll('select');
          selects.forEach(function (select) {
            select.value = '';
          });
          const mesInput = document.getElementById('mes');
          if (mesInput) {
            mesInput.value = '';
          }
          form.submit();
        });
      }

      if (toggleSoloReservas) {
        toggleSoloReservas.addEventListener('change', function (event) {
          const soloReservas = event.target.checked;
          document.querySelectorAll('.day-card--empty').forEach(function (card) {
            card.style.display = soloReservas ? 'none' : '';
          });
        });
      }
    });
  </script>
<?php pie(); ?>
