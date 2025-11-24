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
    <div class="detalle__valoraciones">
      <h3>Experiencia de huéspedes</h3>
      <?php if (!empty($mensajeExito)): ?>
        <p class="pill pill--success"><?= htmlspecialchars($mensajeExito) ?></p>
      <?php elseif (!empty($mensajeError)): ?>
        <p class="pill pill--danger"><?= htmlspecialchars($mensajeError) ?></p>
      <?php endif; ?>
      <?php if (($promedioValoraciones['total'] ?? 0) > 0): ?>
        <p><strong><?= htmlspecialchars($promedioValoraciones['promedio']) ?>/5</strong> ⭐ (<?= (int) $promedioValoraciones['total'] ?> reseñas)</p>
      <?php else: ?>
        <p class="muted">Aún no hay calificaciones para este alojamiento.</p>
      <?php endif; ?>
      <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['usuario_rol'] ?? '') === 'cliente'): ?>
        <div id="calificacion" class="card" style="margin-top:1rem;">
          <div class="card__body">
            <?php if ($valoracionCliente): ?>
              <p><strong>Tu calificación:</strong> <?= (int)$valoracionCliente['estrellas'] ?> ⭐</p>
              <?php if (!empty($valoracionCliente['comentario'])): ?>
                <p class="muted">"<?= nl2br(htmlspecialchars($valoracionCliente['comentario'])) ?>"</p>
              <?php endif; ?>
              <p class="muted">Gracias por compartir tu experiencia.</p>
            <?php elseif ($puedeValorar && $reservaFinalizada): ?>
              <form method="post" action="index.php?ruta=cliente/calificar" class="form">
                <input type="hidden" name="reserva_id" value="<?= (int)$reservaFinalizada['id'] ?>">
                <div class="form-group">
                  <label for="estrellas">Puntuación</label>
                  <div class="rating-input">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                      <label style="margin-right:0.5rem;">
                        <input type="radio" name="estrellas" value="<?= $i ?>" <?= $i===5 ? 'checked' : '' ?>> <?= $i ?> ⭐
                      </label>
                    <?php endfor; ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="comentario">Comentario</label>
                  <textarea id="comentario" name="comentario" rows="3" placeholder="Cuéntanos cómo fue tu estadía"></textarea>
                </div>
                <button type="submit" class="btn">Enviar calificación</button>
              </form>
            <?php else: ?>
              <p class="muted">Solo podrás calificar cuando tu reserva haya sido marcada como <strong>Finalizada</strong>.</p>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="detalle__actions">
      <a class="btn" href="index.php">Regresar al inicio</a>
      <a class="btn btn-secondary" href="index.php?ruta=cliente/reservar&id=<?= (int)$sitio['id'] ?>">Solicitar reserva</a>
      <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol']==='propietario'): ?>
        <a class="btn btn-outline" href="index.php?ruta=propietario/sitios">Ir a mis alojamientos</a>
      <?php endif; ?>
    </div>
  </div>
</article>
<?php pie(); ?>
