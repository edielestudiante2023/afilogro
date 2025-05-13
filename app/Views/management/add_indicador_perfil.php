<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Asignar Indicador a Perfil – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?= $this->include('partials/nav') ?>

    <div class="container py-4">
        <h1 class="h4 mb-4">Asignar Nuevo Indicador a Perfil</h1>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <p><?= esc($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('indicadores_perfil/add') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Cargo / Perfil</label>
                <select name="id_perfil_cargo" id="perfilSelect" class="form-select" required>
                    <option value="">-- Selecciona un cargo --</option>
                    <?php foreach ($perfiles as $p): ?>
                        <option value="<?= $p['id_perfil_cargo'] ?>" data-area="<?= esc($p['area']) ?>">
                            <?= esc($p['nombre_cargo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Área</label>
                <select name="area" id="areaSelect" class="form-select" required>
                    <option value="">-- Selecciona un área --</option>
                    <?php foreach ($areas as $a): ?>
                        <option value="<?= $a['nombre_area'] ?>"><?= esc($a['nombre_area']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="mb-3">
                <label class="form-label">Indicador</label>
                <select name="id_indicador" class="form-select" required>
                    <option value="">-- Selecciona un indicador --</option>
                    <?php foreach ($indicadores as $ind): ?>
                        <option value="<?= $ind['id_indicador'] ?>"><?= esc($ind['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Periodicidad</label>
                <input type="text" name="periodicidad" class="form-control" value="<?= old('periodicidad') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Meta</label>
                <input type="text" name="meta" class="form-control" value="<?= old('meta') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Ponderación (%)</label>
                <input type="number" name="ponderacion" class="form-control" min="0" max="100" value="<?= old('ponderacion') ?>" required>
            </div>

            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-success me-2">Guardar</button>
                <a href="<?= base_url('indicadores_perfil') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('perfilSelect').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const area = selected.getAttribute('data-area') || '';
            document.getElementById('areaDisplay').value = area;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>