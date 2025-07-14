<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirmar Fórmula – Jefatura</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet">
</head>
<body class="p-4">
  <?= $this->include('partials/nav') ?>

  <div class="container">
    <h3 class="mb-4"><?= esc($indicador['nombre']) ?></h3>

    <p><strong>Fórmula construida:</strong>
      <code><?= esc($formula) ?></code>
    </p>
    <p><strong>Resultado:</strong>
      <span class="h4"><?= esc($resultado) ?></span>
    </p>

    <form
      action="<?= base_url('jefatura/formula/guardar/' . $indicador['id_indicador']) ?>"
      method="post"
    >
      <?= csrf_field() ?>

      <!-- Resultado -->
      <input
        type="hidden"
        name="resultado"
        value="<?= esc($resultado) ?>"
      >

      <!-- Partes de fórmula -->
      <?php foreach ($partes as $clave => $valor): ?>
        <input
          type="hidden"
          name="formula_partes[<?= esc($clave) ?>]"
          value="<?= esc($valor) ?>"
        >
      <?php endforeach; ?>

      <button type="submit" class="btn btn-success">Confirmar</button>
      <a
        href="<?= base_url('jefatura/misIndicadoresComoJefe') ?>"
        class="btn btn-secondary ms-2"
      >
        Cancelar
      </a>
    </form>
  </div>
</body>
</html>
