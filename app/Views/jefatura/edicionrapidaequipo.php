<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Edición Rápida de Indicadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container py-4">
        <h3 class="mb-4">✍️ Edición Rápida de Indicadores del Equipo – <?= esc($periodo) ?></h3>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('edicion-indicadores/guardar') ?>">
            <?= csrf_field() ?>

            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha de Registro</th>

                        <th>Trabajador</th>
                        <th>Indicador</th>
                        <th>Resultado Real</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipo as $item): ?>
                        <tr>
                            <td class="text-muted small">
                                <?= esc(date('Y-m-d H:i', strtotime($item['fecha_registro']))) ?>
                            </td>

                            <td><?= esc($item['nombre_completo']) ?></td>
                            <td><?= esc($item['nombre_indicador']) ?></td>
                            <td>
                                <input type="text" name="cambios[<?= $item['id_historial'] ?>][resultado_real]"
                                    class="form-control"
                                    value="<?= esc($item['resultado_real']) ?>">
                            </td>
                            <td>
                                <input type="text" name="cambios[<?= $item['id_historial'] ?>][comentario]"
                                    class="form-control"
                                    value="<?= esc($item['comentario']) ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>