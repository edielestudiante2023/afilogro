<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Indicadores del Equipo – Edición</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <!-- Datepicker CSS -->
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <style>
    /* Fondo lavanda para la celda y el input */
    .bg-lavanda {
      background-color: #EDE7F6 !important;
    }
    .bg-lavanda .form-control {
      background-color: #EDE7F6 !important;
      border-color: #D1C4E9 !important;
    }

    html,
    body {
      height: 100%;
    }

    .dataTables_scrollBody {
      max-height: 70vh !important;
    }
  </style>
</head>

<body>
  <?= $this->include('partials/nav') ?>

  <div class="container-fluid py-4">
    <h1 class="h3 mb-4">Editar Indicadores – Equipo</h1>

    <!-- FILTRO por rango de fechas -->
    <form method="get" action="<?= base_url('jefatura/losindicadoresdemiequipo') ?>" class="row g-3 mb-4">
      <div class="col-auto">
        <label for="fecha_desde" class="form-label">Fecha Desde:</label>
        <input type="text" id="fecha_desde" name="fecha_desde" value="<?= esc($fecha_desde) ?>" class="form-control datepicker" placeholder="YYYY-MM-DD">
      </div>
      <div class="col-auto">
        <label for="fecha_hasta" class="form-label">Fecha Hasta:</label>
        <input type="text" id="fecha_hasta" name="fecha_hasta" value="<?= esc($fecha_hasta) ?>" class="form-control datepicker" placeholder="YYYY-MM-DD">
      </div>
      <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </div>
    </form>

    <!-- Tabla edición -->
    <form method="post" action="<?= base_url('jefatura/guardarIndicadoresDeEquipo') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="fecha_desde" value="<?= esc($fecha_desde) ?>">
      <input type="hidden" name="fecha_hasta" value="<?= esc($fecha_hasta) ?>">

      <div class="table-responsive">
        <table id="edicionTable" class="table table-striped nowrap w-100" style="width:100%">
          <thead class="table-dark">
            <tr>
              <th>Trabajador</th>
              <th>Indicador</th>
              <th>Meta Valor</th>
              <th>Tipo Meta</th>
              <th>Fórmula</th>
              <th>Unidad</th>
              <th>Resultado Real</th>
              <th>Comentario</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th><select class="form-select form-select-sm"><option value="">Todos</option></select></th>
              <th><select class="form-select form-select-sm"><option value="">Todos</option></select></th>
              <th></th>
              <th><select class="form-select form-select-sm"><option value="">Todos</option></select></th>
              <th></th>
              <th><select class="form-select form-select-sm"><option value="">Todos</option></select></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
          <tbody>
            <?php foreach ($equipo as $item): ?>
              <tr>
                <td><?= esc($item['nombre_completo']) ?></td>
                <td><?= esc($item['nombre_indicador']) ?></td>
                <td><?= esc($item['meta_valor']) ?></td>
                <td><?= esc($item['tipo_meta']) ?></td>
                <td><code><?= esc($item['metodo_calculo']) ?></code></td>
                <td><?= esc($item['unidad']) ?></td>
                <td class="bg-lavanda">
                  <input
                    type="text"
                    name="cambios[<?= $item['id_historial'] ?>][resultado_real]"
                    class="form-control form-control-sm"
                    value="<?= esc($item['resultado_real']) ?>">
                </td>
                <td>
                  <input type="text" name="cambios[<?= $item['id_historial'] ?>][comentario]"
                    class="form-control form-control-sm" value="<?= esc($item['comentario']) ?>">
                </td>
                <td>
                  <button type="submit" formaction="<?= base_url('jefatura/guardarIndicadoresDeEquipo') ?>"
                    formmethod="post" class="btn btn-sm btn-success" name="enviar[<?= $item['id_historial'] ?>]">
                    Enviar
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </form>

    <div class="mt-4">
      <a href="<?= base_url('jefatura/jefaturadashboard') ?>" class="btn btn-secondary">&larr; Volver</a>
      <a href="<?= base_url('jefatura/historiallosindicadoresdemiequipo') ?>" class="btn btn-warning ms-2">Historial</a>
    </div>
  </div>

  <?= $this->include('partials/logout') ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function() {
      // Inicializa flatpickr
      $('.datepicker').flatpickr({ dateFormat: 'Y-m-d' });

      // DataTable con scrollX y filtros select
      $('#edicionTable').DataTable({
        scrollX: true,
        autoWidth: false,
        initComplete: function() {
          this.api().columns().every(function() {
            const column = this;
            const footerSelect = $('select', column.footer());
            if (footerSelect.length) {
              column.data().unique().sort().each(function(d) {
                footerSelect.append('<option value="' + d + '">' + d + '</option>');
              });
              footerSelect.on('change', function() {
                const val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });
            }
          });
        }
      });
    });
  </script>
</body>

</html>
