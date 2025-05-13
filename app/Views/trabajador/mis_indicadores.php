<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Indicadores – Afilogro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?= $this->include('partials/nav') ?>

    <a href="<?= base_url('trabajador/dashboard') ?>" class="btn btn-primary">
        Ir al Dashboard del Trabajador
    </a>

    <div class="container py-4">


        <h1 class="h3 mb-4">Mis Indicadores – Periodo <?= esc($periodo) ?></h1>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if ($yaReportado): ?>
            <div class="alert alert-info">Ya has registrado los resultados para este periodo. Si necesitas hacer cambios, contacta con tu jefe o administrador.</div>
        <?php endif; ?>

        <form action="<?= base_url('trabajador/saveIndicadores') ?>" method="post">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
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
                        <?php foreach ($items as $i): ?>
                            <?php $valor = $histMap[$i['id_indicador_perfil']]['resultado_real'] ?? ''; ?>
                            <?php $comentario = $histMap[$i['id_indicador_perfil']]['comentario'] ?? ''; ?>
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
                                    <input name="resultado_real[<?= $i['id_indicador_perfil'] ?>]"
                                        value="<?= esc($valor) ?>"
                                        class="form-control"
                                        <?= $yaReportado ? 'readonly' : '' ?>>
                                </td>
                                <td>
                                    <textarea name="comentario[<?= $i['id_indicador_perfil'] ?>]"
                                        class="form-control"
                                        rows="1"
                                        <?= $yaReportado ? 'readonly' : '' ?>><?= esc($comentario) ?></textarea>
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
            <?php endif; ?>
        </form>
    </div>

    <?= $this->include('partials/logout') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>