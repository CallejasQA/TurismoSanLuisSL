<?php cabecera('Configuración', [], 'admin-page'); ?>
<h2>Imagen de fondo</h2>
<p>Administra la imagen que se muestra en las páginas de autenticación.</p>
<form action="index.php?ruta=admin/configuracion/guardar" method="post" enctype="multipart/form-data" class="config-form">
    <label for="background_image">Selecciona una imagen (JPG, PNG o WebP):</label>
    <input type="file" name="background_image" id="background_image" accept="image/*" required>
    <button type="submit">Actualizar imagen</button>
</form>
<div class="preview-card">
    <p><strong>Imagen actual:</strong></p>
    <div class="preview" style="max-width:320px;">
        <img src="<?= htmlspecialchars($backgroundImage) ?>" alt="Imagen de fondo actual" style="width:100%;height:auto;border-radius:8px;box-shadow:0 6px 16px rgba(0,0,0,0.12);">
    </div>
    <p><small>Ruta guardada: <?= htmlspecialchars($currentSetting) ?></small></p>
</div>
<style>
.config-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin: 20px 0;
    padding: 16px;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    background: #fafafa;
}
.config-form label { font-weight: 600; }
.config-form button {
    align-self: flex-start;
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background: #1A5E2A;
    color: #fff;
    cursor: pointer;
}
.config-form button:hover { background: #144a22; }
.preview-card { margin-top: 12px; }
</style>
<?php pie(); ?>
