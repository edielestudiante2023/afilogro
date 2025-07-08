<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Indicadores – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        #indicadorTable {
            width: 100% !important;
            table-layout: fixed;
        }

        #indicadorTable th,
        #indicadorTable td {
            white-space: normal !important;
            word-break: break-word;
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
            <thead class="table-dark">
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
                    <th>Creado en</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($indicadores as $i): ?>
                    <tr>
                        <td><?= esc($i['nombre']) ?></td>
                        <td><?= isset($i['meta_valor']) ? esc($i['meta_valor']) : '—' ?></td>
                        <td><?= isset($i['meta_descripcion']) ? esc($i['meta_descripcion']) : '—' ?></td>
                        <td><?= isset($i['tipo_meta']) ? esc($i['tipo_meta']) : '—' ?></td>
                        <td><?= esc($i['formula_renderizada']) ?></td>
                        <td><?= isset($i['unidad']) ? esc($i['unidad']) : '—' ?></td>
                        <td class="small text-muted"><?= isset($i['objetivo_proceso']) ? esc($i['objetivo_proceso']) : '—' ?></td>
                        <td class="small text-muted"><?= isset($i['objetivo_calidad']) ? esc($i['objetivo_calidad']) : '—' ?></td>
                        <td><?= isset($i['tipo_aplicacion']) ? esc($i['tipo_aplicacion']) : '—' ?></td>
                        <td><?= isset($i['activo']) ? ($i['activo'] ? 'Sí' : 'No') : '—' ?></td>
                        <td><?= esc($i['periodicidad'] ?? '—') ?></td>
                        <td><?= esc($i['ponderacion'] ?? '0') ?>%</td>
                        <td><?= esc($i['created_at'] ?? '—') ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('indicadores/edit/' . $i['id_indicador']) ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                            <a href="<?= base_url('indicadores/delete/' . $i['id_indicador']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este indicador?')">Eliminar</a>
                            <a href="<?= base_url('indicadores/fill/' . $i['id_indicador']) ?>" class="btn btn-sm btn-info ms-1">Diligenciar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= $this->include('partials/logout') ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#indicadorTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' }
            });
        });
    </script>
</body>

</html>
