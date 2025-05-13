<!-- app/Views/jefatura/misindicadorescomojefe.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Indicadores como Jefe – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
    <h1 class="h3 mb-4">Mis Indicadores como Jefatura – Periodo <?= esc($periodo) ?></h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('jefatura/saveIndicadoresComoJefe') ?>" method="post">
        <?= csrf_field() ?>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                <tr>
                    <th>Indicador</th>
                    <th>Objetivo del Proceso</th>
                    <th>Fórmula Larga</th>
                    <th>Variables</th>
                    <th>Unidad</th>
                    <th>Periodicidad</th>
                    <th>Meta</th>
                    <th>Ponderación</th>
                    <th>Resultado</th>
                    <th>Comentario</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $i): 
                    $valor      = $histMap[$i['id_indicador_perfil']]['resultado_real'] ?? '';
                    $comentario = $histMap[$i['id_indicador_perfil']]['comentario']     ?? '';
                    $disabled   = $yaReportado ? 'disabled' : '';
                ?>
                    <tr>
                        <td><strong><?= esc($i['nombre']) ?></strong></td>
                        <td class="small text-muted"><?= esc($i['objetivo_proceso']) ?></td>
                        <td><code><?= esc($i['formula_larga']) ?></code></td>
                        <td><?= esc($i['variables']) ?></td>
                        <td><?= esc($i['unidad']) ?></td>
                        <td><?= esc($i['periodicidad']) ?></td>
                        <td><?= esc($i['meta']) ?></td>
                        <td><?= esc($i['ponderacion']) ?>%</td>
                        <td>
                            <input 
                                type="text"
                                name="resultado_real[<?= $i['id_indicador_perfil'] ?>]"
                                value="<?= esc($valor) ?>"
                                class="form-control"
                                <?= $disabled ?>>
                        </td>
                        <td>
                            <textarea
                                name="comentario[<?= $i['id_indicador_perfil'] ?>]"
                                class="form-control"
                                rows="1"
                                <?= $disabled ?>><?= esc($comentario) ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (! $yaReportado): ?>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Guardar Resultados</button>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Ya registraste tus resultados para este periodo. Para modificarlos, usa el enlace de historial.
            </div>
            <div class="text-end">
                <a href="<?= base_url('jefatura/historialmisindicadoresfeje') ?>" class="btn btn-secondary">
                    Ver mi Historial
                </a>
            </div>
        <?php endif; ?>
    </form>

    <div class="mt-4">
        <a href="<?= base_url('jefatura/jefaturadashboard') ?>" class="btn btn-link">&larr; Volver al Dashboard</a>
    </div>
</div>

<?= $this->include('partials/logout') ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
