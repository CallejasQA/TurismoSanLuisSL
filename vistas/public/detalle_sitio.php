<?php cabecera('Detalle de alojamiento'); ?>
<article class="detalle">
  <div class="detalle__media">
    <?php if (!empty($sitio['imagen'])): ?>
      <img src="<?= htmlspecialchars($sitio['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($sitio['nombre']) ?>">
    <?php else: ?>
      <div class="card__image card__image--placeholder">Sin imagen</div>
    <?php endif; ?>
  </div>
  <div class="detalle__body">
    <p class="pill"><?= htmlspecialchars(ucfirst($sitio['estado'])) ?></p>
    <h1><?= htmlspecialchars($sitio['nombre']) ?></h1>
    <p class="muted">Ubicación: <?= htmlspecialchars($sitio['ubicacion'] ?: 'Por definir') ?></p>
    <p class="price price--lg">$<?= number_format($sitio['precio_noche'], 2) ?><span>/noche</span></p>
    <?php if (!empty($sitio['rango_precio'])): ?>
      <p class="muted">Rango: <?= htmlspecialchars($sitio['rango_precio']) ?></p>
    <?php endif; ?>
    <?php if (!empty($sitio['servicios'])): ?>
      <div class="detalle__servicios">
        <h3>Servicios incluidos</h3>
        <div class="service-tags">
          <?php foreach ($sitio['servicios'] as $servicio): ?>
            <span class="service-chip"><?= htmlspecialchars($servicio) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <div class="detalle__descripcion">
      <h3>Descripción</h3>
      <p><?= nl2br(htmlspecialchars($sitio['descripcion'] ?? '')) ?></p>
    </div>
    <div class="detalle__actions">
      <a class="btn" href="index.php">Regresar al inicio</a>
      <a class="btn btn-secondary" href="index.php?ruta=cliente/reservar&id=<?= (int)$sitio['id'] ?>">Solicitar reserva</a>
      <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol']==='propietario'): ?>
        <a class="btn btn-outline" href="index.php?ruta=propietario/sitios">Ir a mis alojamientos</a>
      <?php else: ?>
        <a class="btn btn-outline" href="index.php?ruta=auth/register">Deseo afiliarme</a>
      <?php endif; ?>
    </div>
  </div>
</article>
<?php pie(); ?>
