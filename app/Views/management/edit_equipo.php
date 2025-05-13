<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipo – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
    <h1 class="h3 mb-4">Editar Asignación de Equipo</h1>
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach(session()->getFlashdata('errors') as $e): ?><p><?= esc($e) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="<?= base_url('equipos/edit/'.$equipo['id_equipos']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Jefe</label>
                <select name="id_jefe" class="form-select" required>
                    <?php foreach($users as $u): ?>
                        <option value="<?= esc($u['id_users']) ?>" <?= set_select('id_jefe',$u['id_users'],$equipo['id_jefe']==$u['id_users']) ?>>
                            <?= esc($u['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Subordinado</label>
                <select name="id_subordinado" class="form-select" required>
                    <?php foreach($users as $u): ?>
                        <option value="<?= esc($u['id_users']) ?>" <?= set_select('id_subordinado',$u['id_users'],$equipo['id_subordinado']==$u['id_users']) ?>>
                            <?= esc($u['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
