<?php
$backgroundImage = getBackgroundImageUrl();
cabecera('Registro de Cliente', ['css/auth.css'], 'auth-page');
?>
<style>
body.auth-page {
    background-image: url("<?= htmlspecialchars($backgroundImage) ?>");
}
</style>
<div class="auth-shell">
    <div class="auth-card">
        <h1>Regístrate</h1>
        <p class="subtitle">Crea tu cuenta para descubrir y reservar experiencias únicas.</p>
        <?php if (!empty($msg)): ?>
            <div class="<?= $ok ? 'auth-success' : 'auth-error'; ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <form class="auth-form" method="post" action="index.php?ruta=auth/register-cliente">
            <label class="auth-label">Primer nombre
                <input type="text" name="primer_nombre" class="auth-input" value="<?php echo htmlspecialchars($_POST['primer_nombre'] ?? ''); ?>" required>
            </label>
            <label class="auth-label">Segundo nombre
                <input type="text" name="segundo_nombre" class="auth-input" value="<?php echo htmlspecialchars($_POST['segundo_nombre'] ?? ''); ?>">
            </label>
            <label class="auth-label">Primer apellido
                <input type="text" name="primer_apellido" class="auth-input" value="<?php echo htmlspecialchars($_POST['primer_apellido'] ?? ''); ?>" required>
            </label>
            <label class="auth-label">Cédula
                <input type="text" name="cedula" class="auth-input" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>">
            </label>
            <div class="header-offset">
                <label class="auth-label">Celular</label>
                <div style="display:flex; gap:8px;">
                    <input type="text" name="telefono_codigo" class="auth-input" style="max-width:90px;" value="<?php echo htmlspecialchars($_POST['telefono_codigo'] ?? '+57'); ?>" required>
                    <input type="text" name="telefono_numero" class="auth-input" value="<?php echo htmlspecialchars($_POST['telefono_numero'] ?? ''); ?>" required>
                </div>
            </div>
            <label class="auth-label">Correo electrónico
                <input type="email" name="email" class="auth-input" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </label>
            <label class="auth-label">Municipio de origen
                <input type="text" name="municipio_origen" class="auth-input" value="<?php echo htmlspecialchars($_POST['municipio_origen'] ?? ''); ?>">
            </label>
            <label class="auth-label">Contraseña
                <input type="password" name="password" class="auth-input" required>
            </label>
            <button type="submit" class="auth-button">Registrarse</button>
        </form>
        <div class="auth-links">
            <p class="auth-helper">¿Ya tienes cuenta? <a href="index.php?ruta=auth/login">Iniciar sesión</a></p>
        </div>
    </div>
</div>
<?php pie(); ?>
