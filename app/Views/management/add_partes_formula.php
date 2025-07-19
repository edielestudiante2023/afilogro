<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Parte de Fórmula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery (requerido por Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h3 class="mb-4">➕ Agregar Parte de Fórmula</h3>


        <div class="accordion mb-3" id="ayudaFormula">
            <div class="accordion-item border border-secondary">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed small bg-white text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAyuda" aria-expanded="false" aria-controls="collapseAyuda">
                        ¿Qué significa cada tipo de parte?
                    </button>
                </h2>
                <div id="collapseAyuda" class="accordion-collapse collapse" data-bs-parent="#ayudaFormula">
                    <div class="accordion-body small">
                        Al construir una fórmula, debes armarla paso a paso indicando cada componente en orden. Aquí te explicamos cada opción:
                        <ul class="mt-2 mb-2">
                            <li><strong>( apertura</strong>: Inicio de una agrupación. Ej: <code>(</code></li>
                            <li><strong>) cierre</strong>: Cierre de una agrupación. Ej: <code>)</code></li>
                            <li><strong>Operador</strong>: <code>+</code>, <code>-</code>, <code>*</code>, <code>/</code></li>
                            <li><strong>Dato</strong>: Variable que reporta el trabajador. Ej: <code>Ventas</code></li>
                            <li><strong>Constante</strong>: Valor fijo. Ej: <code>100</code></li>
                        </ul>
                        <strong>🧠 Ejemplo:</strong>
                        <code>( Ventas / Objetivo ) * 100</code>
                        <ol class="mt-2 mb-0">
                            <li>( apertura → <code>(</code>)</li>
                            <li>Dato → <code>Ventas</code></li>
                            <li>Operador → <code>/</code></li>
                            <li>Dato → <code>Objetivo</code></li>
                            <li>) cierre → <code>)</code></li>
                            <li>Operador → <code>*</code></li>
                            <li>Constante → <code>100</code></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>



        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <!-- Previsualización de la fórmula actual -->
        <?php if (!empty($formula_actual)): ?>
            <div class="alert alert-info">
                <strong>Fórmula actual:</strong>
                <div class="mt-2">
                    <?php foreach ($formula_actual as $parte): ?>
                        <span class="mx-1"><?= esc($parte['valor']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('partesformula/addpost') ?>" method="post">
            <?= csrf_field() ?>

            <?php if (!empty($id_indicador_seleccionado)): ?>
                <?php
                $nombreIndicador = '';
                foreach ($indicadores as $i) {
                    if ($i['id_indicador'] == $id_indicador_seleccionado) {
                        $nombreIndicador = $i['nombre'];
                        break;
                    }
                }
                ?>
                <div class="mb-3">
                    <label class="form-label">Indicador</label>
                    <input type="text" class="form-control" value="<?= esc($nombreIndicador) ?>" readonly>
                    <input type="hidden" name="id_indicador" value="<?= esc($id_indicador_seleccionado) ?>">
                </div>
            <?php else: ?>
                <!-- select normal -->
            <?php endif; ?>


            <div class="mb-3">
                <label for="tipo_parte" class="form-label">Tipo de Parte</label>
                <select class="form-select" id="tipo_parte" name="tipo_parte" required>
                    <option value="">-- Selecciona --</option>
                    <option value="paréntesis_apertura">( apertura</option>
                    <option value="paréntesis_cierre">) cierre</option>
                    <option value="operador">Operador</option>
                    <option value="dato">Dato</option>
                    <option value="constante">Constante</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="valor" class="form-label">Valor</label>
                <div id="valor-container">
                    <input type="text" class="form-control" id="valor" name="valor" required>
                </div>
            </div>


            <div class="mb-3">
                <label for="orden" class="form-label">Orden en la Fórmula</label>
                <input
                    type="number"
                    class="form-control"
                    id="orden"
                    name="orden"
                    value="<?= esc($siguiente_orden ?? '') ?>"
                    required>
            </div>


            <button type="submit" class="btn btn-success">Guardar Parte</button>
            <a href="<?= site_url('partesformula/list') ?>" class="btn btn-secondary ms-2">Ver Lista</a>


            
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#id_indicador').select2({
                placeholder: "Busca y selecciona un indicador",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tipo_parte').on('change', function() {
                const tipo = $(this).val();
                const contenedor = $('#valor-container');
                let html = '';

                switch (tipo) {
                    case 'paréntesis_apertura':
                        html = `<input type="text" class="form-control" name="valor" value="(" readonly>`;
                        break;

                    case 'paréntesis_cierre':
                        html = `<input type="text" class="form-control" name="valor" value=")" readonly>`;
                        break;

                    case 'operador':
                        html = `
                <select class="form-select" name="valor" required>
                    <option value="">-- Selecciona --</option>
                    <option value="+">+</option>
                    <option value="-">-</option>
                    <option value="*">*</option>
                    <option value="/">/</option>
                </select>`;
                        break;

                    case 'dato':
                        html = `<input type="text" class="form-control" name="valor" placeholder="Ej: Ventas" required>`;
                        break;

                    case 'constante':
                        html = `<input type="number" step="any" class="form-control" name="valor" placeholder="Ej: 100 o 12.5" required>`;
                        break;

                    default:
                        html = `<input type="text" class="form-control" name="valor" required>`;
                }

                contenedor.html(html);
            });
        });
    </script>
    <script>
    const BASE_URL = <?= json_encode(base_url()) ?>;
    const idIndicador = <?= json_encode($id_indicador_seleccionado) ?>;

    function actualizarOrdenAutomaticamente() {
        if (!idIndicador) return;

        fetch(`${BASE_URL}/partesformula/nextorden/${idIndicador}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('orden').value = data.next_orden;
            });
    }

    document.addEventListener('DOMContentLoaded', actualizarOrdenAutomaticamente);
</script>



</body>

</html>