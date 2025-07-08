<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Indicador Asignado – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?= $this->include('partials/nav') ?>

    <div class="container py-4">
        <h1 class="h4 mb-4">Editar Asignación de Indicador</h1>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <p><?= esc($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('indicadores_perfil/edit/' . $registro['id_indicador_perfil']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="perfilSelect" class="form-label">Cargo / Perfil</label>
                <select name="id_perfil_cargo" id="perfilSelect" class="form-select" required>
                    <option value="">-- Selecciona un cargo --</option>
                    <?php foreach ($perfiles as $p): ?>
                        <option value="<?= $p['id_perfil_cargo'] ?>" <?= $p['id_perfil_cargo'] == $registro['id_perfil_cargo'] ? 'selected' : '' ?>>
                            <?= esc($p['nombre_cargo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="indicadorSelect" class="form-label">Indicador</label>
                <select name="id_indicador" id="indicadorSelect" class="form-select" required>
                    <option value="">-- Selecciona un indicador --</option>
                    <?php foreach ($indicadores as $ind): ?>
                        <option value="<?= $ind['id_indicador'] ?>" <?= $ind['id_indicador'] == $registro['id_indicador'] ? 'selected' : '' ?>>
                            <?= esc($ind['nombre']) ?> - <?= esc($ind['unidad']) ?> (<?= esc($ind['tipo_meta']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="periodicidad" class="form-label">Periodicidad</label>
                <input type="text" name="periodicidad" id="periodicidad" class="form-control" value="<?= esc($registro['periodicidad']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="meta" class="form-label">Meta (texto)</label>
                <input type="text" name="meta" id="meta" class="form-control" value="<?= esc($registro['meta']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="ponderacion" class="form-label">Ponderación (%)</label>
                <input type="number" name="ponderacion" id="ponderacion" class="form-control" min="0" max="100" value="<?= esc($registro['ponderacion']) ?>" required>
            </div>

            <div class="alert alert-info">
                <strong>Nota:</strong> El <em>valor meta</em>, la <em>unidad</em> y el <em>método de cálculo</em> ya están definidos en el indicador y se mostrarán automáticamente al consultarlo.
            </div>

            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-success me-2">Actualizar</button>
                <a href="<?= base_url('indicadores_perfil') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
