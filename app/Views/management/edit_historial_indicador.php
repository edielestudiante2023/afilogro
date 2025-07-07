<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Historial Indicador – Afilogro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <?= $this->include('partials/nav') ?>

  <div class="container py-4">
    <h1 class="h3 mb-4">Editar Registro Historial</h1>

    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger">
        <?php foreach (session()->getFlashdata('errors') as $e): ?><p><?= esc($e) ?></p><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('historial_indicador/edit/' . $record['id_historial']) ?>" method="post">
      <?= csrf_field() ?>

      <!-- Indicador x Perfil -->
      <div class="mb-3">
        <label class="form-label">Asignación Indicador x Perfil</label>
        <select name="id_indicador_perfil" class="form-select" required>
          <?php foreach ($asignaciones as $a): ?>
            <option value="<?= $a['id_indicador_perfil'] ?>"
              <?= $a['id_indicador_perfil'] == $record['id_indicador_perfil'] ? 'selected' : '' ?>>
              <?= esc($a['nombre_indicador'] ?? $a['nombre'] ?? 'Indicador sin nombre') ?>
              -
              <?= esc($a['nombre_cargo'] ?? '(Sin cargo definido)') ?>

            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Usuario -->
      <div class="mb-3">
        <label class="form-label">Usuario</label>
        <select name="id_usuario" class="form-select" required>
          <?php foreach ($users as $u): ?>
            <option value="<?= esc($u['id_users']) ?>" <?= set_select('id_usuario', $u['id_users'], $record['id_usuario'] == $u['id_users']) ?>>
              <?= esc($u['nombre_completo']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Periodicidad -->
      <div class="mb-3">
        <label class="form-label">Periodicidad</label>
        <input type="text" class="form-control" value="<?= esc($record['periodicidad'] ?? '-') ?>" readonly>
      </div>

      <!-- Ponderación -->
      <div class="mb-3">
        <label class="form-label">Ponderación</label>
        <input type="text" class="form-control" value="<?= esc($record['ponderacion']) ?>%" readonly>
      </div>

      <!-- Meta Valor -->
      <div class="mb-3">
        <label class="form-label">Meta Valor</label>
        <input type="text" class="form-control" value="<?= esc($record['meta_valor']) ?>" readonly>
      </div>

      <!-- Meta Descripción -->
      <div class="mb-3">
        <label class="form-label">Meta Descripción</label>
        <input type="text" class="form-control" value="<?= esc($record['meta_descripcion']) ?>" readonly>
      </div>

      <!-- Tipo Meta -->
      <div class="mb-3">
        <label class="form-label">Tipo Meta</label>
        <input type="text" class="form-control" value="<?= esc($record['tipo_meta']) ?>" readonly>
      </div>

      <!-- Método de Cálculo -->
      <div class="mb-3">
        <label class="form-label">Método de Cálculo</label>
        <input type="text" class="form-control" value="<?= esc($record['metodo_calculo']) ?>" readonly>
      </div>

      <!-- Unidad -->
      <div class="mb-3">
        <label class="form-label">Unidad</label>
        <input type="text" class="form-control" value="<?= esc($record['unidad']) ?>" readonly>
      </div>

      <!-- Periodo -->
      <div class="mb-3">
        <label class="form-label">Periodo (YYYY-MM)</label>
        <input type="text" name="periodo" class="form-control" value="<?= old('periodo', esc($record['periodo'])) ?>" required>
      </div>

      <!-- Valores JSON -->
      <div class="mb-3">
        <label class="form-label">Valores JSON</label>
        <textarea name="valores_json" class="form-control" rows="2" required><?= old('valores_json', esc($record['valores_json'])) ?></textarea>
      </div>

      <!-- Resultado Real -->
      <div class="mb-3">
        <label class="form-label">Resultado Real</label>
        <input type="text" name="resultado_real" class="form-control" value="<?= old('resultado_real', esc($record['resultado_real'])) ?>" required>
      </div>

      <!-- Comentario -->
      <div class="mb-3">
        <label class="form-label">Comentario</label>
        <textarea name="comentario" class="form-control" rows="2"><?= old('comentario', esc($record['comentario'])) ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Actualizar Registro</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>