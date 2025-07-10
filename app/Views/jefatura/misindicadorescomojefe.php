<!DOCTYPE html>
<html lang="es">
<head>
  <!-- tus <meta> y CSS… -->
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container-fluid py-4">
  <h1 class="h3 mb-4">
    Mis Indicadores como Jefatura –
    Desde <?= esc($fecha_desde) ?> Hasta <?= esc($fecha_hasta) ?>
  </h1>

  <!-- 1) El filtro GET -->
  <form method="get" action="<?= base_url('jefatura/misindicadorescomojefe') ?>" class="mb-4">
    <!-- inputs date Desde/Hasta y botón Filtrar -->
  </form>

  <!-- 2) ¡Empieza el único form POST! -->
  <form method="post" action="<?= base_url('jefatura/saveIndicadoresComoJefe') ?>">
    <?= csrf_field() ?>
    <!-- conservamos las fechas -->
    <input type="hidden" name="fecha_desde" value="<?= esc($fecha_desde) ?>">
    <input type="hidden" name="fecha_hasta" value="<?= esc($fecha_hasta) ?>">

    <!-- 3) Toggle columnas, DataTable y tabla -->
    <div class="mb-2">
      <button id="toggleCols" class="btn btn-secondary btn-sm">
        Para ver las columnas completas de la tabla, haz clic aquí
      </button>
    </div>

    <div class="table-responsive">
      <table id="misindicadoresTable" class="table table-bordered nowrap w-100">
        <thead class="table-dark"> 
          <!-- tus <th>… -->
        </thead>
        <tfoot>
          <tr>
            <?php for($i=0;$i<15;$i++): ?>
              <th><input class="form-control form-control-sm" placeholder="Buscar…"></th>
            <?php endfor; ?>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach($items as $i): 
            // NO prefillees value con histMap: deja vacío
          ?>
          <tr>
            <td><?= esc($i['nombre_indicador']) ?></td>
            <!-- … otras celdas … -->
            <td>
              <input
                type="text"
                name="resultado_real[<?= $i['id_indicador_perfil'] ?>]"
                value=""
                placeholder="Ingresa resultado"
                class="form-control"
              >
            </td>
            <td>
              <textarea
                name="comentario[<?= $i['id_indicador_perfil'] ?>]"
                class="form-control"
                rows="1"
              ></textarea>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
      <button type="submit" class="btn btn-primary">Guardar Resultados</button>
    </div>
  </form>
</div>

<?= $this->include('partials/logout') ?>
<!-- tus scripts… -->
</body>
</html>
