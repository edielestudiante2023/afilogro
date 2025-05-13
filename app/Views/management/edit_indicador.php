<!-- app/Views/management/edit_indicador.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Indicador – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
    <h1 class="h3 mb-4">Editar Indicador</h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach (session()->getFlashdata('errors') as $e): ?>
                <p><?= esc($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('indicadores/edit/'.$indicador['id_indicador']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= old('nombre', esc($indicador['nombre'])) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fórmula</label>
            <input type="text" name="formula" class="form-control" value="<?= old('formula', esc($indicador['formula'])) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fórmula Larga</label>
            <textarea name="formula_larga" class="form-control" rows="3" required><?= old('formula_larga', esc($indicador['formula_larga'])) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Variables</label>
            <input type="text" name="variables" class="form-control" value="<?= old('variables', esc($indicador['variables'])) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Unidad</label>
            <input type="text" name="unidad" class="form-control" value="<?= old('unidad', esc($indicador['unidad'])) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Periodicidad</label>
            <input type="text" name="periodicidad" class="form-control" value="<?= old('periodicidad', esc($indicador['periodicidad'])) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Meta</label>
            <input type="text" name="meta" class="form-control" value="<?= old('meta', esc($indicador['meta'])) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ponderación (%)</label>
            <input type="number" name="ponderacion" class="form-control" value="<?= old('ponderacion', esc($indicador['ponderacion'])) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Objetivo de Proceso</label>
            <textarea name="objetivo_proceso" class="form-control" rows="3" required><?= old('objetivo_proceso', esc($indicador['objetivo_proceso'])) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Objetivo de Calidad que Impacta</label>
            <input type="text" name="objetivo_calidad" class="form-control" value="<?= old('objetivo_calidad', esc($indicador['objetivo_calidad'])) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Indicador</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>