<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\IndicadorPerfilModel;
use App\Models\HistorialIndicadorModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TrabajadorController extends BaseController
{
    protected $userModel;
    protected $ipModel;
    protected $histModel;

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

        // Obtener indicadores con campos del modelo IndicadorModel
        $items   = $this->ipModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        // Historial del periodo actual
        $history = $this->histModel
            ->where('id_usuario', $userId)
            ->where('periodo', $periodo)
            ->findAll();

        $histMap = [];
        foreach ($history as $h) {
            $histMap[$h['id_indicador_perfil']] = $h;
        }

        return view('trabajador/mis_indicadores', [
            'items'   => $items,
            'histMap' => $histMap,
            'periodo' => $periodo,
        ]);
    }

    /**
     * Guarda nuevos resultados de indicadores en historial
     */
    public function saveIndicadores()
    {
        $session = session();
        $userId  = $session->get('id_users');
        $periodo = date('Y-m');
        $post    = $this->request->getPost();

        foreach ($post['resultado_real'] as $ipId => $valor) {
            $this->histModel->insert([
                'id_indicador_perfil' => $ipId,
                'id_usuario'          => $userId,
                'periodo'             => $periodo,
                'valores_json'        => json_encode(['valor' => $valor]),
                'resultado_real'      => $valor,
                'comentario'          => $post['comentario'][$ipId] ?? null,
                'fecha_registro'      => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('/trabajador/historialResultados')
            ->with('success', 'Resultados guardados en el historial.');
    }

    /**
     * Muestra historial de resultados con datos extendidos del indicador
     */
    public function historialResultados()
    {
        $session = session();
        $userId  = $session->get('id_users');

        $historial = $this->histModel
            ->select([
                'historial_indicadores.*',
                'indicadores.id_indicador',
                'indicadores.nombre AS nombre_indicador',
                'indicadores.periodicidad',
                'indicadores.meta_valor',
                'indicadores.meta_descripcion',
                'indicadores.tipo_meta',
                'indicadores.metodo_calculo',
                'indicadores.unidad',
                'indicadores.objetivo_proceso',
                'indicadores.objetivo_calidad',
                'indicadores.tipo_aplicacion',
                'indicadores.created_at',
                'indicadores_perfil.meta',
                'indicadores_perfil.ponderacion',
            ])
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->where('historial_indicadores.id_usuario', $userId)
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        return view('trabajador/historial_resultados', [
            'historial' => $historial,
        ]);
    }
}
