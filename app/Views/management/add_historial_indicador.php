<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nuevo Registro Historial – Afilogro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/nav') ?>

<div class="container py-4">
  <h1 class="h3 mb-4">Nuevo Registro Historial</h1>

  <?php if(session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <?php foreach(session()->getFlashdata('errors') as $e): ?><p><?= esc($e) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('historial_indicador/add') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Indicador x Perfil -->
    <div class="mb-3">
      <label class="form-label">Asignación Indicador x Perfil</label>
      <select name="id_indicador_perfil" class="form-select" required>
        <option value="">-- Seleccione --</option>
        <?php foreach($asignaciones as $a): ?>
          <option value="<?= esc($a['id_indicador_perfil']) ?>" <?= old('id_indicador_perfil') == $a['id_indicador_perfil'] ? 'selected' : '' ?>>
            <?= esc($a['nombre_indicador'] ?? $a['nombre'] ?? 'Indicador sin nombre') ?> —
            <?= esc($a['nombre_cargo'] ?? '(Sin cargo)') ?> —
            <?= esc($a['periodicidad'] ?? '-') ?> —
            Meta: <?= esc($a['meta_descripcion'] ?? $a['meta_valor'] ?? '-') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Usuario -->
    <div class="mb-3">
      <label class="form-label">Usuario</label>
      <select name="id_usuario" class="form-select" required>
        <option value="">-- Seleccione --</option>
        <?php foreach($users as $u): ?>
          <option value="<?= esc($u['id_users']) ?>" <?= old('id_usuario') == $u['id_users'] ? 'selected' : '' ?>>
            <?= esc($u['nombre_completo']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Periodo -->
    <div class="mb-3">
      <label class="form-label">Periodo (YYYY-MM)</label>
      <input type="text" name="periodo" class="form-control" value="<?= old('periodo') ?>" required>
    </div>

    <!-- Valores JSON -->
    <div class="mb-3">
      <label class="form-label">Valores JSON</label>
      <textarea name="valores_json" class="form-control" rows="2" required><?= old('valores_json') ?></textarea>
    </div>

    <!-- Resultado Real -->
    <div class="mb-3">
      <label class="form-label">Resultado Real</label>
      <input type="text" name="resultado_real" class="form-control" value="<?= old('resultado_real') ?>" required>
    </div>

    <!-- Comentario -->
    <div class="mb-3">
      <label class="form-label">Comentario</label>
      <textarea name="comentario" class="form-control" rows="2"><?= old('comentario') ?></textarea>
    </div>

    <!-- Datos complementarios del indicador (solo informativos) -->
    <div class="alert alert-secondary">
      <strong>Nota:</strong> Al guardar, se registrarán también los siguientes campos para fines de trazabilidad:
      <ul class="mb-0">
        <li><strong>Periodicidad</strong>, <strong>Ponderación</strong>, <strong>Meta Valor</strong>, <strong>Meta Descripción</strong></li>
        <li><strong>Tipo de Meta</strong>, <strong>Método de Cálculo</strong>, <strong>Unidad</strong></li>
        <li><strong>Objetivo del Proceso</strong> y <strong>Objetivo de Calidad</strong></li>
      </ul>
    </div>

    <button type="submit" class="btn btn-success">Guardar Registro</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
