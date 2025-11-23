<?php cabecera('Mis reservas'); ?>
<div class="toolbar">
  <div>
    <h1>Mis reservas</h1>
    <p class="muted">Consulta el estado de tus solicitudes de alojamiento.</p>
  </div>
</div>
<div class="card">
  <div class="card__body">
    <?php if (empty($reservas)): ?>
      <p>Aún no tienes reservas registradas.</p>
    <?php else: ?>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>Alojamiento</th>
              <th>Ubicación</th>
              <th>Fechas</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Solicitada</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservas as $r): ?>
              <tr>
                <td><a href="index.php?ruta=alojamiento/ver&id=<?= (int)$r['alojamiento_id'] ?>"><?= htmlspecialchars($r['alojamiento']) ?></a></td>
                <td><?= htmlspecialchars($r['ubicacion'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['fecha_inicio']) ?> → <?= htmlspecialchars($r['fecha_fin']) ?></td>
                <td>$<?= number_format($r['total'],2) ?></td>
                <td><span class="pill pill--<?= $r['estado']==='confirmada' ? 'success' : ($r['estado']==='cancelada'?'danger':'warning') ?>"><?= htmlspecialchars(ucfirst($r['estado'])) ?></span></td>
                <td><?= htmlspecialchars(date('Y-m-d', strtotime($r['creado_en']))) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php pie(); ?>
