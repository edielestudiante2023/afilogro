<!DOCTYPE html>
<html lang="es">
<head>
  <!-- ... tus <meta> y CSS ... -->
</head>
<body>
  <?= $this->include('partials/nav') ?>

  <div class="container-fluid py-4">
    <h1 class="h3 mb-4">Indicadores del Equipo – Periodo <?= esc($periodo) ?></h1>

    <!-- FILTRO por mes -->
    <form method="get"
          action="<?= base_url('jefatura/losindicadoresdemiequipo') ?>"
          class="mb-4">
      <div class="row g-3 align-items-end">
        <div class="col-auto">
          <label for="periodo" class="form-label">Periodo:</label>
          <input type="month"
                 name="periodo"
                 id="periodo"
                 value="<?= esc($periodo) ?>"
                 class="form-control">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
      </div>
    </form>

    <!-- TOGGLE COLUMNAS -->
    <div class="mb-2">
      <button id="toggleCols" class="btn btn-secondary btn-sm">
        Para ver las columnas completas de la tabla, haz clic aquí
      </button>
    </div>

    <!-- FORM de guardado -->
    <form method="post" action="<?= base_url('jefatura/guardarIndicadoresDeEquipo') ?>">
      <?= csrf_field() ?>
      <!-- Mantenemos el periodo en el POST para redirección -->
      <input type="hidden" name="periodo" value="<?= esc($periodo) ?>">

      <div class="table-responsive">
        <table id="edicionTable" class="table table-bordered nowrap w-100">
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
              <th>Fecha Registro</th>
              <th>Periodicidad</th>
              <th>Ponderación (%)</th>
              <th>Resultado Real</th>
              <th>Comentario</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <?php for ($i = 0; $i < 16; $i++): ?>
                <th><input class="form-control form-control-sm" placeholder="Buscar…"></th>
              <?php endfor; ?>
            </tr>
          </tfoot>
          <tbody>
            <?php foreach ($equipo as $indicador): ?>
              <tr>
                <td><?= esc($indicador['nombre_completo']) ?></td>
                <td><?= esc($indicador['nombre_indicador']) ?></td>
                <td><?= esc($indicador['meta_valor']) ?></td>
                <td><?= esc($indicador['meta_descripcion']) ?></td>
                <td><?= esc($indicador['tipo_meta']) ?></td>
                <td><code><?= esc($indicador['metodo_calculo']) ?></code></td>
                <td><?= esc($indicador['unidad']) ?></td>
                <td>
                  <div class="dropdown">
                    <a class="dropdown-toggle" data-bs-toggle="dropdown" title="<?= esc($indicador['objetivo_proceso']) ?>">
                      <?= esc($indicador['objetivo_proceso']) ?>
                    </a>
                    <div class="dropdown-menu p-3">
                      <?= nl2br(esc($indicador['objetivo_proceso'])) ?>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="dropdown">
                    <a class="dropdown-toggle" data-bs-toggle="dropdown" title="<?= esc($indicador['objetivo_calidad']) ?>">
                      <?= esc($indicador['objetivo_calidad']) ?>
                    </a>
                    <div class="dropdown-menu p-3">
                      <?= nl2br(esc($indicador['objetivo_calidad'])) ?>
                    </div>
                  </div>
                </td>
                <td><?= esc($indicador['tipo_aplicacion']) ?></td>
                <td><?= esc($indicador['created_at']) ?></td>
                <td><?= esc($indicador['fecha_registro']) ?></td>
                <td><?= esc($indicador['periodicidad']) ?></td>
                <td><?= esc($indicador['ponderacion']) ?>%</td>
                <td>
                  <input type="text"
                         name="cambios[<?= $indicador['id_historial'] ?>][resultado_real]"
                         class="form-control"
                         value="<?= esc($indicador['resultado_real']) ?>">
                </td>
                <td>
                  <input type="text"
                         name="cambios[<?= $indicador['id_historial'] ?>][comentario]"
                         class="form-control"
                         value="<?= esc($indicador['comentario']) ?>">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </form>

    <div class="mt-4">
      <a href="<?= base_url('jefatura/jefaturadashboard') ?>" class="btn btn-secondary">
        &larr; Volver al Dashboard
      </a>
      <a href="<?= base_url('jefatura/historiallosindicadoresdemiequipo') ?>" class="btn btn-warning ms-2">
        Ver Historial de Equipo
      </a>
    </div>
  </div>

  <?= $this->include('partials/logout') ?>
  <!-- scripts JS… -->
</body>
</html>
