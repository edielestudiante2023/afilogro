<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Áreas – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>

<?= $this->include('partials/nav') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Listado de Áreas</h1>
        <a href="<?= base_url('areas/add') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva Área
        </a>
    </div>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <table id="areasTable" class="table table-striped table-hover nowrap" style="width:100%">
        <thead class="table-dark">
            <tr>
                <th>Nombre Área</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($areas as $a): ?>
            <tr>
                <td><?= esc($a['nombre_area']) ?></td>
                <td><?= esc($a['descripcion_area']) ?></td>
                <td>
                    <?= $a['estado_area'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?>
                </td>
                <td class="text-center">
                    <a href="<?= base_url('areas/edit/'.$a['id_areas']) ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                    <a href="<?= base_url('areas/delete/'.$a['id_areas']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta área?')">Eliminar</a>
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
    $('#areasTable').DataTable({ responsive:true, autoWidth:false });
});
</script>
</body>
</html>
