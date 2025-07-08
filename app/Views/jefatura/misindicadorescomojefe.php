<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Indicadores como Jefatura – Afilogro</title>
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
                    <th>Meta Valor</th>
                    <th>Meta Descripción</th>
                    <th>Tipo de Meta</th>
                    <th>Fórmula</th>
                    <th>Unidad</th>
                    <th>Objetivo Proceso</th>
                    <th>Objetivo Calidad</th>
                    <th>Tipo Aplicación</th>
                    <th>Creado en</th>
                    <th>Periodicidad</th>
                    <th>Meta (texto)</th>
                    <th>Ponderación (%)</th>
                    <th>Resultado</th>
                    <th>Comentario</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $i): ?>
                    <?php
                        $valor      = $histMap[$i['id_indicador_perfil']]['resultado_real'] ?? '';
                        $comentario = $histMap[$i['id_indicador_perfil']]['comentario']      ?? '';
                    ?>
                    <tr>
                        <td><strong><?= esc($i['nombre']) ?></strong></td>
                        <td><?= isset($i['meta_valor']) ? esc($i['meta_valor']) : '' ?></td>
                        <td><?= isset($i['meta_descripcion']) ? esc($i['meta_descripcion']) : ''?></td>
                        <td><?= isset($i['tipo_meta']) ? esc($i['tipo_meta']) : '' ?></td>
                        <td><code><?= isset($i['metodo_calculo']) ? esc($i['metodo_calculo']) : '' ?></code></td>
                        <td><?= isset($i['unidad']) ? esc($i['unidad']) : '' ?></td>
                        <td class="small text-muted"><?= isset($i['objetivo_proceso']) ? esc($i['objetivo_proceso']) : '' ?></td>
                        <td class="small text-muted"><?= isset($i['objetivo_calidad']) ? esc($i['objetivo_calidad']) : '' ?></td>
                        <td><?= isset($i['tipo_aplicacion']) ? esc($i['tipo_aplicacion']) : '' ?></td>
                        <td><?= isset($i['created_at']) ? esc($i['created_at']) : '' ?></td>
                        <td><?= esc($i['periodicidad']) ?></td>
                        <td><?= esc($i['meta']) ?></td>
                        <td><?= esc($i['ponderacion']) ?>%</td>
                        <td>
                            <input 
                                type="text"
                                name="resultado_real[<?= $i['id_indicador_perfil'] ?>]"
                                value="<?= esc($valor) ?>"
                                class="form-control"
                            >
                        </td>
                        <td>
                            <textarea 
                                name="comentario[<?= $i['id_indicador_perfil'] ?>]"
                                class="form-control"
                                rows="1"
                            ><?= esc($comentario) ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Guardar Resultados</button>
        </div>

        <div class="text-end mt-3">
            <a href="<?= base_url('jefatura/historialmisindicadoresfeje') ?>" class="btn btn-secondary">
                Ver mi Historial
            </a>
        </div>
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
