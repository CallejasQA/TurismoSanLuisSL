<?php cabecera('Gestión de comentarios'); ?>
<h2>Gestión de comentarios</h2>
<p>Actualiza o elimina comentarios realizados por los clientes y filtra por fechas, propietario o alojamiento.</p>

<section class="card" style="padding:16px; margin:12px 0;">
  <h3>Filtros</h3>
  <form method="get" action="index.php">
    <input type="hidden" name="ruta" value="admin/valoraciones">
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
      <div>
        <label for="propietario_id">Propietario</label>
        <select id="propietario_id" name="propietario_id">
          <option value="">Todos</option>
          <?php foreach ($propietarios as $p): ?>
            <option value="<?= htmlspecialchars($p['id']) ?>" <?= (!empty($_GET['propietario_id']) && (int)$_GET['propietario_id'] === (int)$p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['email']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="alojamiento_id">Alojamiento</label>
        <select id="alojamiento_id" name="alojamiento_id">
          <option value="">Todos</option>
          <?php foreach ($alojamientos as $a): ?>
            <option value="<?= htmlspecialchars($a['id']) ?>" <?= (!empty($_GET['alojamiento_id']) && (int)$_GET['alojamiento_id'] === (int)$a['id']) ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="fecha_desde">Desde</label>
        <input type="date" id="fecha_desde" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
      </div>
      <div>
        <label for="fecha_hasta">Hasta</label>
        <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
      </div>
      <div style="align-self:flex-end;">
        <button type="submit">Filtrar</button>
      </div>
    </div>
  </form>
</section>

<section style="margin-top:16px;">
  <?php if (empty($valoraciones)): ?>
    <div class="empty-state">No se encontraron comentarios con los filtros seleccionados.</div>
  <?php else: ?>
    <table class="table">
      <tr>
        <th>ID</th>
        <th>Alojamiento</th>
        <th>Propietario</th>
        <th>Cliente</th>
        <th>Calificación</th>
        <th>Comentario / Acciones</th>
        <th>Fecha</th>
      </tr>
      <?php foreach ($valoraciones as $v): ?>
        <tr>
          <td><?= htmlspecialchars($v['id']) ?></td>
          <td><?= htmlspecialchars($v['alojamiento_nombre']) ?></td>
          <td><?= htmlspecialchars($v['propietario_email']) ?></td>
          <td>
            <?= htmlspecialchars(trim(($v['cliente_nombre'] ?? '') . ' ' . ($v['cliente_apellido'] ?? ''))) ?><br>
            <small class="muted"><?= htmlspecialchars($v['cliente_email']) ?></small>
          </td>
          <td><?= (int) $v['estrellas'] ?> ⭐</td>
          <td>
            <form method="post" action="index.php?ruta=admin/valoraciones/actualizar" style="display:flex; flex-direction:column; gap:6px;">
              <input type="hidden" name="id" value="<?= htmlspecialchars($v['id']) ?>">
              <textarea name="comentario" rows="3" required><?= htmlspecialchars($v['comentario']) ?></textarea>
              <div style="display:flex; gap:6px;">
                <button type="submit" class="btn">Actualizar</button>
                <button type="submit" formaction="index.php?ruta=admin/valoraciones/eliminar" class="btn btn-danger" onclick="return confirm('¿Eliminar este comentario?');">Eliminar</button>
              </div>
            </form>
          </td>
          <td><?= htmlspecialchars(date('Y-m-d', strtotime($v['creado_en']))) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</section>
<?php pie(); ?>
