<!-- app/Views/jefatura/historiallosindicadoresdemiequipo.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Indicadores de Mi Equipo – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?= $this->include('partials/nav') ?>

    <div class="container py-4">
        <h1 class="h3 mb-4">Indicadores de Mi Equipo – Periodo </h1>

        <form method="get" action="<?= base_url('jefatura/historiallosindicadoresdemiequipo') ?>" class="mb-4">
            <label for="periodo" class="form-label">Seleccionar Periodo:</label>
            <input type="month" name="periodo" id="periodo" value="<?= esc($periodo) ?>" class="form-control" style="max-width: 200px;" />
            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
        </form>


        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (empty($equipo)): ?>
            <div class="alert alert-warning">No hay indicadores reportados por tu equipo en este periodo.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Colaborador</th>
                            <th>Indicador</th>
                            <th>Fórmula</th>
                            <th>Resultado</th>
                            <th>Comentario</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipo as $r): ?>
                            <tr>
                                <td><?= esc($r['nombre_completo']) ?></td>
                                <td><?= esc($r['nombre_indicador']) ?></td>
                                <td><code><?= esc($r['formula_larga']) ?></code></td>
                                <td><?= esc($r['resultado_real']) ?></td>
                                <td><?= esc($r['comentario']) ?: '—' ?></td>
                                <td><?= esc($r['fecha_registro']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= base_url('jefatura/jefaturadashboard') ?>" class="btn btn-secondary">
                ← Volver al Dashboard
            </a>
        </div>
    </div>

    <?= $this->include('partials/logout') ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>