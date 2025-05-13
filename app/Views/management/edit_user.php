<!-- app/Views/management/edit_user.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario – Afilogro</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body>
    <?= $this->include('partials/nav') ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Editar Usuario</h1>
            <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <!-- Errores de validación -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('users/edit/' . $user['id_users']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_completo" class="form-label">Nombre completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo"
                                class="form-control"
                                value="<?= old('nombre_completo', esc($user['nombre_completo'])) ?>"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="documento_identidad" class="form-label">Documento de identidad</label>
                            <input type="text" id="documento_identidad" name="documento_identidad"
                                class="form-control"
                                value="<?= old('documento_identidad', esc($user['documento_identidad'])) ?>"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <input type="email" id="correo" name="correo"
                            class="form-control"
                            value="<?= old('correo', esc($user['correo'])) ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input type="text" id="cargo" name="cargo"
                            class="form-control"
                            value="<?= old('cargo', esc($user['cargo'])) ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Nueva contraseña
                            <small class="text-muted">(dejar vacío para no cambiar)</small>
                        </label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                class="form-control"
                                placeholder="••••••••">
                            <button type="button" class="btn btn-outline-secondary toggle-password">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="id_roles" class="form-label">Rol</label>
                            <select id="id_roles" name="id_roles" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <option value="1" <?= set_select('id_roles', '1', $user['id_roles'] == 1) ?>>Superadmin</option>
                                <option value="2" <?= set_select('id_roles', '2', $user['id_roles'] == 2) ?>>Admin</option>
                                <option value="3" <?= set_select('id_roles', '3', $user['id_roles'] == 3) ?>>Jefatura</option>
                                <option value="4" <?= set_select('id_roles', '4', $user['id_roles'] == 4) ?>>Trabajador</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="id_areas" class="form-label">Área <span class="text-danger">*</span></label>

                            <select id="id_areas" name="id_areas" class="form-select">
                                <option value="">-- Seleccione --</option>
                                <option value="1" <?= set_select('id_areas', '1', $user['id_areas'] == 1) ?>>Gestión Humana</option>
                                <!-- Añade aquí otras áreas según tu tabla -->
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="id_perfil_cargo" class="form-label">Perfil de cargo</label>
                            <select id="id_perfil_cargo" name="id_perfil_cargo" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($perfiles_cargo as $p): ?>
                                    <option value="<?= esc($p['id_perfil_cargo']) ?>"
                                        <?= set_select('id_perfil_cargo', $p['id_perfil_cargo'], $user['id_perfil_cargo'] == $p['id_perfil_cargo']) ?>>
                                        <?= esc($p['nombre_cargo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_jefe" class="form-label">Jefe Inmediato</label>
                            <select id="id_jefe" name="id_jefe" class="form-select select2">
                                <option value="">-- Ninguno --</option>
                                <?php foreach ($jefes as $j): ?>
                                    <option value="<?= esc($j['id_users']) ?>"
                                        <?= old('id_jefe') == $j['id_users'] ? 'selected' : '' ?>>
                                        <?= esc($j['nombre_completo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>



                        <div class="col-md-4 mb-3">
                            <label for="activo" class="form-label">Estado</label>
                            <select id="activo" name="activo" class="form-select" required>
                                <option value="1" <?= set_select('activo', '1', $user['activo'] == 1) ?>>Activo</option>
                                <option value="0" <?= set_select('activo', '0', $user['activo'] == 0) ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (ya está en tu proyecto por DataTables) -->
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Script para toggle de contraseña -->
    <script>
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
                }
            });
        });
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Seleccione un jefe",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>

</html>