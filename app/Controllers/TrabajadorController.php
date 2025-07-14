<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\IndicadorPerfilModel;
use App\Models\HistorialIndicadorModel;
use App\Models\PartesFormulaModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TrabajadorController extends BaseController
{
    protected $userModel;
    protected $ipModel;
    protected $histModel;
    protected $partesModel;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        helper(['url', 'session', 'form']);
        $this->userModel = new UserModel();
        $this->ipModel   = new IndicadorPerfilModel();
        $this->histModel = new HistorialIndicadorModel();
        $this->partesModel = new PartesFormulaModel();
    }

    /**
     * Dashboard inicial para trabajador
     */
    public function trabajadordashboard()
    {
        return view('trabajador/trabajadordashboard');
    }

    /**
     * Lista los indicadores asignados con campos extendidos del modelo Indicador
     */
    public function misIndicadores()
    {
        $session = session();
        if (! $session->has('id_users') || ! $session->has('id_perfil_cargo')) {
            $session->destroy();
            return redirect()->to('/login')
                ->with('error', 'Tu sesión ha expirado. Por favor vuelve a ingresar.');
        }

        $userId = $session->get('id_users');
        $perfil = $session->get('id_perfil_cargo');

        // 1) Obtener indicadores para el perfil
        $items   = $this->ipModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        // 2) Historial del periodo actual
        $history = $this->histModel
            ->where('id_usuario', $userId)
            ->where('periodo', $periodo)
            ->findAll();
        $histMap = [];
        foreach ($history as $h) {
            $histMap[$h['id_indicador_perfil']] = $h;
        }

        // 3) Cargar las partes de fórmula para cada indicador
        $formulas = [];
        foreach ($items as $item) {
            $formulas[$item['id_indicador']] = $this->partesModel
                ->where('id_indicador', $item['id_indicador'])
                ->orderBy('orden', 'ASC')
                ->findAll();
        }

        // 4) Enviar todo a la vista
        return view('trabajador/mis_indicadores', [
            'items'     => $items,
            'histMap'   => $histMap,
            'periodo'   => $periodo,
            'userId'    => $userId,
            'formulas'  => $formulas,
        ]);
    }


    /**
     * Guarda nuevos resultados de indicadores en historial
     */
    public function saveIndicadores()
    {
        $session     = session();
        $userId      = $session->get('id_users');
        $periodo     = date('Y-m');
        $resultados  = $this->request->getPost('resultado_real') ?? [];
        $comentarios = $this->request->getPost('comentario')     ?? [];

        // Verifica si llegaron partes de fórmula adicionales
        $formulasDigitadas = $this->request->getPost('formula_partes') ?? [];

        foreach ($resultados as $ipId => $valor) {
            $valor = trim($valor);
            if ($valor === '') {
                continue;   // no guardar si está vacío
            }

            // Si este indicador tiene fórmula digitada, la incluimos en valores_json
            $json = ['valor' => $valor];
            if (isset($formulasDigitadas[$ipId])) {
                $json['formula_partes'] = $formulasDigitadas[$ipId];
            }

            $this->histModel->insert([
                'id_indicador_perfil' => $ipId,
                'id_usuario'          => $userId,
                'periodo'             => $periodo,
                'valores_json'        => json_encode($json),
                'resultado_real'      => $valor,
                'comentario'          => trim($comentarios[$ipId] ?? ''),
                'fecha_registro'      => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('/trabajador/historialResultados')
            ->with('success', 'Resultado(s) guardado(s) correctamente.');
    }



    /**
     * Muestra historial de resultados con datos extendidos del indicador
     */
 public function historialResultados()
{
    $session    = session();
    $userId     = $session->get('id_users');
    $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
    $fechaHasta = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');

    // 1) Traer el historial
    
    $historial = $this->histModel
        ->select([
            'historial_indicadores.*',
            'indicadores_perfil.id_indicador AS id_indicador',
            'indicadores.nombre           AS nombre_indicador',
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
        ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores',         'indicadores.id_indicador = indicadores_perfil.id_indicador')
        ->where('historial_indicadores.id_usuario', $userId)
        ->where('historial_indicadores.fecha_registro >=', $fechaDesde . ' 00:00:00')
        ->where('historial_indicadores.fecha_registro <=', $fechaHasta . ' 23:59:59')
        ->orderBy('historial_indicadores.fecha_registro', 'DESC')
        ->findAll();

    // 2) Precargar partes de fórmula para cada indicador del historial
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




    // 3) Enviar todo a la vista
    return view('trabajador/historial_resultados', [
        'historial'    => $historial,
        'fecha_desde'  => $fechaDesde,
        'fecha_hasta'  => $fechaHasta,
        'formulasHist' => $formulasHist,
    ]);
}



    /**
     * Guarda en historial el resultado calculado por fórmula
     */
    public function guardarFormula($idIndicador)
    {
        $session = session();
        if (! $session->has('id_users') || ! $session->has('id_perfil_cargo')) {
            return redirect()->to('/login')
                ->with('error', 'Tu sesión ha expirado.');
        }

        $userId    = $session->get('id_users');
        $perfil    = $session->get('id_perfil_cargo');
        $periodo   = date('Y-m');
        $resultado = $this->request->getPost('resultado');
        $partes    = $this->request->getPost('formula_partes') ?? [];

        // Encuentra la relación perfil–indicador
        $rel = $this->ipModel
            ->where('id_indicador', $idIndicador)
            ->where('id_perfil_cargo', $perfil)
            ->first();

        if (! $rel) {
            return redirect()->to('/trabajador/misIndicadores')
                ->with('error', 'Indicador no asignado a tu perfil.');
        }

        // Prepara el JSON
        $json = ['valor' => $resultado, 'formula_partes' => $partes];

        // Inserta en historial
        $this->histModel->insert([
            'id_indicador_perfil' => $rel['id_indicador_perfil'],
            'id_usuario'          => $userId,
            'periodo'             => $periodo,
            'valores_json'        => json_encode($json),
            'resultado_real'      => $resultado,
            'comentario'          => null,
            'fecha_registro'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/trabajador/misIndicadores')
            ->with('success', 'Resultado guardado correctamente.');
    }
}
