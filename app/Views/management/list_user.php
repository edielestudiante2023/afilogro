<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios – Afilogro</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .container-fluid {
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 0;
        }
        .table-container {
            flex: 1;
            overflow: hidden;
        }
        .dataTables_scrollBody {
            overflow: auto;
        }
    </style>
</head>
<body>

<?= $this->include('partials/nav') ?>

    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h1 class="h3 m-0">Listado de Usuarios</h1>
            <a href="<?= base_url('users/add') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
            </a>
        </div>

               <!-- Resumen de métricas -->
               <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Usuarios</h5>
                        <p class="display-6 fw-bold"><?= esc($total_usuarios) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Roles</h5>
                        <p class="display-6 fw-bold"><?= esc($total_roles) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Áreas</h5>
                        <p class="display-6 fw-bold"><?= esc($total_areas) ?></p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Table -->
        <div class="table-container p-3">
            <table id="userTable" class="table table-striped table-hover table-bordered nowrap display" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Cargo</th>
                        <th>Rol</th>
                        <th>Área</th>
                        <th>Jefe Inmediato</th>

                        <th>Perfil de Cargo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Cargo</th>
                        <th>Rol</th>
                        <th>Área</th>
                        <th>Jefe Inmediato</th>

                        <th>Perfil de Cargo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if (! empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= esc($u['nombre_completo']) ?></td>
                                <td><?= esc($u['documento_identidad']) ?></td>
                                <td><?= esc($u['correo']) ?></td>
                                <td><?= esc($u['cargo']) ?></td>
                                <td><?= esc($u['rol_nombre'] ?? $u['id_roles']) ?></td>
                                <td><?= esc($u['area_nombre'] ?? $u['id_areas']) ?></td>
                                <td><?= esc($u['nombre_jefe'] ?? '—') ?></td>

                                <td><?= esc($u['perfil_nombre']) ?></td>
                                <td>
                                    <?php if ($u['activo']): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('users/edit/'.$u['id_users']) ?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="<?= base_url('users/delete/'.$u['id_users']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar a <?= esc($u['nombre_completo']) ?>?')" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?= $this->include('partials/logout') ?>

    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <!-- DataTable Initialization -->
    <script>
    $(document).ready(function() {
        $('#userTable').DataTable({
            responsive: true,
            scrollY: 'calc(100vh - 150px)',
            scrollX: true,
            scrollCollapse: true,
            paging: true,
            autoWidth: false,
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                },
                zeroRecords: "No se encontraron registros",
                infoEmpty: "Mostrando 0 a 0 de 0 usuarios",
                infoFiltered: "(filtrado de _MAX_ total usuarios)"
            },
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var input = $('<input type="text" class="form-control form-control-sm" placeholder="Buscar..." />')
                        .appendTo($(column.footer()).empty())
                        .on('keyup change clear', function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                });
            }
        });
    });
    </script>
</body>
</html>
