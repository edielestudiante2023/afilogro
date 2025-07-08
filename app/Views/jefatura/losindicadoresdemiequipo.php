<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Indicadores Actuales de Mi Equipo – Afilogro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?= $this->include('partials/nav') ?>

    <div class="container py-4">
        <h1 class="h3 mb-4">Indicadores del Equipo – Periodo <?= esc($periodo) ?></h1>

        <form method="get" action="<?= base_url('jefatura/losindicadoresdemiequipo') ?>" class="mb-4">
            <label for="periodo" class="form-label">Seleccionar Periodo:</label>
            <input type="month" name="periodo" id="periodo" value="<?= esc($periodo) ?>" class="form-control" style="max-width: 200px;" />
            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
        </form>

        <form method="post" action="<?= base_url('jefatura/saveIndicadoresComoJefe') ?>">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Trabajador</th>
                            <th>Indicador</th>
                            <th>Meta Valor</th>
                            <th>Meta Descripción</th>
                            <th>Tipo Meta</th>
                            <th>Fórmula</th>
                            <th>Unidad</th>
                            <th>Objetivo Proceso</th>
                            <th>Objetivo Calidad</th>
                            <th>Tipo Aplicación</th>
                            <th>Creado en</th>
                            <th>Periodicidad</th>
                            <th>Ponderación (%)</th>
                            <th>Resultado Real</th>
                            <th>Comentario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipo as $indicador): ?>
                            <?php $clave = $indicador['id_indicador_perfil'] . '_' . $indicador['id_usuario']; ?>
                            <tr>
                                <td>
                                    <?= esc($indicador['nombre_completo']) ?>
                                    <input type="hidden" name="id_usuario[<?= $clave ?>]" value="<?= esc($indicador['id_usuario']) ?>">
                                </td>
                                <td><?= esc($indicador['nombre_indicador']) ?></td>
                                <td><?= esc($indicador['meta_valor']) ?></td>
                                <td><?= esc($indicador['meta_descripcion']) ?></td>
                                <td><?= esc($indicador['tipo_meta']) ?></td>
                                <td><code><?= esc($indicador['metodo_calculo']) ?></code></td>
                                <td><?= esc($indicador['unidad']) ?></td>
                                <td class="small text-muted"><?= esc($indicador['objetivo_proceso']) ?></td>
                                <td class="small text-muted"><?= esc($indicador['objetivo_calidad']) ?></td>
                                <td><?= esc($indicador['tipo_aplicacion']) ?></td>
                                <td><?= esc($indicador['created_at']) ?></td>
                                <td><?= esc($indicador['periodicidad']) ?></td>
                                <td><?= esc($indicador['ponderacion']) ?>%</td>
                                <td>
                                    <input type="text" name="resultado_real[<?= $clave ?>]" value="<?= esc($indicador['resultado_real']) ?>" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="comentario[<?= $clave ?>]" value="<?= esc($indicador['comentario']) ?>" class="form-control">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-success">Guardar Cambios</button>
        </form>

        <div class="mt-4">
            <a href="<?= base_url('jefatura/jefaturadashboard') ?>" class="btn btn-secondary">&larr; Volver al Dashboard</a>
            <a href="<?= base_url('jefatura/historiallosindicadoresdemiequipo') ?>" class="btn btn-warning ms-2">Ver Historial de Equipo</a>
        </div>
    </div>

    <?= $this->include('partials/logout') ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
