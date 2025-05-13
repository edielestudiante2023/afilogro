<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Indicadores por Perfil – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        #indicadorPerfilTable {
            width: 100% !important;
            table-layout: fixed;
        }

        #indicadorPerfilTable th,
        #indicadorPerfilTable td {
            white-space: normal !important;
            word-break: break-word;
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

        <table id="indicadorPerfilTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Área</th> <!-- NUEVO -->
                    <th>Cargo</th>
                    <th>Indicador</th>
                    <th>Periodicidad</th>
                    <th>Meta</th>
                    <th>Ponderación (%)</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($indicadores_perfil as $item): ?>
                    <tr>
                        <td><?= esc($item['nombre_area']) ?></td> <!-- NUEVO -->
                        <td><?= esc($item['nombre_cargo']) ?></td>
                        <td><?= esc($item['nombre_indicador']) ?></td>
                        <td><?= esc($item['periodicidad']) ?></td>
                        <td><?= esc($item['meta']) ?></td>
                        <td><?= esc($item['ponderacion']) ?></td>
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

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#indicadorPerfilTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                }
            });
        });
    </script>
</body>

</html>