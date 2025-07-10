<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Indicadores como Jefatura – Afilogro</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <style>
    html, body { height:100%; margin:0; padding:0; }
    .container-fluid { display:flex; flex-direction:column; height:100%; }
    .table-responsive { flex-grow:1; }
  </style>
</head>
<body>
  <?= $this->include('partials/nav') ?>

  <div class="container-fluid py-4">
    <h1 class="h3 mb-4">
      Mis Indicadores como Jefatura – Periodo <?= esc($periodo) ?>
    </h1>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="table-responsive">
      <form method="post" action="<?= base_url('jefatura/saveIndicadoresComoJefe') ?>">
        <?= csrf_field() ?>

        <table class="table table-bordered align-middle w-100">
          <thead class="table-dark">
            <tr>
              <th>Indicador</th>
              <th>Meta Valor</th>
              <th>Meta Descripción</th>
              <th>Tipo Meta</th>
              <th>Fórmula</th>
              <th>Unidad</th>
              <th>Objetivo Proceso</th>
              <th>Objetivo Calidad</th>
              <th>Tipo Aplicación</th>
              <!-- <th>Creado en</th> -->
              <th>Periodicidad</th>
              <th>Meta (texto)</th>
              <th>Ponderación (%)</th>
              <th>Resultado</th>
              <th>Comentario</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($items as $i): 
              $id = $i['id_indicador_perfil'];
            ?>
            <tr>
              <td><?= esc($i['nombre_indicador']) ?></td>
              <td><?= esc($i['meta_valor']) ?></td>
              <td><?= esc($i['meta_descripcion']) ?></td>
              <td><?= esc($i['tipo_meta']) ?></td>
              <td><code><?= esc($i['metodo_calculo']) ?></code></td>
              <td><?= esc($i['unidad']) ?></td>
              <td class="small"><?= esc($i['objetivo_proceso']) ?></td>
              <td class="small"><?= esc($i['objetivo_calidad']) ?></td>
              <td><?= esc($i['tipo_aplicacion']) ?></td>
              <!-- <td><?= esc($i['created_at']) ?></td> -->
              <td><?= esc($i['periodicidad']) ?></td>
              <td><?= esc($i['meta']) ?></td>
              <td><?= esc($i['ponderacion']) ?>%</td>
              <td>
                <input
                  type="text"
                  name="resultado_real[<?= $id ?>]"
                  class="form-control resultado-input"
                  data-id="<?= $id ?>"
                  placeholder="Ingresa resultado"
                >
              </td>
              <td>
                <textarea
                  name="comentario[<?= $id ?>]"
                  class="form-control comentario-input"
                  data-id="<?= $id ?>"
                  rows="1"
                  placeholder="Comentario (opcional)"
                ></textarea>
              </td>
              <td>
                <button
                  type="submit"
                  name="single"
                  value="<?= $id ?>"
                  class="btn btn-sm btn-success single-save-btn"
                  disabled
                >Guardar</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>

  <?= $this->include('partials/logout') ?>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
  <script>
    $(function(){
      // Habilita el botón “Guardar” cuando el input de resultado no esté vacío
      $('.resultado-input').on('input', function(){
        var id  = $(this).data('id');
        var val = $(this).val().trim();
        $('button.single-save-btn[value="'+id+'"]').prop('disabled', val === '');
      });
    });
  </script>
</body>
</html>
