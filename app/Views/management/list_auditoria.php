<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría de Indicadores – Afilogro</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (para icono Excel si lo deseas) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- Buttons extension CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/nav') ?>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Listado de Auditoría de Indicadores</h1>
            <!-- Botón Excel se inyecta via DataTables Buttons -->
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="auditTable" class="table table-striped table-bordered nowrap" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Historial ID</th>
                        <th>Editor</th>
                        <th>Campo</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                        <th>Fecha de Edición</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if (! empty($auditorias)): ?>
                        <?php foreach ($auditorias as $a): ?>
                            <tr>
                                <td><?= esc($a['id_auditoria']) ?></td>
                                <td><?= esc($a['id_historial']) ?></td>
                                <td><?= esc($a['editor_nombre'] ?? $a['editor_id']) ?></td>
                                <td><?= esc($a['campo']) ?></td>
                                <td><?= esc($a['valor_anterior']) ?></td>
                                <td><?= esc($a['valor_nuevo']) ?></td>
                                <td><?= esc($a['fecha_edicion']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?= $this->include('partials/logout') ?>

    <!-- JS: jQuery, Bootstrap, DataTables + Extensions -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
    $(document).ready(function() {
        var table = $('#auditTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel"></i> Exportar a Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success mb-3'
            }],
            order: [[6, 'desc']],         // Orden descendente por Fecha de Edición (columna 7)
            pageLength: 20,
            lengthMenu: [[20, 50, 100], [20, 50, 100]],
            responsive: true,
            autoWidth: false,
            initComplete: function () {
                // Aplica filtros tipo lista (select) en cada columna
                this.api().columns().every(function () {
                    var column = this;
                    var select = $('select', column.footer());
                    column.data().unique().sort().each(function (d) {
                        var txt = $('<div>').html(d).text().trim();
                        if (txt && select.find('option[value="'+txt+'"]').length === 0) {
                            select.append('<option value="'+txt+'">'+txt+'</option>');
                        }
                    });
                    select.on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^'+val+'$' : '', true, false).draw();
                    });
                });
            },
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: {
                    first:    "Primero",
                    last:     "Último",
                    next:     "Siguiente",
                    previous: "Anterior"
                },
                zeroRecords:    "No se encontraron registros",
                infoEmpty:      "Mostrando 0 a 0 de 0 registros",
                infoFiltered:   "(filtrado de _MAX_ registros totales)"
            }
        });
    });
    </script>
</body>
</html>
