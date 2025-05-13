<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
    <h1 class="h3 mb-4">Editar Perfil</h1>
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach(session()->getFlashdata('errors') as $e): ?><p><?= esc($e) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="<?= base_url('perfiles/edit/'.$perfil['id_perfil_cargo']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Nombre del Cargo</label>
            <input type="text" name="nombre_cargo" class="form-control" value="<?= old('nombre_cargo', esc($perfil['nombre_cargo'])) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Área</label>
            <input type="text" name="area" class="form-control" value="<?= old('area', esc($perfil['area'])) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jefe Inmediato</label>
            <input type="text" name="jefe_inmediato" class="form-control" value="<?= old('jefe_inmediato', esc($perfil['jefe_inmediato'])) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Colaboradores a Cargo</label>
            <textarea name="colaboradores_a_cargo" class="form-control"><?= old('colaboradores_a_cargo', esc($perfil['colaboradores_a_cargo'])) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>