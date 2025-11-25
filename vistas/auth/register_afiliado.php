<?php
$backgroundImage = getBackgroundImageUrl();
cabecera('Registro de Afiliado', ['css/auth.css'], 'auth-page');
?>
<div class="auth-shell" style="--auth-bg: url('<?= htmlspecialchars($backgroundImage) ?>');">
    <div class="auth-card">
        <h1>Afíliate</h1>
        <p class="subtitle">Registra tu alojamiento y llega a más viajeros.</p>
        <?php if (!empty($msg)): ?>
            <div class="<?= $ok ? 'auth-success' : 'auth-error'; ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <form class="auth-form" method="post" action="index.php?ruta=auth/register">
            <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(16)); ?>">
            <label class="auth-label">Nombre del negocio
                <input type="text" name="nombre_negocio" class="auth-input" required>
            </label>
            <label class="auth-label">Tipo
                <select name="tipo" class="auth-select" required>
                    <option value="">Seleccione...</option>
                    <option>Finca</option>
                    <option>Glamping</option>
                    <option>Hotel</option>
                    <option>Cabaña</option>
                    <option>Ecohotel</option>
                </select>
            </label>
            <label class="auth-label">Dirección
                <input type="text" name="direccion" class="auth-input" required>
            </label>
            <label class="auth-label">Descripción
                <textarea name="descripcion" class="auth-textarea" required></textarea>
            </label>
            <label class="auth-label">Email
                <input type="email" name="email" class="auth-input" required>
            </label>
            <label class="auth-label">Contraseña
                <input type="password" name="password" class="auth-input" required>
            </label>
            <button type="submit" class="auth-button">Enviar solicitud</button>
        </form>
    </div>
</div>
<?php pie(); ?>
