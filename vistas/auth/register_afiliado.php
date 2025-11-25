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
                <div class="auth-input-wrapper">
                    <input type="text" name="nombre_negocio" class="auth-input" required minlength="3" maxlength="30" aria-describedby="nombre-negocio-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="nombre-negocio-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Tipo
                <div class="auth-input-wrapper">
                    <select name="tipo" class="auth-select" required aria-describedby="tipo-feedback">
                        <option value="">Seleccione...</option>
                        <option>Finca</option>
                        <option>Glamping</option>
                        <option>Hotel</option>
                        <option>Cabaña</option>
                        <option>Ecohotel</option>
                    </select>
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="tipo-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Dirección
                <div class="auth-input-wrapper">
                    <input type="text" name="direccion" class="auth-input" required minlength="3" maxlength="80" aria-describedby="direccion-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="direccion-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Descripción
                <div class="auth-input-wrapper">
                    <textarea name="descripcion" class="auth-textarea" required minlength="3" maxlength="300" aria-describedby="descripcion-feedback"></textarea>
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="descripcion-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Email
                <div class="auth-input-wrapper">
                    <input type="email" name="email" class="auth-input" required autocomplete="email" aria-describedby="email-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="email-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Contraseña
                <div class="auth-input-wrapper">
                    <input type="password" name="password" class="auth-input" required autocomplete="new-password" aria-describedby="password-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="password-feedback" role="status" aria-live="polite"></div>
            </label>
            <button type="submit" class="auth-button">Enviar solicitud</button>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('.auth-form');
        const businessInput = form.querySelector('input[name="nombre_negocio"]');
        const tipoSelect = form.querySelector('select[name="tipo"]');
        const direccionInput = form.querySelector('input[name="direccion"]');
        const descripcionInput = form.querySelector('textarea[name="descripcion"]');
        const emailInput = form.querySelector('input[name="email"]');
        const passwordInput = form.querySelector('input[name="password"]');

        const sanitizeEmail = () => {
            const clean = emailInput.value.replace(/\s+/g, '');
            if (clean !== emailInput.value) {
                emailInput.value = clean;
            }
        };

        const validators = {
            nombre_negocio: (value) => {
                if (!value) {
                    return { valid: false, message: 'El nombre del negocio es obligatorio.' };
                }

                if (value.length < 3 || value.length > 30) {
                    return { valid: false, message: 'Debe tener entre 3 y 30 caracteres.' };
                }

                return { valid: true, message: 'Nombre válido.' };
            },
            tipo: (value) => {
                if (!value) {
                    return { valid: false, message: 'Selecciona el tipo de alojamiento.' };
                }
                return { valid: true, message: 'Tipo seleccionado.' };
            },
            direccion: (value) => {
                if (!value) {
                    return { valid: false, message: 'La dirección es obligatoria.' };
                }

                if (value.length < 3 || value.length > 80) {
                    return { valid: false, message: 'Debe tener entre 3 y 80 caracteres.' };
                }

                return { valid: true, message: 'Dirección válida.' };
            },
            descripcion: (value) => {
                if (!value) {
                    return { valid: false, message: 'La descripción es obligatoria.' };
                }

                if (value.length < 3 || value.length > 300) {
                    return { valid: false, message: 'Debe tener entre 3 y 300 caracteres.' };
                }

                return { valid: true, message: 'Descripción válida.' };
            },
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

        const setupValidation = (input, validator) => {
            const feedback = input.closest('.auth-label').querySelector('.field-feedback');
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

            const eventName = input.tagName === 'SELECT' ? 'change' : 'input';

            input.addEventListener(eventName, () => {
                if (input === emailInput) {
                    sanitizeEmail();
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

        setupValidation(businessInput, validators.nombre_negocio);
        setupValidation(tipoSelect, validators.tipo);
        setupValidation(direccionInput, validators.direccion);
        setupValidation(descripcionInput, validators.descripcion);
        setupValidation(emailInput, validators.email);
        setupValidation(passwordInput, validators.password);
    });
</script>
<?php pie(); ?>
