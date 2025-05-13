// app/Views/management/list_historial_indicador.php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Indicadores – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Historial Indicadores</h1>
    <a href="<?= base_url('historial_indicador/add') ?>" class="btn btn-primary">
      <i class="bi bi-plus-lg me-1"></i> Nuevo Registro
    </a>
  </div>
  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <table id="histTable" class="table table-striped table-bordered nowrap" style="width:100%">
    <thead class="table-dark">
      <tr>
        <th>Indicador</th>
        <th>Perfil</th>
        <th>Usuario</th>
        <th>Periodo</th>
        <th>Resultado</th>
        <th>Comentario</th>
        <th>Fecha Registro</th>
        <th class="text-center">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($records as $r): ?>
      <tr>
        <td><?= esc($r['indicador']) ?></td>
        <td><?= esc($r['perfil']) ?></td>
        <td><?= esc($r['usuario']) ?></td>
        <td><?= esc($r['periodo']) ?></td>
        <td><?= esc($r['resultado_real']) ?></td>
        <td><?= esc($r['comentario']) ?></td>
        <td><?= esc($r['fecha_registro']) ?></td>
        <td class="text-center">
          <a href="<?= base_url('historial_indicador/edit/'.$r['id_historial']) ?>" class="btn btn-sm btn-warning me-1">Editar</a>
          <a href="<?= base_url('historial_indicador/delete/'.$r['id_historial']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar registro?')">Eliminar</a>
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
    $('#histTable').DataTable({ responsive:true, autoWidth:false });
});
</script>
</body>
</html>
