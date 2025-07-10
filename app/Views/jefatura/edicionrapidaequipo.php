<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Edición Rápida de Indicadores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <h3 class="mb-4">✍️ Edición Rápida de Indicadores del Equipo – <?= esc($periodo) ?></h3>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('edicion-indicadores/guardar') ?>">
            <?= csrf_field() ?>

            <div class="table-responsive">
                <table id="edicionTable" class="table table-bordered align-middle nowrap" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha de Registro</th>
                            <th>Trabajador</th>
                            <th>Indicador</th>
                            <th>Resultado Real</th>
                            <th>Comentario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipo as $item): ?>
                            <tr>
                                <td class="text-muted small">
                                    <?= esc(date('Y-m-d H:i', strtotime($item['fecha_registro']))) ?>
                                </td>
                                <td><?= esc($item['nombre_completo']) ?></td>
                                <td><?= esc($item['nombre_indicador']) ?></td>
                                <td>
                                    <input type="text"
                                           name="cambios[<?= $item['id_historial'] ?>][resultado_real]"
                                           class="form-control"
                                           value="<?= esc($item['resultado_real']) ?>">
                                </td>
                                <td>
                                    <input type="text"
                                           name="cambios[<?= $item['id_historial'] ?>][comentario]"
                                           class="form-control"
                                           value="<?= esc($item['comentario']) ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
        </form>
    </div>

    <!-- Scripts: jQuery, Bootstrap, DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#edicionTable').DataTable({
            order: [[0, 'desc']],        // Orden descendente por Fecha de Registro
            pageLength: 20,
            lengthMenu: [[20, 50, 100], [20, 50, 100]],
            responsive: true,
            autoWidth: false,
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
                zeroRecords: "No se encontraron registros",
                infoEmpty:   "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)"
            }
        });
    });
    </script>
</body>

</html>
