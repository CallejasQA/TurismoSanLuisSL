<?php cabecera('Turismo San Luis'); ?>
<section class="hero">
  <div class="hero__content">
    <p class="eyebrow">Explora San Luis</p>
    <h1>Encuentra alojamientos listos para recibirte</h1>
    <p class="lede">Conecta con hospedajes aprobados por el administrador y administra tus propios sitios desde el panel de propietario.</p>
    <div class="hero__actions">
      <a class="btn" href="index.php?ruta=auth/register">Quiero afiliar mi hospedaje</a>
      <a class="btn btn-secondary" href="#alojamientos">Ver alojamientos</a>
    </div>
  </div>
</section>
<section id="alojamientos">
  <div class="section-header">
    <h2>Alojamientos disponibles</h2>
    <p>Listado de hospedajes aprobados o activos en la plataforma.</p>
  </div>
  <?php if (empty($sitios)): ?>
    <div class="empty-state">
      <p>Aún no hay alojamientos aprobados. ¡Sé el primero en publicar!</p>
    </div>
  <?php else: ?>
    <div class="card-grid">
      <?php foreach ($sitios as $s): ?>
        <article class="card">
          <?php if (!empty($s['imagen'])): ?>
            <img class="card__image" src="<?= htmlspecialchars($s['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($s['nombre']) ?>">
          <?php else: ?>
            <div class="card__image card__image--placeholder">Sin imagen</div>
          <?php endif; ?>
          <div class="card__body">
            <p class="pill"><?= htmlspecialchars(ucfirst($s['estado'])) ?></p>
            <h3><?= htmlspecialchars($s['nombre']) ?></h3>
            <p class="muted"><?= htmlspecialchars($s['ubicacion'] ?: 'Ubicación por definir') ?></p>
            <p class="price">$<?= number_format($s['precio_noche'], 2) ?><span>/noche</span></p>
            <p class="excerpt"><?= htmlspecialchars(mb_strimwidth($s['descripcion'] ?? '', 0, 140, '…')) ?></p>
          </div>
          <div class="card__footer">
            <a class="btn btn-outline" href="index.php?ruta=alojamiento/ver&id=<?= $s['id'] ?>">Ver detalles</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php pie(); ?>
