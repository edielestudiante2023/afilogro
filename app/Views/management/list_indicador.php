<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Indicadores – Afilogro</title>

  <!-- Bootstrap & DataTables CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

  <style>
    #indicadorTable {
      width: 100% !important;
      table-layout: fixed;
      font-family: Arial, sans-serif;
      font-size: 0.875rem;
    }
    #indicadorTable tbody tr { height: 3rem; }
    .cell-content {
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      height: 3rem;
      line-height: 3rem;
    }
    .action-cell {
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 0.2rem;
      height: 100%;
    }
    .action-cell .btn {
      padding: 0.2rem 0.4rem;
      font-size: 0.75rem;
      line-height: 1rem;
    }
    tfoot input {
      width: 100%;
      box-sizing: border-box;
      padding: 0.2rem;
      font-size: 0.875rem;
    }
  </style>
</head>

<body class="p-0">
  <?= $this->include('partials/nav') ?>

  <div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Listado de Indicadores</h1>
      <div>
        <button id="resetFilters" class="btn btn-secondary me-2">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer filtros
        </button>
        <a href="<?= base_url('indicadores/add') ?>" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Nuevo Indicador
        </a>
      </div>
    </div>

    <!-- Div de búsqueda personalizado para Nombre Indicador -->
    <div id="nombreFilter" class="mb-4 d-flex align-items-end">
      <div class="me-3">
        <label for="filterNameDropdown" class="form-label">Buscar Nombre Indicador (Lista)</label>
        <select id="filterNameDropdown" class="form-select">
          <option value="">Todos</option>
          <?php foreach (array_unique(array_column($indicadores, 'nombre')) as $nombre): ?>
            <option value="<?= esc($nombre) ?>"><?= esc($nombre) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="filterNameSelect2" class="form-label">Buscar Nombre Indicador (Texto)</label>
        <select id="filterNameSelect2" class="form-select"></select>
      </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <table id="indicadorTable" class="table table-striped table-bordered nowrap w-100">
      <thead class="table-dark align-middle">
        <tr>
          <th>Nombre Indicador</th>
          <th>Meta Valor</th>
          <th>Meta Descripción</th>
          <th>Tipo Meta</th>
          <th>Fórmula</th>
          <th>Unidad</th>
          <th>Objetivo Proceso</th>
          <th>Objetivo Calidad</th>
          <th>Tipo Aplicación</th>
          <th>Activo</th>
          <th>Periodicidad</th>
          <th>Ponderación (%)</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tfoot class="table-dark align-middle">
        <tr>
          <!-- Eliminamos el input de filtro aquí para Nombre -->
          <th></th>
          <th><input type="text" placeholder="Buscar Meta Valor"></th>
          <th><input type="text" placeholder="Buscar Descripción"></th>
          <th><input type="text" placeholder="Buscar Tipo Meta"></th>
          <th><input type="text" placeholder="Buscar Fórmula"></th>
          <th><input type="text" placeholder="Buscar Unidad"></th>
          <th><input type="text" placeholder="Buscar Obj. Proceso"></th>
          <th><input type="text" placeholder="Buscar Obj. Calidad"></th>
          <th><input type="text" placeholder="Buscar Tipo Aplicación"></th>
          <th><input type="text" placeholder="Buscar Activo"></th>
          <th><input type="text" placeholder="Buscar Periodicidad"></th>
          <th><input type="text" placeholder="Buscar % Ponderación"></th>
          <th><input type="text" placeholder="Buscar Acciones"></th>
        </tr>
      </tfoot>
      <tbody>
        <?php foreach ($indicadores as $i): ?>
          <tr>
            <td>
              <div class="cell-content" data-bs-toggle="tooltip" title="<?= esc($i['nombre']) ?>">
                <?= esc($i['nombre']) ?>
              </div>
            </td>
            <td><?= esc($i['meta_valor'] ?? '—') ?></td>
            <td>
              <div class="cell-content" data-bs-toggle="tooltip" title="<?= esc($i['meta_descripcion'] ?? '') ?>">
                <?= esc($i['meta_descripcion'] ?? '—') ?>
              </div>
            </td>
            <td><?= esc($i['tipo_meta'] ?? '—') ?></td>
            <td>
              <div class="cell-content" data-bs-toggle="tooltip" title="<?= esc($i['formula_renderizada']) ?>">
                <?= esc($i['formula_renderizada']) ?>
              </div>
            </td>
            <td><?= esc($i['unidad'] ?? '—') ?></td>
            <td>
              <div class="cell-content" data-bs-toggle="tooltip" title="<?= esc($i['objetivo_proceso'] ?? '') ?>">
                <?= esc($i['objetivo_proceso'] ?? '—') ?>
              </div>
            </td>
            <td class="small text-muted">
              <div class="cell-content" data-bs-toggle="tooltip" title="<?= esc($i['objetivo_calidad'] ?? '') ?>">
                <?= esc($i['objetivo_calidad'] ?? '—') ?>
              </div>
            </td>
            <td><?= esc($i['tipo_aplicacion'] ?? '—') ?></td>
            <td><?= isset($i['activo']) ? ($i['activo'] ? 'Sí' : 'No') : '—' ?></td>
            <td><?= esc($i['periodicidad'] ?? '—') ?></td>
            <td><?= esc($i['ponderacion'] ?? '0') ?>%</td>
            <td class="text-center">
              <div class="action-cell">
                <a href="<?= base_url('indicadores/edit/' . $i['id_indicador']) ?>" class="btn btn-warning">Editar</a>
                <a href="<?= base_url('indicadores/delete/' . $i['id_indicador']) ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar este indicador?')">Eliminar</a>
                <a href="<?= base_url('indicadores/fill/' . $i['id_indicador']) ?>" class="btn btn-info">Diligenciar</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?= $this->include('partials/logout') ?>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    const nameStorageKey = 'indicadorNameFilter';

    $(document).ready(function() {
      // Inicializa DataTable
      const table = $('#indicadorTable').DataTable({
        responsive: true,
        autoWidth: false,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        initComplete: function() {
          // filtros en el <tfoot>
          this.api().columns().every(function(idx) {
            if (idx === 0) return; // omitimos la columna Nombre
            const column = this;
            const input = $('input', column.footer());
            input.on('keyup change clear', function() {
              if (column.search() !== this.value) {
                column.search(this.value).draw();
              }
            });
          });
        }
      });

      // Tooltips de Bootstrap
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
      });

      // Prepara datos únicos de nombres
      const indicatorNames = <?= json_encode(array_values(array_unique(array_column($indicadores, 'nombre')))); ?>;

      // Inicializa Select2
      $('#filterNameSelect2').select2({
        data: indicatorNames.map(n => ({ id: n, text: n })),
        placeholder: 'Buscar texto…',
        allowClear: true,
        width: '200px'
      });

      // Carga filtro guardado
      const saved = JSON.parse(localStorage.getItem(nameStorageKey) || '{}');
      if (saved.dropdown) {
        $('#filterNameDropdown').val(saved.dropdown);
        table.column(0).search(saved.dropdown).draw();
      }
      if (saved.select2) {
        $('#filterNameSelect2').val(saved.select2).trigger('change');
        table.column(0).search(saved.select2).draw();
      }

      // Evento cambio en lista desplegable
      $('#filterNameDropdown').on('change', function() {
        const val = this.value;
        // limpia select2
        $('#filterNameSelect2').val(null).trigger('change');
        // aplica filtro
        table.column(0).search(val).draw();
        // guarda estado
        localStorage.setItem(nameStorageKey, JSON.stringify({ dropdown: val }));
      });

      // Evento cambio en select2
      $('#filterNameSelect2').on('change', function() {
        const val = this.value || '';
        // limpia dropdown
        $('#filterNameDropdown').val('');
        // aplica filtro
        table.column(0).search(val).draw();
        // guarda estado
        localStorage.setItem(nameStorageKey, JSON.stringify({ select2: val }));
      });

      // Botón Restablecer filtros
      $('#resetFilters').on('click', function() {
        localStorage.removeItem(nameStorageKey);
        $('#filterNameDropdown').val('');
        $('#filterNameSelect2').val(null).trigger('change');
        table.columns().every(function() {
          this.search('');
          if (this.index() !== 0) {
            $('input', this.footer()).val('');
          }
        });
        table.draw();
      });
    });
  </script>
</body>
</html>
