<?php cabecera('Turismo San Luis'); ?>

<?php if (!empty($slider)): ?>
<section class="highlight-slider" aria-label="Alojamientos destacados">
  <div class="slider__header">
    <div>
      <p class="eyebrow">Recomendados</p>
      <h2>Explora el carrusel de alojamientos</h2>
      <p class="lede">Selección curada por el administrador para inspirar tu próxima visita.</p>
    </div>
    <div class="slider__controls" data-slider-controls>
      <button class="slider__button" data-dir="prev" aria-label="Anterior">‹</button>
      <button class="slider__button" data-dir="next" aria-label="Siguiente">›</button>
    </div>
  </div>
  <div class="slider" data-slider>
    <div class="slider__track" data-track>
      <?php foreach ($slider as $destacado): ?>
        <article class="slide-card">
          <?php if (!empty($destacado['imagen'])): ?>
            <img src="<?= htmlspecialchars($destacado['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($destacado['nombre']) ?>">
          <?php else: ?>
            <div class="slide-card__placeholder">Sin imagen</div>
          <?php endif; ?>
          <div class="slide-card__body">
            <p class="pill pill--ghost">Carrusel</p>
            <h3><?= htmlspecialchars($destacado['nombre']) ?></h3>
            <p class="muted"><?= htmlspecialchars($destacado['ubicacion'] ?: 'Ubicación por definir') ?></p>
            <p class="price price--lg">$<?= number_format($destacado['precio_noche'], 2) ?><span>/noche</span></p>
            <?php if (!empty($destacado['nombre_negocio'])): ?>
              <p class="muted">Operado por <?= htmlspecialchars($destacado['nombre_negocio']) ?></p>
            <?php endif; ?>
            <a class="btn" href="index.php?ruta=alojamiento/ver&id=<?= $destacado['id'] ?>">Ver detalles</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

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
            <?php if (!empty($s['nombre_negocio'])): ?>
              <div class="business-chip" aria-label="Operado por">
                <span class="business-chip__label">Operado por</span>
                <span class="business-chip__name"><?= htmlspecialchars($s['nombre_negocio']) ?></span>
              </div>
            <?php endif; ?>
            <p class="price">$<?= number_format($s['precio_noche'], 2) ?><span>/noche</span></p>
            <p class="excerpt"><?= htmlspecialchars(mb_strimwidth($s['descripcion'] ?? '', 0, 140, '…')) ?></p>
            <?php $servicios = $s['servicios'] ?? []; ?>
            <?php if (!empty($servicios)): ?>
              <div class="service-tags" aria-label="Servicios destacados">
                <?php foreach (array_slice($servicios, 0, 6) as $servicio): ?>
                  <span class="service-chip"><?= htmlspecialchars($servicio) ?></span>
                <?php endforeach; ?>
                <?php if (count($servicios) > 6): ?>
                  <span class="service-chip service-chip--more">+<?= count($servicios) - 6 ?></span>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="card__footer">
            <a class="btn btn-outline" href="index.php?ruta=alojamiento/ver&id=<?= $s['id'] ?>">Ver detalles</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php if (!empty($slider)): ?>
<script>
  (function() {
    const slider = document.querySelector('[data-slider]');
    if (!slider) return;
    const track = slider.querySelector('[data-track]');
    const slides = Array.from(track.children);
    if (slides.length === 0) return;

    let index = 0;
    const update = () => {
      track.style.transform = `translateX(-${index * 100}%)`;
    };

    const goNext = () => { index = (index + 1) % slides.length; update(); };
    const goPrev = () => { index = (index - 1 + slides.length) % slides.length; update(); };

    const controls = document.querySelector('[data-slider-controls]');
    if (controls) {
      const nextBtn = controls.querySelector('[data-dir="next"]');
      const prevBtn = controls.querySelector('[data-dir="prev"]');
      if (nextBtn) nextBtn.addEventListener('click', goNext);
      if (prevBtn) prevBtn.addEventListener('click', goPrev);
    }

    let interval = setInterval(goNext, 7000);
    slider.addEventListener('mouseenter', () => clearInterval(interval));
    slider.addEventListener('mouseleave', () => interval = setInterval(goNext, 7000));
  })();
</script>
<?php endif; ?>
<?php pie(); ?>
