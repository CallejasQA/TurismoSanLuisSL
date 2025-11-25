<?php
$backgroundImage = getBackgroundImageUrl();
cabecera('Ingresar', ['css/auth.css'], 'auth-page');
?>
<style>
body.auth-page {
    background-image: url("<?= htmlspecialchars($backgroundImage) ?>");
}
</style>
<div class="auth-shell">
    <div class="auth-card">
        <h1>Iniciar sesión</h1>
        <p class="subtitle">Accede para gestionar tus reservas o alojamientos.</p>
        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form class="auth-form" method="post" action="index.php?ruta=auth/login">
            <label class="auth-label">
                Email
                <input type="email" name="email" class="auth-input" required>
            </label>
            <label class="auth-label">
                Contraseña
                <input type="password" name="password" class="auth-input" required>
            </label>
            <button type="submit" class="auth-button">Ingresar</button>
        </form>
        <div class="auth-links">
            <p class="auth-helper">¿Eres propietario? <a href="index.php?ruta=auth/register">Afíliate</a></p>
            <p class="auth-helper">¿Aún no tienes cuenta? <a href="index.php?ruta=auth/register-cliente">Regístrate como cliente</a></p>
        </div>
    </div>
</div>
<?php pie(); ?>
