<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Indicadores de Mi Equipo – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Buttons CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <style>
        td .dropdown-toggle {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 200px;
        }
        td .dropdown-menu {
            max-width: 400px;
            white-space: normal;
        }
        tfoot input {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <?= $this->include('partials/nav') ?>

    <div class="container-fluid py-4">
   

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (empty($equipo)): ?>
            <div class="alert alert-warning">
                No hay indicadores reportados por tu equipo en este rango de fechas.
            </div>
        <?php else: ?>

            <!-- TOGGLE COLUMNAS (contraído por defecto) -->
            <div class="mb-2">
                <button id="toggleCols" class="btn btn-sm btn-secondary">
                    Para ver las columnas completas de la tabla, haz clic aquí
                </button>
            </div>

            <div class="table-responsive">
                <table id="historialTable"
                       class="table table-bordered table-striped align-middle nowrap w-100"
                       style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th>Colaborador</th>
                            <th>Indicador</th>
                            <th>Meta Valor</th>
                            <th>Meta Descripción</th>
                            <th>Tipo Meta</th>
                            <th>Fórmula</th>
                            <th>Unidad</th>
                            <th>Objetivo Proceso</th>
                            <th>Objetivo Calidad</th>
                            <th>Tipo Aplicación</th>
                            <th>Creado en</th>
                            <th>Fecha de Registro</th>
                            <th>Periodicidad</th>
                            <th>Ponderación (%)</th>
                            <th>Resultado Real</th>
                            <th>Comentario</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <?php for ($i = 0; $i < 16; $i++): ?>
                                <th>
                                    <input type="text"
                                           placeholder="Buscar..."
                                           class="form-control form-control-sm"/>
                                </th>
                            <?php endfor; ?>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach ($equipo as $r): ?>
                            <tr>
                                <td><?= esc($r['nombre_completo']) ?></td>
                                <td><?= esc($r['nombre_indicador']) ?></td>
                                <td><?= esc($r['meta_valor']) ?></td>
                                <td><?= esc($r['meta_descripcion']) ?></td>
                                <td><?= esc($r['tipo_meta']) ?></td>
                                <td><code><?= esc($r['metodo_calculo']) ?></code></td>
                                <td><?= esc($r['unidad']) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle"
                                           data-bs-toggle="dropdown"
                                           aria-expanded="false"
                                           title="<?= esc($r['objetivo_proceso']) ?>">
                                            <?= esc($r['objetivo_proceso']) ?>
                                        </a>
                                        <div class="dropdown-menu p-3">
                                            <?= nl2br(esc($r['objetivo_proceso'])) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle"
                                           data-bs-toggle="dropdown"
                                           aria-expanded="false"
                                           title="<?= esc($r['objetivo_calidad']) ?>">
                                            <?= esc($r['objetivo_calidad']) ?>
                                        </a>
                                        <div class="dropdown-menu p-3">
                                            <?= nl2br(esc($r['objetivo_calidad'])) ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($r['tipo_aplicacion']) ?></td>
                                <td><?= esc($r['created_at']) ?></td>
                                <td><?= esc($r['fecha_registro']) ?></td>
                                <td><?= esc($r['periodicidad']) ?></td>
                                <td><?= esc($r['ponderacion']) ?>%</td>
                                <td><?= esc($r['resultado_real']) ?></td>
                                <td><?= esc($r['comentario']) ?: '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= base_url('jefatura/jefaturadashboard') ?>" class="btn btn-secondary">
                &larr; Volver al Dashboard
            </a>
        </div>
    </div>

    <?= $this->include('partials/logout') ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function() {
        // Columnas 8–11 (0-indexed: 7..10) ocultas por defecto
        var colsToToggle = [7, 8, 9, 10];
        var table = $('#historialTable').DataTable({
            scrollX: true,
            order: [[11, 'desc']],   // Fecha de Registro (col 11) descendente
            dom: 'Bfrtip',
            buttons: []
        });

        table.columns(colsToToggle).visible(false);

        // Filtros por columna
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change clear', function() {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });

        // Toggle de visibilidad y texto
        var expanded = false;
        $('#toggleCols').on('click', function() {
            expanded = !expanded;
            table.columns(colsToToggle).visible(expanded);
            $(this).text(
                expanded
                ? 'Ocultar columnas completas'
                : 'Para ver las columnas completas de la tabla, haz clic aquí'
            );
        });
    });
    </script>
</body>

</html>
