<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Indicadores – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- ESTILOS ADICIONALES PARA WIDTH Y WRAP -->
    <style>
      /* Fuerza que la tabla use todo el ancho disponible y fije el layout */
      #indicadorTable {
        width: 100% !important;
        table-layout: fixed; 
      }
      /* Permite que el texto haga wrap y rompa palabras largas */
      #indicadorTable th,
      #indicadorTable td {
        white-space: normal !important;
        word-break: break-word;
      }
    </style>
</head>

<body class="p-0">
<?= $this->include('partials/nav') ?>

    <!-- container-fluid para ancho completo -->
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

        <!-- Tabla sin la clase nowrap -->
        <table id="indicadorTable" class="table table-striped table-bordered" style="width:100%">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Fórmula</th>
                    <th>Fórmula Larga</th>
                    <th>Variables</th>
                    <th>Unidad</th>
                    <th>Periodicidad</th>
                    <th>Meta</th>
                    <th>Ponderación</th>
                    <th>Objetivo Proceso</th>
                    <th>Objetivo Calidad</th>
                    <th>Creado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($indicadores as $i): ?>
                    <tr>
                        <td><?= esc($i['nombre']) ?></td>
                        <td><?= esc($i['formula']) ?></td>
                        <td><code><?= esc($i['formula_larga']) ?></code></td>
                        <td><?= esc($i['variables']) ?></td>
                        <td><?= esc($i['unidad']) ?></td>
                        <td><?= esc($i['periodicidad']  ?? '—') ?></td>
                        <td><?= esc($i['meta']          ?? '—') ?></td>
                        <td><?= esc($i['ponderacion']   ?? '0') ?>%</td>
                        <td><?= esc($i['objetivo_proceso']) ?></td>
                        <td><?= esc($i['objetivo_calidad']) ?></td>
                        <td><?= esc($i['created_at']) ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('indicadores/edit/' . $i['id_indicador']) ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                            <a href="<?= base_url('indicadores/delete/' . $i['id_indicador']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este indicador?')">Eliminar</a>
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
                autoWidth: false
            });
        });
    </script>
</body>
</html>
