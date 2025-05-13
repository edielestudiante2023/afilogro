<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Perfiles – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Listado de Perfiles</h1>
        <a href="<?= base_url('perfiles/add') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Perfil
        </a>
    </div>
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <table id="perfilTable" class="table table-striped" style="width:100%">
        <thead class="table-dark">
            <tr>
                <th>Nombre Cargo</th>
                <th>Área</th>
                <th>Jefe Inmediato</th>
                <th>Colaboradores</th>
                <th>Creado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($perfiles as $p): ?>
            <tr>
                <td><?= esc($p['nombre_cargo']) ?></td>
                <td><?= esc($p['area']) ?></td>
                <td><?= esc($p['jefe_inmediato']) ?></td>
                <td><?= esc($p['colaboradores_a_cargo']) ?></td>
                <td><?= esc($p['created_at']) ?></td>
                <td class="text-center">
                    <a href="<?= base_url('perfiles/edit/'.$p['id_perfil_cargo']) ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                    <a href="<?= base_url('perfiles/delete/'.$p['id_perfil_cargo']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este perfil?')">Eliminar</a>
                </
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
    $('#perfilTable').DataTable({ responsive:true, autoWidth:false });
});
</script>
</body>
</html>
