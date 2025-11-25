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
                <div class="auth-input-wrapper">
                    <input type="text" name="primer_nombre" class="auth-input" value="<?php echo htmlspecialchars($_POST['primer_nombre'] ?? ''); ?>" required maxlength="30" aria-describedby="primer-nombre-feedback" data-feedback="primer-nombre-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="primer-nombre-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Segundo nombre
                <div class="auth-input-wrapper">
                    <input type="text" name="segundo_nombre" class="auth-input" value="<?php echo htmlspecialchars($_POST['segundo_nombre'] ?? ''); ?>" maxlength="30" aria-describedby="segundo-nombre-feedback" data-feedback="segundo-nombre-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="segundo-nombre-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Primer apellido
                <div class="auth-input-wrapper">
                    <input type="text" name="primer_apellido" class="auth-input" value="<?php echo htmlspecialchars($_POST['primer_apellido'] ?? ''); ?>" required maxlength="30" aria-describedby="primer-apellido-feedback" data-feedback="primer-apellido-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="primer-apellido-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Cédula
                <div class="auth-input-wrapper">
                    <input type="text" name="cedula" class="auth-input" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>" required maxlength="20" inputmode="numeric" pattern="\\d*" aria-describedby="cedula-feedback" data-feedback="cedula-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="cedula-feedback" role="status" aria-live="polite"></div>
            </label>
            <div class="header-offset">
                <label class="auth-label">Celular</label>
                <div class="auth-input-grid">
                    <div class="auth-input-wrapper">
                        <input type="text" name="telefono_codigo" class="auth-input" style="max-width: 110px;" value="<?php echo htmlspecialchars($_POST['telefono_codigo'] ?? '+57'); ?>" required aria-describedby="telefono-codigo-feedback" data-feedback="telefono-codigo-feedback">
                        <span class="validation-icon" aria-hidden="true"></span>
                    </div>
                    <div class="auth-input-wrapper">
                        <input type="text" name="telefono_numero" class="auth-input" value="<?php echo htmlspecialchars($_POST['telefono_numero'] ?? ''); ?>" required maxlength="20" inputmode="numeric" pattern="\\d*" aria-describedby="telefono-numero-feedback" data-feedback="telefono-numero-feedback">
                        <span class="validation-icon" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="field-feedback is-hidden" id="telefono-codigo-feedback" role="status" aria-live="polite"></div>
                <div class="field-feedback is-hidden" id="telefono-numero-feedback" role="status" aria-live="polite"></div>
            </div>
            <label class="auth-label">Correo electrónico
                <div class="auth-input-wrapper">
                    <input type="email" name="email" class="auth-input" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required aria-describedby="email-feedback" data-feedback="email-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="email-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Municipio de origen
                <div class="auth-input-wrapper">
                    <input type="text" name="municipio_origen" class="auth-input" value="<?php echo htmlspecialchars($_POST['municipio_origen'] ?? ''); ?>" maxlength="100" aria-describedby="municipio-origen-feedback" data-feedback="municipio-origen-feedback">
                    <span class="validation-icon" aria-hidden="true"></span>
                </div>
                <div class="field-feedback is-hidden" id="municipio-origen-feedback" role="status" aria-live="polite"></div>
            </label>
            <label class="auth-label">Contraseña
                <div class="auth-input-wrapper">
                    <input type="password" name="password" class="auth-input" required aria-describedby="password-feedback" data-feedback="password-feedback">
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

        const fieldMap = {
            primer_nombre: {
                validator: (value) => {
                    if (!value) {
                        return { valid: false, message: 'El primer nombre es obligatorio.' };
                    }

                    if (value.length < 2 || value.length > 30) {
                        return { valid: false, message: 'Debe tener entre 2 y 30 caracteres.' };
                    }

                    if (!/^[\p{L}ñÑáéíóúÁÉÍÓÚ'\s-]+$/u.test(value)) {
                        return { valid: false, message: 'Solo se permiten letras.' };
                    }

                    return { valid: true, message: 'Nombre válido.' };
                },
                sanitize: sanitizeName
            },
            segundo_nombre: {
                validator: (value) => {
                    if (!value) {
                        return { valid: true, message: 'Campo opcional.' };
                    }

                    if (value.length < 2 || value.length > 30) {
                        return { valid: false, message: 'Debe tener entre 2 y 30 caracteres.' };
                    }

                    if (!/^[\p{L}ñÑáéíóúÁÉÍÓÚ'\s-]+$/u.test(value)) {
                        return { valid: false, message: 'Solo se permiten letras.' };
                    }

                    return { valid: true, message: 'Nombre válido.' };
                },
                sanitize: sanitizeName
            },
            primer_apellido: {
                validator: (value) => {
                    if (!value) {
                        return { valid: false, message: 'El primer apellido es obligatorio.' };
                    }

                    if (value.length < 2 || value.length > 30) {
                        return { valid: false, message: 'Debe tener entre 2 y 30 caracteres.' };
                    }

                    if (!/^[\p{L}ñÑáéíóúÁÉÍÓÚ'\s-]+$/u.test(value)) {
                        return { valid: false, message: 'Solo se permiten letras.' };
                    }

                    return { valid: true, message: 'Apellido válido.' };
                },
                sanitize: sanitizeName
            },
            cedula: {
                validator: (value) => {
                    if (!value) {
                        return { valid: false, message: 'La cédula es obligatoria.' };
                    }

                    if (value.length < 6 || value.length > 20) {
                        return { valid: false, message: 'Debe tener entre 6 y 20 dígitos.' };
                    }

                    if (!/^\d+$/.test(value)) {
                        return { valid: false, message: 'Solo se permiten números.' };
                    }

                    return { valid: true, message: 'Cédula válida.' };
                },
                sanitize: sanitizeDigits
            },
            telefono_codigo: {
                validator: (value) => {
                    if (!value) {
                        return { valid: false, message: 'El código es obligatorio.' };
                    }

                    if (!/^\+?\d{1,5}$/.test(value)) {
                        return { valid: false, message: 'Ingresa un código válido (ej. +57).' };
                    }

                    return { valid: true, message: 'Código válido.' };
                },
                sanitize: sanitizePhoneCode
            },
            telefono_numero: {
                validator: (value) => {
                    if (!value) {
                        return { valid: false, message: 'El número es obligatorio.' };
                    }

                    if (value.length < 7 || value.length > 20) {
                        return { valid: false, message: 'Debe tener entre 7 y 20 dígitos.' };
                    }

                    if (!/^\d+$/.test(value)) {
                        return { valid: false, message: 'Solo se permiten números.' };
                    }

                    return { valid: true, message: 'Número válido.' };
                },
                sanitize: sanitizeDigits
            },
            municipio_origen: {
                validator: (value) => {
                    if (!value) {
                        return { valid: true, message: 'Campo opcional.' };
                    }

                    if (value.length < 3 || value.length > 100) {
                        return { valid: false, message: 'Debe tener entre 3 y 100 caracteres.' };
                    }

                    return { valid: true, message: 'Municipio válido.' };
                }
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
                    sanitize(input);
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

        Object.entries(fieldMap).forEach(([name, config]) => {
            const input = form.querySelector(`input[name="${name}"]`);
            if (!input) return;
            setupValidation(input, config.validator, config.sanitize);
        });

        const nameInputs = ['primer_nombre', 'segundo_nombre', 'primer_apellido']
            .map((name) => form.querySelector(`input[name="${name}"]`));
        const cedulaInput = form.querySelector('input[name="cedula"]');
        const codigoInput = form.querySelector('input[name="telefono_codigo"]');
        const telefonoInput = form.querySelector('input[name="telefono_numero"]');

        nameInputs.forEach((input) => {
            input.addEventListener('keydown', (event) => {
                if (controlKeys.includes(event.key)) return;
                if (/\d/.test(event.key)) {
                    event.preventDefault();
                }
            });
        });

        cedulaInput.addEventListener('keydown', (event) => {
            if (controlKeys.includes(event.key)) return;
            if (/[^0-9]/.test(event.key)) {
                event.preventDefault();
            }
        });

        codigoInput.addEventListener('keydown', (event) => {
            if (controlKeys.includes(event.key)) return;
            if (event.key === '+' && !codigoInput.value.includes('+')) {
                return;
            }
            if (/[^0-9]/.test(event.key)) {
                event.preventDefault();
            }
        });

        telefonoInput.addEventListener('keydown', (event) => {
            if (controlKeys.includes(event.key)) return;
            if (/[^0-9]/.test(event.key)) {
                event.preventDefault();
            }
        });
    });
</script>
<?php pie(); ?>
