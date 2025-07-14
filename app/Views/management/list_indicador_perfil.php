<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Indicadores por Perfil – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap5 CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* Fila fija de 30px */
        #indicadorPerfilTable tbody tr {
            height: 30px !important;
        }
        /* Ajuste vertical y padding */
        #indicadorPerfilTable td {
            padding-top: 0;
            padding-bottom: 0;
            line-height: 30px;
        }
    </style>
</head>
<body>
    <?= $this->include('partials/nav') ?>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4">Listado de Indicadores por Perfil</h1>
            <a href="<?= base_url('indicadores_perfil/add') ?>" class="btn btn-primary">+ Asignar Indicador</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <table id="indicadorPerfilTable" class="table table-bordered table-striped" style="table-layout: fixed; width:100%">
            <thead class="table-dark">
                <tr>
                    <th>Área</th>
                    <th>Cargo</th>
                    <th>Indicador</th>
                    <th>Periodicidad</th>
                    <th>Meta</th>
                    <th>Meta Valor</th>
                    <th>Meta Descripción</th>
                    <th>Ponderación (%)</th>
                    <th>Tipo de Meta</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Área</th>
                    <th>Cargo</th>
                    <th>Indicador</th>
                    <th>Periodicidad</th>
                    <th>Meta</th>
                    <th>Meta Valor</th>
                    <th>Meta Descripción</th>
                    <th>Ponderación (%)</th>
                    <th>Tipo de Meta</th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($indicadores_perfil as $item): ?>
                <tr>
                    <td><?= esc($item['nombre_area']) ?></td>
                    <td><?= esc($item['nombre_cargo']) ?></td>
                    <td><?= esc($item['nombre_indicador']) ?></td>
                    <td><?= esc($item['periodicidad']) ?></td>
                    <td><?= esc($item['meta']) ?></td>
                    <td><?= esc($item['meta_valor']) ?></td>
                    <td><?= esc($item['meta_descripcion']) ?></td>
                    <td><?= esc($item['ponderacion']) ?></td>
                    <td><?= esc($item['tipo_meta']) ?></td>
                    <td class="text-center">
                        <a href="<?= base_url('indicadores_perfil/edit/' . $item['id_indicador_perfil']) ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="<?= base_url('indicadores_perfil/delete/' . $item['id_indicador_perfil']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar esta asignación?')">Eliminar</a>
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
    $(function() {
        // Inicializa DataTable con select en footer
        $('#indicadorPerfilTable').DataTable({
            responsive: true,
            autoWidth: false,
            language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" },
            initComplete: function() {
                this.api().columns().every(function() {
                    var col = this;
                    var select = $('<select class="form-select form-select-sm"><option value="">Todos</option></select>')
                        .appendTo($(col.footer()).empty())
                        .on('change', function() {
                            col.search($.fn.dataTable.util.escapeRegex(this.value) ? '^'+this.value+'$' : '', true, false).draw();
                        });
                    col.data().unique().sort().each(function(d) {
                        if (d) select.append('<option value="'+d+'">'+d+'</option>');
                    });
                });
            }
        });
    });
    </script>
</body>
</html>
