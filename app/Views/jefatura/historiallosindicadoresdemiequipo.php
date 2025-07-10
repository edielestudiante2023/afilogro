<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Indicadores de Mi Equipo – Afilogro</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap & DataTables CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <link 
    href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" 
    rel="stylesheet"
  >
  <link 
    href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" 
    rel="stylesheet"
  >
  <style>
    html, body { height:100%; margin:0; padding:0; }
    .container-fluid { display:flex; flex-direction:column; height:100%; }
    .dataTables_wrapper .dt-buttons { margin-bottom:1rem; }
    table.dataTable { width:100% !important; }
    tfoot select { width:100%; box-sizing:border-box; }
    td .dropdown-toggle { max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    td .dropdown-menu { max-width:400px; white-space:normal; }
  </style>
</head>
<body>
  <?= $this->include('partials/nav') ?>

  <div class="container-fluid py-4 flex-grow-1">

    <!-- 1) FILTRO Desde/Hasta -->
    <form method="get" class="row g-3 mb-4" action="<?= base_url('jefatura/historiallosindicadoresdemiequipo') ?>">
      <div class="col-auto">
        <label for="fecha_desde" class="form-label">Desde:</label>
        <input 
          type="date" id="fecha_desde" name="fecha_desde"
          class="form-control"
          value="<?= esc($fecha_desde) ?>"
        >
      </div>
      <div class="col-auto">
        <label for="fecha_hasta" class="form-label">Hasta:</label>
        <input 
          type="date" id="fecha_hasta" name="fecha_hasta"
          class="form-control"
          value="<?= esc($fecha_hasta) ?>"
        >
      </div>
      <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </div>
    </form>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (empty($equipo)): ?>
      <div class="alert alert-warning">
        No hay indicadores reportados por tu equipo en este rango de fechas.
      </div>
    <?php else: ?>

      <!-- Toggle columnas -->
      <div class="mb-2">
        <button id="toggleCols" class="btn btn-secondary btn-sm">
          Para ver las columnas completas de la tabla, haz clic aquí
        </button>
      </div>

      <div class="table-responsive">
        <table 
          id="historialTable"
          class="table table-bordered table-striped align-middle nowrap w-100"
        >
          <thead class="table-dark">
            <tr>
              <th>Colaborador</th>
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
              <th>Fecha de Registro</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <?php for($i=0; $i<16; $i++): ?>
                <th></th>
              <?php endfor; ?>
            </tr>
          </tfoot>
          <tbody>
            <?php foreach($equipo as $r): ?>
            <tr>
              <td><?= esc($r['nombre_completo']) ?></td>
              <td><?= esc($r['nombre_indicador']) ?></td>
              <td><?= esc($r['meta_valor']) ?></td>
              <td><?= esc($r['meta_descripcion']) ?></td>
              <td><?= esc($r['tipo_meta']) ?></td>
              <td><code><?= esc($r['metodo_calculo']) ?></code></td>
              <td><?= esc($r['unidad']) ?></td>
              <td>
                <div class="dropdown">
                  <a 
                    class="dropdown-toggle" data-bs-toggle="dropdown"
                    title="<?= esc($r['objetivo_proceso']) ?>"
                  ><?= esc($r['objetivo_proceso']) ?></a>
                  <div class="dropdown-menu p-3">
                    <?= nl2br(esc($r['objetivo_proceso'])) ?>
                  </div>
                </div>
              </td>
              <td>
                <div class="dropdown">
                  <a 
                    class="dropdown-toggle" data-bs-toggle="dropdown"
                    title="<?= esc($r['objetivo_calidad']) ?>"
                  ><?= esc($r['objetivo_calidad']) ?></a>
                  <div class="dropdown-menu p-3">
                    <?= nl2br(esc($r['objetivo_calidad'])) ?>
                  </div>
                </div>
              </td>
              <td><?= esc($r['tipo_aplicacion']) ?></td>
              <td><?= esc($r['created_at']) ?></td>
              <td><?= esc($r['periodicidad']) ?></td>
              <td><?= esc($r['ponderacion']) ?>%</td>
              <td><?= esc($r['resultado_real']) ?></td>
              <td><?= esc($r['comentario']) ?: '—' ?></td>
              <td><?= esc($r['fecha_registro']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="mt-4">
      <a href="<?= base_url('jefatura/jefaturadashboard') ?>"
         class="btn btn-secondary">&larr; Volver al Dashboard</a>
    </div>
  </div>

  <?= $this->include('partials/logout') ?>

  <!-- JS: jQuery, Bootstrap, DataTables + Buttons, JSZip -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

  <script>
  $(document).ready(function() {
    // Índices 1-indexed: el 7º th es Objetivo Proceso → 0-indexed = 7
    var hiddenCols = [7,8,9,10,11]; // ObjProc, ObjCal, TipoAp, CreadoEn, Periodicidad-texto

    var table = $('#historialTable').DataTable({
      scrollX: true,
      dom: 'Bfrtip',
      buttons: [
        { extend: 'excelHtml5', title: 'Historial_Equipo' }
      ],
      order: [[15, 'desc']],  // Fecha de Registro (índice 15, 0-based)
      columnDefs: [
        { targets: hiddenCols, visible: false }
      ],
      initComplete: function() {
        var api = this.api();
        api.columns().every(function() {
          var idx = this.index();
          if (hiddenCols.includes(idx)) return;

          var $footer = $(this.footer()).empty();
          var $select = $('<select class="form-select form-select-sm"><option value="">Todos</option></select>')
            .appendTo($footer)
            .on('change', function() {
              var val = $.fn.dataTable.util.escapeRegex($(this).val());
              api.column(idx).search(val ? '^'+val+'$' : '', true, false).draw();
            });

          this.data().unique().sort().each(function(d) {
            if (d!=null && d!=='') {
              var text = $('<div>').html(d).text();
              $select.append('<option value="'+text+'">'+text+'</option>');
            }
          });
        });
      }
    });

    // Toggle columnas
    var expanded = false;
    $('#toggleCols').on('click', function() {
      expanded = !expanded;
      table.columns(hiddenCols).visible(expanded);
      $(this).text(expanded
        ? 'Ocultar columnas completas'
        : 'Para ver las columnas completas de la tabla, haz clic aquí'
      );
    });
  });
  </script>
</body>
</html>
