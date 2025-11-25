<?php cabecera($esEdicion ? 'Editar cliente' : 'Nuevo cliente'); ?>
<div class="toolbar">
  <div>
    <h1><?= $esEdicion ? 'Editar cliente' : 'Nuevo cliente' ?></h1>
    <p class="muted">Completa los datos del huésped. Los campos marcados con * son obligatorios.</p>
  </div>
  <div class="toolbar__actions">
    <a class="btn btn-outline" href="index.php?ruta=admin/clientes">Volver al listado</a>
  </div>
</div>

<div class="card">
  <div class="card__body">
    <?php if (!empty($errores)): ?>
      <div class="alert alert--error">
        <ul>
          <?php foreach ($errores as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" class="form-grid">
      <div class="form-group">
        <label>Primer nombre *</label>
        <input type="text" name="primer_nombre" minlength="3" maxlength="20" value="<?= htmlspecialchars($valores['primer_nombre']) ?>" required>
      </div>
      <div class="form-group">
        <label>Segundo nombre</label>
        <input type="text" name="segundo_nombre" maxlength="20" value="<?= htmlspecialchars($valores['segundo_nombre']) ?>">
      </div>
      <div class="form-group">
        <label>Primer apellido *</label>
        <input type="text" name="primer_apellido" minlength="3" maxlength="20" value="<?= htmlspecialchars($valores['primer_apellido']) ?>" required>
      </div>
      <div class="form-group">
        <label>Cédula</label>
        <input type="text" name="cedula" value="<?= htmlspecialchars($valores['cedula']) ?>" maxlength="20" inputmode="numeric" pattern="\d*">
      </div>
      <div class="form-group">
        <label>Celular *</label>
        <div class="phone-field">
          <select name="telefono_codigo">
            <option value="+57" <?= $valores['telefono_codigo']==='+57'?'selected':'' ?>>🇨🇴 +57</option>
            <option value="+58" <?= $valores['telefono_codigo']==='+58'?'selected':'' ?>>🇻🇪 +58</option>
            <option value="+1" <?= $valores['telefono_codigo']==='+1'?'selected':'' ?>>🇺🇸 +1</option>
            <option value="+34" <?= $valores['telefono_codigo']==='+34'?'selected':'' ?>>🇪🇸 +34</option>
          </select>
          <input type="tel" name="telefono_numero" value="<?= htmlspecialchars($valores['telefono_numero']) ?>" placeholder="3001234567" required maxlength="20" inputmode="numeric" pattern="\d*">
        </div>
      </div>
      <div class="form-group">
        <label>Correo electrónico *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($valores['email']) ?>" required>
      </div>
      <div class="form-group">
        <label>Municipio de origen</label>
        <input type="text" name="municipio_origen" value="<?= htmlspecialchars($valores['municipio_origen']) ?>">
      </div>
      <div class="form-group">
        <label>Contraseña <?= $esEdicion ? '(dejar en blanco para conservar)' : '*' ?></label>
        <input type="password" name="password" minlength="6" placeholder="<?= $esEdicion ? 'Solo si deseas cambiarla' : 'Mínimo 6 caracteres' ?>">
      </div>
      <div class="form-group">
        <label>Estado</label>
        <select name="estado">
          <option value="activo" <?= $valores['estado']==='activo'?'selected':'' ?>>Activo</option>
          <option value="inactivo" <?= $valores['estado']==='inactivo'?'selected':'' ?>>Inactivo</option>
        </select>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit">Guardar</button>
        <a class="btn btn-outline" href="index.php?ruta=admin/clientes">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php pie(); ?>
