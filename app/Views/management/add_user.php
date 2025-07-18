<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario – Afilogro</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Indicar invalid state */
        .select2-container.is-invalid .select2-selection {
            border-color: #dc3545 !important;
        }
        
        /* Mejorar la compatibilidad con Bootstrap 5 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: calc(1.5em + 0.75rem + 2px);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Afilogro</a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Crear Nuevo Usuario</h1>
            <a href="#" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <!-- Errores de validación del servidor -->
        <div class="alert alert-danger d-none" id="error-alert">
            <ul class="mb-0" id="error-list"></ul>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form id="form-add-user" action="#" method="post" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_completo" class="form-label">Nombre completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo"
                                   class="form-control" value="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="documento_identidad" class="form-label">Documento de identidad</label>
                            <input type="text" id="documento_identidad" name="documento_identidad"
                                   class="form-control" value="" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <input type="email" id="correo" name="correo"
                               class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input type="text" id="cargo" name="cargo"
                               class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control" placeholder="••••••••" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Rol (requerido) -->
                        <div class="col-md-4 mb-3">
                            <label for="id_roles" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select id="id_roles" name="id_roles"
                                    class="form-select select2"
                                    data-placeholder="Seleccione un rol"
                                    data-required="true">
                                <option value=""></option>
                                <option value="1">Administrador</option>
                                <option value="2">Supervisor</option>
                                <option value="3">Empleado</option>
                            </select>
                        </div>

                        <!-- Área (requerido) -->
                        <div class="col-md-4 mb-3">
                            <label for="id_areas" class="form-label">Área <span class="text-danger">*</span></label>
                            <select id="id_areas" name="id_areas"
                                    class="form-select select2"
                                    data-placeholder="Seleccione un área"
                                    data-required="true">
                                <option value=""></option>
                                <option value="1">Recursos Humanos</option>
                                <option value="2">Tecnología</option>
                                <option value="3">Ventas</option>
                                <option value="4">Marketing</option>
                            </select>
                        </div>

                        <!-- Perfil de cargo (requerido) -->
                        <div class="col-md-4 mb-3">
                            <label for="id_perfil_cargo" class="form-label">Perfil de Cargo <span class="text-danger">*</span></label>
                            <select id="id_perfil_cargo" name="id_perfil_cargo"
                                    class="form-select select2"
                                    data-placeholder="Seleccione un perfil de cargo"
                                    data-required="true">
                                <option value=""></option>
                                <option value="1">Gerente</option>
                                <option value="2">Coordinador</option>
                                <option value="3">Analista</option>
                                <option value="4">Asistente</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jefe inmediato (opcional) -->
                    <div class="mb-3">
                        <label for="id_jefe" class="form-label">Jefe Inmediato</label>
                        <select id="id_jefe" name="id_jefe"
                                class="form-select select2"
                                data-placeholder="Seleccione un jefe">
                            <option value=""></option>
                            <option value="1">Juan Pérez</option>
                            <option value="2">María García</option>
                            <option value="3">Carlos López</option>
                        </select>
                    </div>

                    <!-- Estado (requerido) -->
                    <div class="col-md-4 mb-3">
                        <label for="activo" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select id="activo" name="activo"
                                class="form-select select2"
                                data-placeholder="Seleccione estado"
                                data-required="true">
                            <option value=""></option>
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Guardar usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery (Versión corregida) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        console.log('jQuery loaded:', typeof $ !== 'undefined');
        console.log('Select2 loaded:', typeof $.fn.select2 !== 'undefined');
        
        // Toggle de visibilidad de contraseña
        $('.toggle-password').on('click', function() {
            const $input = $(this).closest('.input-group').find('input');
            const $icon = $(this).find('i');
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
            }
        });

        // Inicializar Select2 con configuración corregida
        $('.select2').each(function() {
            const $select = $(this);
            const placeholder = $select.data('placeholder') || 'Seleccione una opción';
            
            $select.select2({
                theme: 'bootstrap-5',
                placeholder: placeholder,
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    }
                }
            });
        });

        // Validación personalizada para los select2 marcados como required
        $('#form-add-user').on('submit', function(e) {
            e.preventDefault(); // Prevenir envío para demo
            
            let valid = true;
            let errors = [];
            
            // Validar campos de texto requeridos
            $(this).find('input[required]').each(function() {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass('is-invalid');
                    errors.push(`El campo ${$(this).prev('label').text()} es obligatorio`);
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            // Validar select2 requeridos
            $('.select2').each(function() {
                const $select = $(this);
                const $container = $select.next('.select2-container');
                
                if ($select.data('required') && (!$select.val() || $select.val().length === 0)) {
                    valid = false;
                    $container.addClass('is-invalid');
                    const label = $select.prev('label').text().replace('*', '').trim();
                    errors.push(`El campo ${label} es obligatorio`);
                } else {
                    $container.removeClass('is-invalid');
                }
            });
            
            if (!valid) {
                // Mostrar errores
                $('#error-list').empty();
                errors.forEach(error => {
                    $('#error-list').append(`<li>${error}</li>`);
                });
                $('#error-alert').removeClass('d-none');
                
                // Scroll al primer error
                $('html, body').animate({
                    scrollTop: $('#error-alert').offset().top - 100
                }, 300);
            } else {
                $('#error-alert').addClass('d-none');
                alert('Formulario válido. En producción se enviaría al servidor.');
            }
        });
        
        // Limpiar errores al cambiar valores
        $('input').on('input', function() {
            $(this).removeClass('is-invalid');
        });
        
        $('.select2').on('change', function() {
            $(this).next('.select2-container').removeClass('is-invalid');
        });
    });
    </script>
</body>
</html>