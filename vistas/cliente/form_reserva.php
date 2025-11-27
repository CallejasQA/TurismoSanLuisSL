<?php cabecera('Solicitar reserva'); ?>
<div class="detalle detalle--booking">
  <div class="detalle__media">
    <?php $imagenUrl = assetUrl($sitio['imagen'] ?? ''); ?>
    <?php if ($imagenUrl !== ''): ?>
      <img src="<?= htmlspecialchars($imagenUrl) ?>" alt="Imagen de <?= htmlspecialchars($sitio['nombre']) ?>">
    <?php else: ?>
      <div class="card__image card__image--placeholder">Sin imagen</div>
    <?php endif; ?>
  </div>
  <div class="detalle__body">
    <p class="pill">Reserva</p>
    <h1>Solicitar reserva en <?= htmlspecialchars($sitio['nombre']) ?></h1>
    <p class="muted">Ubicación: <?= htmlspecialchars($sitio['ubicacion'] ?: 'Por definir') ?></p>
    <p class="price price--lg">$<?= number_format($sitio['precio_noche'], 2) ?><span>/noche</span></p>

    <?php if (!empty($errores)): ?>
      <div class="alert alert--error">
        <ul>
          <?php foreach ($errores as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" class="form-grid form-grid--compact">
      <div class="form-group">
        <label>Fecha de inicio *</label>
        <input type="date" name="fecha_inicio" required value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Fecha de fin *</label>
        <input type="date" name="fecha_fin" required value="<?= htmlspecialchars($_POST['fecha_fin'] ?? '') ?>">
      </div>
      <div class="form-actions">
        <button class="btn" type="submit">Enviar solicitud</button>
        <a class="btn btn-outline" href="index.php?ruta=alojamiento/ver&id=<?= (int)$sitio['id'] ?>">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php pie(); ?>
