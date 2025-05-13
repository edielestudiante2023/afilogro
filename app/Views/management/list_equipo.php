<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Equipos – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Listado de Equipos</h1>
        <a href="<?= base_url('equipos/add') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Equipo
        </a>
    </div>
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <table id="equipoTable" class="table table-striped table-hover table-bordered nowrap" style="width:100%">
        <thead class="table-dark">
            <tr>
                <th>Jefe</th>
                <th>Subordinado</th>
                <th>Fecha Asignación</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($equipos as $e): ?>
            <tr>
                <td><?= esc($e['jefe_nombre']) ?></td>
                <td><?= esc($e['sub_nombre']) ?></td>
                <td><?= esc($e['fecha_asignacion']) ?></td>
                <td>
                    <?= $e['estado_relacion'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?>
                </td>
                <td class="text-center">
                    <a href="<?= base_url('equipos/edit/'.$e['id_equipos']) ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                    <a href="<?= base_url('equipos/delete/'.$e['id_equipos']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta asignación?')">Eliminar</a>
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
    $('#equipoTable').DataTable({ responsive:true, autoWidth:false });
});
</script>
</body>
</html>
