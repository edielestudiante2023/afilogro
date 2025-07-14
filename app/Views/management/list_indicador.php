<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Indicadores – Afilogro</title>

    <!-- Bootstrap & DataTables CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* Tabla de ancho completo y layout fijo */
        #indicadorTable {
            width: 100% !important;
            table-layout: fixed;
            font-family: Arial, sans-serif;
            font-size: 0.875rem;
        }

        /* Altura reducida de las filas */
        #indicadorTable tbody tr {
            height: 3rem;
        }

        /* Celdas truncadas con tooltip */
        .cell-content {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 3rem;
            line-height: 3rem;
        }

        /* Columna de acciones: botones apilados */
        .action-cell {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: stretch;
            height: 100%;
            gap: 0.2rem;
        }

        /* Botones compactos */
        .action-cell .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1rem;
        }

        /* Inputs de filtro en el footer */
        tfoot input {
            width: 100%;
            box-sizing: border-box;
            padding: 0.2rem;
            font-size: 0.875rem;
        }
    </style>
</head>

<body class="p-0">
    <?= $this->include('partials/nav') ?>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Listado de Indicadores</h1>
            <a href="<?= base_url('indicadores/add') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Indicador
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <table id="indicadorTable" class="table table-striped table-bordered nowrap w-100">
            <thead class="table-dark align-middle">
                <tr>
                    <th>Nombre</th>
                    <th>Meta Valor</th>
                    <th>Meta Descripción</th>
                    <th>Tipo Meta</th>
                    <th>Fórmula</th>
                    <th>Unidad</th>
                    <th>Objetivo Proceso</th>
                    <th>Objetivo Calidad</th>
                    <th>Tipo Aplicación</th>
                    <th>Activo</th>
                    <th>Periodicidad</th>
                    <th>Ponderación (%)</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tfoot class="table-dark align-middle">
                <tr>
                    <th><input type="text" placeholder="Buscar Nombre"></th>
                    <th><input type="text" placeholder="Buscar Meta Valor"></th>
                    <th><input type="text" placeholder="Buscar Descripción"></th>
                    <th><input type="text" placeholder="Buscar Tipo Meta"></th>
                    <th><input type="text" placeholder="Buscar Fórmula"></th>
                    <th><input type="text" placeholder="Buscar Unidad"></th>
                    <th><input type="text" placeholder="Buscar Obj. Proceso"></th>
                    <th><input type="text" placeholder="Buscar Obj. Calidad"></th>
                    <th><input type="text" placeholder="Buscar Tipo Aplicación"></th>
                    <th><input type="text" placeholder="Buscar Activo"></th>
                    <th><input type="text" placeholder="Buscar Periodicidad"></th>
                    <th><input type="text" placeholder="Buscar % Ponderación"></th>
                    <th><input type="text" placeholder="Buscar Acciones"></th>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($indicadores as $i): ?>
                    <tr>
                        <!-- Nombre -->
                        <td>
                            <div class="cell-content"
                                 data-bs-toggle="tooltip"
                                 title="<?= esc($i['nombre']) ?>">
                                <?= esc($i['nombre']) ?>
                            </div>
                        </td>

                        <!-- Meta Valor -->
                        <td><?= esc($i['meta_valor'] ?? '—') ?></td>

                        <!-- Meta Descripción -->
                        <td>
                            <div class="cell-content"
                                 data-bs-toggle="tooltip"
                                 title="<?= esc($i['meta_descripcion'] ?? '') ?>">
                                <?= esc($i['meta_descripcion'] ?? '—') ?>
                            </div>
                        </td>

                        <!-- Tipo Meta -->
                        <td><?= esc($i['tipo_meta'] ?? '—') ?></td>

                        <!-- Fórmula -->
                        <td>
                            <div class="cell-content"
                                 data-bs-toggle="tooltip"
                                 title="<?= esc($i['formula_renderizada']) ?>">
                                <?= esc($i['formula_renderizada']) ?>
                            </div>
                        </td>

                        <!-- Unidad -->
                        <td><?= esc($i['unidad'] ?? '—') ?></td>

                        <!-- Objetivo Proceso -->
                        <td>
                            <div class="cell-content"
                                 data-bs-toggle="tooltip"
                                 title="<?= esc($i['objetivo_proceso'] ?? '') ?>">
                                <?= esc($i['objetivo_proceso'] ?? '—') ?>
                            </div>
                        </td>

                        <!-- Objetivo Calidad -->
                        <td class="small text-muted">
                            <div class="cell-content"
                                 data-bs-toggle="tooltip"
                                 title="<?= esc($i['objetivo_calidad'] ?? '') ?>">
                                <?= esc($i['objetivo_calidad'] ?? '—') ?>
                            </div>
                        </td>

                        <!-- Tipo Aplicación -->
                        <td><?= esc($i['tipo_aplicacion'] ?? '—') ?></td>

                        <!-- Activo -->
                        <td><?= isset($i['activo']) ? ($i['activo'] ? 'Sí' : 'No') : '—' ?></td>

                        <!-- Periodicidad -->
                        <td><?= esc($i['periodicidad'] ?? '—') ?></td>

                        <!-- Ponderación -->
                        <td><?= esc($i['ponderacion'] ?? '0') ?>%</td>

                        <!-- Acciones -->
                        <td>
                            <div class="action-cell">
                                <a href="<?= base_url('indicadores/edit/' . $i['id_indicador']) ?>"
                                   class="btn btn-warning">
                                    Editar
                                </a>
                                <a href="<?= base_url('indicadores/delete/' . $i['id_indicador']) ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Eliminar este indicador?')">
                                    Eliminar
                                </a>
                                <a href="<?= base_url('indicadores/fill/' . $i['id_indicador']) ?>"
                                   class="btn btn-info">
                                    Diligenciar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= $this->include('partials/logout') ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#indicadorTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                initComplete: function () {
                    this.api().columns().every(function () {
                        const column = this;
                        const footerInput = $('input', column.footer());
                        footerInput.on('keyup change clear', function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                    });
                }
            });

            // Inicializar tooltips de Bootstrap
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
</body>

</html>
