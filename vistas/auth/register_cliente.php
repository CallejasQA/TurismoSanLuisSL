<?php
$backgroundImage = getBackgroundImageUrl();
cabecera('Registro de Cliente', ['css/auth.css'], 'auth-page');
?>
<div class="auth-shell" style="--auth-bg: url('<?= htmlspecialchars($backgroundImage) ?>');">
    <div class="auth-card">
        <h1>Regístrate</h1>
        <p class="subtitle">Crea tu cuenta para descubrir y reservar experiencias únicas.</p>
        <?php if (!empty($msg)): ?>
            <div class="<?= $ok ? 'auth-success' : 'auth-error'; ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <form class="auth-form" method="post" action="index.php?ruta=auth/register-cliente">
            <label class="auth-label">Primer nombre
                <input type="text" name="primer_nombre" class="auth-input" value="<?php echo htmlspecialchars($_POST['primer_nombre'] ?? ''); ?>" required maxlength="30">
            </label>
            <label class="auth-label">Segundo nombre
                <input type="text" name="segundo_nombre" class="auth-input" value="<?php echo htmlspecialchars($_POST['segundo_nombre'] ?? ''); ?>" maxlength="30">
            </label>
            <label class="auth-label">Primer apellido
                <input type="text" name="primer_apellido" class="auth-input" value="<?php echo htmlspecialchars($_POST['primer_apellido'] ?? ''); ?>" required maxlength="30">
            </label>
            <label class="auth-label">Cédula
                <input type="text" name="cedula" class="auth-input" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>" maxlength="30">
            </label>
            <div class="header-offset">
                <label class="auth-label">Celular</label>
                <div style="display:flex; gap:8px;">
                    <input type="text" name="telefono_codigo" class="auth-input" style="max-width:90px;" value="<?php echo htmlspecialchars($_POST['telefono_codigo'] ?? '+57'); ?>" required>
                    <input type="text" name="telefono_numero" class="auth-input" value="<?php echo htmlspecialchars($_POST['telefono_numero'] ?? ''); ?>" required maxlength="15">
                </div>
            </div>
            <label class="auth-label">Correo electrónico
                <div class="auth-input-wrapper">
                    <input type="email" name="email" class="auth-input" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required aria-describedby="email-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="email-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Municipio de origen
                <input type="text" name="municipio_origen" class="auth-input" value="<?php echo htmlspecialchars($_POST['municipio_origen'] ?? ''); ?>" maxlength="100">
            </label>
            <label class="auth-label">Contraseña
                <div class="auth-input-wrapper">
                    <input type="password" name="password" class="auth-input" required aria-describedby="password-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="password-feedback" role="status" aria-live="polite"></div>
            </label>
            <button type="submit" class="auth-button">Registrarse</button>
        </form>
        <div class="auth-links">
            <p class="auth-helper">¿Ya tienes cuenta? <a href="index.php?ruta=auth/login">Iniciar sesión</a></p>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('.auth-form');
        const nameInputs = ['primer_nombre', 'segundo_nombre', 'primer_apellido']
            .map((name) => form.querySelector(`input[name="${name}"]`));
        const cedulaInput = form.querySelector('input[name="cedula"]');
        const codigoInput = form.querySelector('input[name="telefono_codigo"]');
        const telefonoInput = form.querySelector('input[name="telefono_numero"]');
        const emailInput = form.querySelector('input[name="email"]');
        const passwordInput = form.querySelector('input[name="password"]');

        const sanitizeName = (input) => {
            const clean = input.value.replace(/[0-9]/g, '');
            if (clean !== input.value) {
                input.value = clean;
            }
        };

        const sanitizeDigits = (input) => {
            const clean = input.value.replace(/[^0-9]/g, '');
            if (clean !== input.value) {
                input.value = clean;
            }
        };

        const sanitizePhoneCode = (input) => {
            let value = input.value;
            const hasPlus = value.startsWith('+');
            value = value.replace(/[^0-9]/g, '');
            input.value = `${hasPlus ? '+' : ''}${value}`;
        };

        const sanitizeEmail = () => {
            const clean = emailInput.value.replace(/\s+/g, '');
            if (clean !== emailInput.value) {
                emailInput.value = clean;
            }
        };

        const controlKeys = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete', 'Home', 'End'];

        nameInputs.forEach((input) => {
            input.addEventListener('keydown', (event) => {
                if (controlKeys.includes(event.key)) return;
                if (/\d/.test(event.key)) {
                    event.preventDefault();
                }
            });
            input.addEventListener('input', () => sanitizeName(input));
        });

        cedulaInput.addEventListener('keydown', (event) => {
            if (controlKeys.includes(event.key)) return;
            if (/[^0-9]/.test(event.key)) {
                event.preventDefault();
            }
        });
        cedulaInput.addEventListener('input', () => sanitizeDigits(cedulaInput));

        codigoInput.addEventListener('keydown', (event) => {
            if (controlKeys.includes(event.key)) return;
            if (event.key === '+' && !codigoInput.value.includes('+')) {
                return;
            }
            if (/[^0-9]/.test(event.key)) {
                event.preventDefault();
            }
        });
        codigoInput.addEventListener('input', () => sanitizePhoneCode(codigoInput));

        telefonoInput.addEventListener('keydown', (event) => {
            if (controlKeys.includes(event.key)) return;
            if (/[^0-9]/.test(event.key)) {
                event.preventDefault();
            }
        });
        telefonoInput.addEventListener('input', () => sanitizeDigits(telefonoInput));

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

            input.addEventListener('input', () => {
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

        setupValidation(emailInput, validators.email);
        setupValidation(passwordInput, validators.password);
    });
</script>
<?php pie(); ?>
