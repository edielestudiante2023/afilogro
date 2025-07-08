<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Indicadores – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?= $this->include('partials/nav') ?>

    <div class="container py-4">
        <a href="<?= base_url('trabajador/dashboard') ?>" class="btn btn-primary mb-3">
            Ir al Dashboard del Trabajador
        </a>

        <h1 class="h3 mb-4">Mis Indicadores – Periodo <?= esc($periodo) ?></h1>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php elseif (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('trabajador/saveIndicadores') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-responsive">
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
                        <?php foreach ($items as $i):
                            $valor      = $histMap[$i['id_indicador_perfil']]['resultado_real'] ?? '';
                            $comentario = $histMap[$i['id_indicador_perfil']]['comentario']   ?? '';
                        ?>
                        <tr>
                            <td><strong><?= esc($i['nombre_indicador']) ?></strong></td>
                            <td><?= esc($i['meta_valor']) ?></td>
                            <td><?= esc($i['meta_descripcion']) ?></td>
                            <td><?= esc($i['tipo_meta']) ?></td>
                            <td><code><?= esc($i['metodo_calculo']) ?></code></td>
                            <td><?= esc($i['unidad']) ?></td>
                            <td class="small text-muted"><?= esc($i['objetivo_proceso']) ?></td>
                            <td class="small text-muted"><?= esc($i['objetivo_calidad']) ?></td>
                            <td><?= esc($i['tipo_aplicacion']) ?></td>
                            <td><?= esc($i['created_at']) ?></td>
                            <td><?= esc($i['periodicidad']) ?></td>
                            <td><?= esc($i['meta']) ?></td>
                            <td><?= esc($i['ponderacion']) ?>%</td>
                            <td>
                                <input
                                    type="text"
                                    name="resultado_real[<?= $i['id_indicador_perfil'] ?>]"
                                    value="<?= esc($valor) ?>"
                                    class="form-control"
                                />
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
        </form>
    </div>

    <?= $this->include('partials/logout') ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
