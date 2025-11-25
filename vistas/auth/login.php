<?php
$backgroundImage = getBackgroundImageUrl();
cabecera('Ingresar', ['css/auth.css'], 'auth-page');
?>
<div class="auth-shell" style="--auth-bg: url('<?= htmlspecialchars($backgroundImage) ?>');">
    <div class="auth-card">
        <h1>Iniciar sesión</h1>
        <p class="subtitle">Accede para gestionar tus reservas o alojamientos.</p>
        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form class="auth-form" method="post" action="index.php?ruta=auth/login">
            <label class="auth-label">
                Email
                <div class="auth-input-wrapper">
                    <input type="email" name="email" class="auth-input" autocomplete="email" required aria-describedby="email-feedback" data-feedback="email-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="email-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">
                Contraseña
                <div class="auth-input-wrapper">
                    <input type="password" name="password" class="auth-input" autocomplete="current-password" required aria-describedby="password-feedback" data-feedback="password-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="password-feedback" role="status" aria-live="polite"></div>
            </label>
            <button type="submit" class="auth-button">Ingresar</button>
        </form>
        <div class="auth-links">
            <p class="auth-helper">¿Eres propietario? <a href="index.php?ruta=auth/register">Afíliate</a></p>
            <p class="auth-helper">¿Aún no tienes cuenta? <a href="index.php?ruta=auth/register-cliente">Regístrate como cliente</a></p>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('.auth-form');
        const emailInput = form.querySelector('input[name="email"]');
        const passwordInput = form.querySelector('input[name="password"]');

        const sanitizeEmail = () => {
            const clean = emailInput.value.replace(/\s+/g, '');
            if (clean !== emailInput.value) {
                emailInput.value = clean;
            }
        };

        const validators = {
            email: (value) => {
                if (!value) {
                    return { valid: false, message: 'El email es obligatorio.' };
                }

                if (/\s/.test(value)) {
                    return { valid: false, message: 'El formato del correo no es válido.' };
                }

                if (value.length < 6 || value.length > 70) {
                    return { valid: false, message: 'El correo debe tener entre 6 y 70 caracteres.' };
                }

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(value)) {
                    return { valid: false, message: 'El formato del correo no es válido.' };
                }

                return { valid: true, message: 'Correo válido.' };
            },
            password: (value) => {
                if (!value) {
                    return { valid: false, message: 'La contraseña es obligatoria.' };
                }

                if (value.length < 6 || value.length > 20) {
                    return { valid: false, message: 'Debe tener entre 6 y 20 caracteres.' };
                }

                return { valid: true, message: 'Contraseña válida.' };
            }
        };

        const setupValidation = (input, validator, sanitize) => {
            const feedback = document.getElementById(input.dataset.feedback);
            const icon = input.closest('.auth-input-wrapper').querySelector('.validation-icon');
            let touched = false;

            const renderFeedback = () => {
                if (!touched) {
                    feedback.textContent = '';
                    feedback.classList.add('is-hidden');
                    icon.textContent = '';
                    input.classList.remove('is-valid', 'is-invalid');
                    return;
                }

                const { valid, message } = validator(input.value.trim());
                feedback.textContent = message;
                feedback.classList.remove('is-hidden');
                feedback.classList.toggle('valid', valid);
                feedback.classList.toggle('invalid', !valid);
                input.classList.toggle('is-valid', valid);
                input.classList.toggle('is-invalid', !valid);
                icon.textContent = valid ? '✔' : '✖';
                icon.classList.toggle('valid', valid);
                icon.classList.toggle('invalid', !valid);
            };

            input.addEventListener('input', () => {
                if (sanitize) {
                    sanitize();
                }
                if (touched) {
                    renderFeedback();
                }
            });

            input.addEventListener('blur', () => {
                touched = true;
                renderFeedback();
            });
        };

        setupValidation(emailInput, validators.email, sanitizeEmail);
        setupValidation(passwordInput, validators.password);
    });
</script>
<?php pie(); ?>
