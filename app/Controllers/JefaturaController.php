<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\HistorialIndicadorModel;
use App\Models\IndicadorAuditoriaModel;
use App\Models\IndicadorPerfilModel;
use App\Models\PartesFormulaModel;
use App\Models\IndicadorModel;                  // ← Añadir
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class JefaturaController extends BaseController
{
    protected $userModel;
    protected $histModel;
    protected $auditModel;
    protected $ipModel;
    protected $partesModel;
    protected $indicadorModel;                    // ← Añadir

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        helper(['url', 'session', 'form']);

        $this->userModel      = new UserModel();
        $this->histModel      = new HistorialIndicadorModel();
        $this->auditModel     = new IndicadorAuditoriaModel();
        $this->ipModel        = new IndicadorPerfilModel();
        $this->partesModel    = new PartesFormulaModel();
        $this->indicadorModel = new IndicadorModel();   // ← Instanciar
    }


    /**
     * Dashboard de jefatura
     */
    public function jefaturadashboard()
    {
        $subordinados = $this->userModel->getSubordinadosDeJefe(session()->get('id_users'));

        return view('jefatura/jefaturadashboard', [
            'subordinados' => $subordinados,
        ]);
    }

    /**
     * Mis indicadores como jefe (solo para ver)
     */
    /**
     * Mis indicadores como jefe (solo para ver), 
     * ahora con partes de fórmula para renderizarla
     */
    public function misIndicadoresComoJefe()
    {
        $session = session();
        if (! $session->has('id_users') || ! $session->has('id_perfil_cargo')) {
            return redirect()->to('/login')
                ->with('error', 'Tu sesión ha expirado.');
        }

        $jefeId = $session->get('id_users');
        $perfil = $session->get('id_perfil_cargo');

        // 1) Indicadores asignados a este perfil
        $items   = $this->ipModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        // 2) Precargar partes de fórmula para cada indicador
        $formulas = [];
        foreach ($items as $i) {
            $formulas[$i['id_indicador']] = $this->partesModel
                ->where('id_indicador', $i['id_indicador'])
                ->orderBy('orden', 'ASC')
                ->findAll();
        }

        // 3) Enviar todo a la vista
        return view('jefatura/misindicadorescomojefe', [
            'items'     => $items,
            'periodo'   => $periodo,
            'formulas'  => $formulas,
        ]);
    }


    /**
     * Procesa el POST de "Mis Indicadores como Jefatura"
     */
    /**
     * Procesa el POST de "Mis Indicadores como Jefatura"
     * Ahora guardando también los datos de fórmula operacionalizada
     */
    public function saveIndicadoresComoJefe()
    {
        $session           = session();
        $jefeId            = $session->get('id_users');
        $periodo           = date('Y-m');
        $resultados        = $this->request->getPost('resultado_real') ?? [];
        $comentarios       = $this->request->getPost('comentario')      ?? [];
        $formulasDigitadas = $this->request->getPost('formula_partes')  ?? [];

        // 1) Modo single (botón por fila)
        if ($single = $this->request->getPost('single')) {
            $valor = trim($resultados[$single] ?? '');
            if ($valor !== '') {
                // Preparamos el JSON
                $json = ['valor' => $valor];
                if (isset($formulasDigitadas[$single])) {
                    $json['formula_partes'] = $formulasDigitadas[$single];
                }

                $this->histModel->insert([
                    'id_indicador_perfil' => $single,
                    'id_usuario'          => $jefeId,
                    'periodo'             => $periodo,
                    'valores_json'        => json_encode($json),
                    'resultado_real'      => $valor,
                    'comentario'          => trim($comentarios[$single] ?? ''),
                    'fecha_registro'      => date('Y-m-d H:i:s'),
                ]);
            }
            return redirect()->back()->with('success', 'Resultado guardado.');
        }

        // 2) Modo batch (todas las filas)
        foreach ($resultados as $ipId => $valor) {
            $valor = trim($valor);
            if ($valor === '') {
                continue;
            }

            // Preparamos el JSON
            $json = ['valor' => $valor];
            if (isset($formulasDigitadas[$ipId])) {
                $json['formula_partes'] = $formulasDigitadas[$ipId];
            }

            $this->histModel->insert([
                'id_indicador_perfil' => $ipId,
                'id_usuario'          => $jefeId,
                'periodo'             => $periodo,
                'valores_json'        => json_encode($json),
                'resultado_real'      => $valor,
                'comentario'          => trim($comentarios[$ipId] ?? ''),
                'fecha_registro'      => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->back()->with('success', 'Resultados guardados correctamente.');
    }





    /**
     * Edición rápida de indicadores del equipo
     */
    public function losIndicadoresDeMiEquipo()
    {
        // 1) Filtros de rango de fecha
        $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
        $fechaHastaRaw = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');
        $fechaHasta = $fechaHastaRaw . ' 23:59:59';


        // 2) IDs de subordinados + el jefe
        $jefeId = session()->get('id_users');
        $subs   = $this->userModel->getSubordinadosDeJefe($jefeId);
        $subIds = array_column($subs, 'id_users');
        $subIds[] = $jefeId;

        // 3) Consulta de indicadores en el rango de fecha
        $equipo = $this->histModel
            ->select([
                'historial_indicadores.id_historial',
                'i.id_indicador       AS id_indicador',
                'usuarios.nombre_completo AS nombre_completo',
                'i.nombre             AS nombre_indicador',
                'i.meta_valor         AS meta_valor',
                'i.tipo_meta          AS tipo_meta',
                'i.metodo_calculo     AS metodo_calculo',
                'i.unidad             AS unidad',
                'historial_indicadores.resultado_real',
                'historial_indicadores.comentario',
            ])
            // no usamos from(), el modelo ya sabe de qué tabla viene
            ->join(
                'indicadores_perfil ip',
                'ip.id_indicador_perfil = historial_indicadores.id_indicador_perfil'
            )
            ->join(
                'indicadores i',
                'i.id_indicador = ip.id_indicador'
            )
            ->join(
                'users AS usuarios',
                'usuarios.id_users = historial_indicadores.id_usuario'
            )
            ->whereIn('historial_indicadores.id_usuario', $subIds)
            ->where('historial_indicadores.fecha_registro >=', $fechaDesde)
            ->where('historial_indicadores.fecha_registro <=', $fechaHasta)
            ->orderBy('usuarios.nombre_completo', 'ASC')
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        // 4) Precargar partes de fórmula
        $formulas = [];
        foreach ($equipo as $item) {
            $id = $item['id_indicador'];
            if (! isset($formulas[$id])) {
                $formulas[$id] = $this->partesModel
                    ->where('id_indicador', $id)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
            }
        }


        // 5) Renderizar vista
        return view('jefatura/losindicadoresdemiequipo', [
            'equipo'      => $equipo,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'formulas'    => $formulas,
        ]);
    }





    /**
     * Procesa la edición rápida y guarda los cambios + auditoría
     */
    public function guardarIndicadoresDeEquipo()
    {
        $jefeId  = session()->get('id_users');
        $desde   = $this->request->getPost('periodo_desde') ?? date('Y-m', strtotime('-2 months'));
        $hasta   = $this->request->getPost('periodo_hasta') ?? date('Y-m');
        $cambios = $this->request->getPost('cambios') ?? [];

        foreach ($cambios as $idHistorial => $datos) {
            $old = $this->histModel->find($idHistorial);
            $new = [
                'resultado_real' => $datos['resultado_real'],
                'comentario'     => $datos['comentario'],
            ];
            $this->histModel->update($idHistorial, $new);

            foreach (['resultado_real', 'comentario'] as $campo) {
                if ((string)$old[$campo] !== (string)$new[$campo]) {
                    $this->auditModel->insert([
                        'id_historial'   => $idHistorial,
                        'editor_id'      => $jefeId,
                        'campo'          => $campo,
                        'valor_anterior' => $old[$campo],
                        'valor_nuevo'    => $new[$campo],
                    ]);
                }
            }
        }

        return redirect()
            ->to('/jefatura/losindicadoresdemiequipo?periodo_desde=' . $desde . '&periodo_hasta=' . $hasta)
            ->with('success', 'Indicadores del equipo actualizados correctamente.');
    }

    /**
     * Historial de mis indicadores (todos los periodos)
     */
    public function historialMisIndicadoresFeje()
    {
        $session    = session();
        $userId     = $session->get('id_users');
        $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
        $fechaHasta = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');

        // 1) Traer el historial incluyendo id_indicador
        $historial = $this->histModel
            ->select([
                'historial_indicadores.*',
                'indicadores_perfil.id_indicador AS id_indicador',   // ← lo agregamos
                'indicadores.nombre            AS nombre_indicador',
                'indicadores.meta_valor',
                'indicadores.meta_descripcion',
                'indicadores.tipo_meta',
                'indicadores.metodo_calculo',
                'indicadores.unidad',
                'indicadores.objetivo_proceso',
                'indicadores.objetivo_calidad',
                'indicadores.tipo_aplicacion',
                'indicadores.created_at',
                'indicadores.periodicidad      AS periodicidad',
                'indicadores_perfil.meta',
                'indicadores_perfil.ponderacion',
            ])
            ->join(
                'indicadores_perfil',
                'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil'
            )
            ->join(
                'indicadores',
                'indicadores.id_indicador = indicadores_perfil.id_indicador'
            )
            ->where('historial_indicadores.id_usuario', $userId)
            ->where('historial_indicadores.fecha_registro >=', $fechaDesde . ' 00:00:00')
            ->where('historial_indicadores.fecha_registro <=', $fechaHasta . ' 23:59:59')
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        // 2) Precargar las partes de fórmula indexadas por id_indicador
        $formulasHist = [];
        foreach ($historial as $r) {
            $id = $r['id_indicador'];
            if (! isset($formulasHist[$id])) {
                $formulasHist[$id] = $this->partesModel
                    ->where('id_indicador', $id)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
            }
        }

        // 3) Pasar todo a la vista
        return view('jefatura/historialmisindicadoresfeje', [
            'historial'    => $historial,
            'fecha_desde'  => $fechaDesde,
            'fecha_hasta'  => $fechaHasta,
            'formulasHist' => $formulasHist,     // ← importante
        ]);
    }


    /**
     * Historial de indicadores de mi equipo para un periodo
     */
    /**
     * Historial de indicadores de mi equipo en un rango de fechas
     */
    /**
     * Historial de indicadores de mi equipo en un rango de fechas
     */
    public function historialLosIndicadoresDeMiEquipo()
    {
        $session    = session();
        $jefeId     = $session->get('id_users');

        // 1) Leer filtros o poner valores por defecto
        $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
        $fechaHasta = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');

        // 2) IDs de subordinados
        $subsIds = array_column(
            $this->userModel->getSubordinadosDeJefe($jefeId),
            'id_users'
        );
        if (empty($subsIds)) {
            $equipo = [];
        } else {
            // 3) Consulta con joins y filtro por rango de fecha
            $equipo = $this->histModel
                ->select([
                    'historial_indicadores.*',
                    'indicadores_perfil.id_indicador        AS id_indicador',
                    'users.nombre_completo                  AS nombre_completo',
                    'indicadores.nombre                     AS nombre_indicador',
                    'indicadores.meta_valor                 AS meta_valor',
                    'indicadores.meta_descripcion           AS meta_descripcion',
                    'indicadores.tipo_meta                  AS tipo_meta',
                    'indicadores.metodo_calculo             AS metodo_calculo',
                    'indicadores.unidad                     AS unidad',
                    'indicadores.objetivo_proceso           AS objetivo_proceso',
                    'indicadores.objetivo_calidad           AS objetivo_calidad',
                    'indicadores.tipo_aplicacion            AS tipo_aplicacion',
                    'indicadores.created_at                 AS creado_en',
                    'indicadores.periodicidad               AS periodicidad',
                    'indicadores_perfil.meta                AS meta_texto',
                    'indicadores_perfil.ponderacion         AS ponderacion',
                    'historial_indicadores.resultado_real',
                    'historial_indicadores.comentario',
                    'historial_indicadores.valores_json',
                    'historial_indicadores.fecha_registro',
                ])
                ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
                ->join('indicadores',         'indicadores.id_indicador = indicadores_perfil.id_indicador')
                ->join('users',               'users.id_users = historial_indicadores.id_usuario')
                ->whereIn('historial_indicadores.id_usuario', $subsIds)
                ->where('historial_indicadores.fecha_registro >=', $fechaDesde . ' 00:00:00')
                ->where('historial_indicadores.fecha_registro <=', $fechaHasta . ' 23:59:59')
                ->orderBy('historial_indicadores.fecha_registro', 'DESC')
                ->findAll();
        }

        // 4) Precargar partes de fórmula para cada indicador
        $formulasHist = [];
        foreach ($equipo as $r) {
            $id = $r['id_indicador'];
            if (! isset($formulasHist[$id])) {
                $formulasHist[$id] = $this->partesModel
                    ->where('id_indicador', $id)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
            }
        }

        // 5) Renderizar vista con fórmulas incluidas
        return view('jefatura/historiallosindicadoresdemiequipo', [
            'equipo'       => $equipo,
            'fecha_desde'  => $fechaDesde,
            'fecha_hasta'  => $fechaHasta,
            'formulasHist' => $formulasHist,
        ]);
    }


    // Muestra el formulario para diligenciar la fórmula
    public function fillFormula(int $idIndicador)
    {
        $session = session();
        if (! $session->has('id_users')) {
            return redirect()->to('/login')
                ->with('error', 'Tu sesión ha expirado.');
        }

        // 1) Traer los datos del indicador (tiene campo 'nombre' e 'id_indicador')
        $indicador = $this->indicadorModel->find($idIndicador);
        if (! $indicador) {
            throw new PageNotFoundException("Indicador #{$idIndicador} no existe");
        }

        // 2) Traer las partes de la fórmula
        $partes = $this->partesModel
            ->where('id_indicador', $idIndicador)
            ->orderBy('orden', 'ASC')
            ->findAll();


        // 3) Renderizar vista
        return view('jefatura/fill_formula_jefe', [
            'indicador' => $indicador,
            'partes'    => $partes,
        ]);
    }

    // Recibe el POST con los datos de cada parte, calcula y confirma
    public function fillFormulaPost(int $idIndicador)
    {
        $session = session();
        if (! $session->has('id_users') || ! $session->has('id_perfil_cargo')) {
            return redirect()->to('/login')
                ->with('error', 'Tu sesión ha expirado.');
        }
        $perfil = $session->get('id_perfil_cargo');

        // 1) Cargar las partes de la fórmula
        $partes = $this->partesModel
            ->where('id_indicador', $idIndicador)
            ->orderBy('orden', 'ASC')
            ->findAll();

        // 2) Leer los valores enviados
        $inputs = $this->request->getPost('dato') ?? [];

        // 3) Armar la expresión
        $expr = '';
        foreach ($partes as $p) {
            if ($p['tipo_parte'] === 'dato') {
                $valor = $inputs[$p['valor']] ?? 0;
                $expr .= " {$valor}";
            } else {
                $expr .= " {$p['valor']}";
            }
        }
        $expr = trim($expr);

        // 4) Calcular el resultado con manejo de división por cero
        try {
            $resultado = eval("return {$expr};");
        } catch (\DivisionByZeroError $e) {
            // Redirigir con mensaje de error
            return redirect()->back()
                ->with('error', 'Error en la fórmula: división por cero. Verifica los valores ingresados.');
        } catch (\ParseError $e) {
            // En caso de sintaxis inválida
            return redirect()->back()
                ->with('error', 'Error en la fórmula: sintaxis inválida.');
        }

        // 5) Traer datos del indicador (incluye el nombre)
        $indicador = $this->ipModel
            ->select([
                'indicadores_perfil.id_indicador_perfil',
                'indicadores.id_indicador',
                'indicadores.nombre AS nombre'
            ])
            ->join(
                'indicadores',
                'indicadores.id_indicador = indicadores_perfil.id_indicador'
            )
            ->where('indicadores_perfil.id_indicador', $idIndicador)
            ->where('indicadores_perfil.id_perfil_cargo', $perfil)
            ->first();

        // 6) Enviar todo a la vista
        return view('jefatura/confirmar_formula_jefe', [
            'indicador' => $indicador,
            'formula'   => $expr,
            'resultado' => $resultado,
            'partes'    => $inputs,
        ]);
    }


    // Guarda en historial el resultado calculado
    public function guardarFormula(int $idIndicador)
    {
        $session = session();
        $userId  = $session->get('id_users');
        $periodo = date('Y-m');
        $resultado    = $this->request->getPost('resultado');
        $partesValores = $this->request->getPost('formula_partes') ?? [];

        // Busca el id_indicador_perfil de este jefe
        $rel = $this->ipModel
            ->where('id_indicador', $idIndicador)
            ->where('id_perfil_cargo', $session->get('id_perfil_cargo'))
            ->first();

        $this->histModel->insert([
            'id_indicador_perfil' => $rel['id_indicador_perfil'],
            'id_usuario'          => $userId,
            'periodo'             => $periodo,
            'valores_json'        => json_encode(['valor' => $resultado, 'formula_partes' => $partesValores]),
            'resultado_real'      => $resultado,
            'comentario'          => null,
            'fecha_registro'      => date('Y-m-d H:i:s'),
        ]);



        return redirect()->to('/jefatura/historialmisindicadoresfeje')
            ->with('success', 'Resultado guardado correctamente.');
    }
}
