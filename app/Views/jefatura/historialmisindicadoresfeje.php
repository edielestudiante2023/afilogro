
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Mis Indicadores – Jefatura</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?= $this->include('partials/nav') ?>
    <div class="container py-4">
        <a href="<?= base_url('jefatura/jefaturadashboard') ?>" class="btn btn-secondary">
            ← Volver al Dashboard
        </a>
        <h1 class="h3 mb-4">Historial de Mis Indicadores</h1>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (empty($historial)): ?>
            <div class="alert alert-warning">No hay registros en tu historial.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Indicador</th>
                            <th>Objetivo del Proceso</th>
                            <th>Fórmula Larga</th>
                            <th>Meta</th>
                            <th>Ponderación</th>
                            <th>Unidad</th>
                            <th>Periodo</th>
                            <th>Resultado</th>
                            <th>Comentario</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $r): ?>
                            <tr>
                                <td><strong><?= esc($r['nombre']) ?></strong></td>
                                <td class="small text-muted"><?= esc($r['objetivo_proceso']) ?></td>
                                <td><code><?= esc($r['formula_larga']) ?></code></td>
                                <td><?= esc($r['meta']) ?></td>
                                <td><?= esc($r['ponderacion']) ?>%</td>
                                <td><?= esc($r['unidad']) ?></td>
                                <td><?= esc($r['periodo']) ?></td>
                                <td><?= esc($r['resultado_real']) ?></td>
                                <td><?= esc($r['comentario']) ?></td>
                                <td><?= esc($r['fecha_registro']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        <?php endif; ?>
    </div>
    <?= $this->include('partials/logout') ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>