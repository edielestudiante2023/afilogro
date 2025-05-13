<?php namespace App\Controllers;

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
     * Muestra el dashboard del trabajador
     */
    public function dashboard()
    {
        return view('trabajador/trabajadordashboard');
    }

    /**
     * Lista los indicadores asignados y sus valores actuales
     */
    public function misIndicadores()
    {
        $session = session();
        $userId  = $session->get('id_users');
        $user    = $this->userModel->find($userId);
        $perfil  = $user['id_perfil_cargo'];

        $items = $this->ipModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        $history = $this->histModel
            ->where('id_usuario', $userId)
            ->where('periodo', $periodo)
            ->findAll();

        $histMap = [];
        foreach ($history as $h) {
            $histMap[$h['id_indicador_perfil']] = $h;
        }

        $yaReportado = !empty($history);

        return view('trabajador/mis_indicadores', [
            'items'   => $items,
            'histMap' => $histMap,
            'periodo' => $periodo,
            'yaReportado' => $yaReportado
        ]);
    }

    /**
     * Guarda o actualiza los resultados de indicadores del trabajador
     */
    public function saveIndicadores()
    {
        $session = session();
        $userId  = $session->get('id_users');
        $periodo = date('Y-m');
        $post    = $this->request->getPost();

        // Verifica si ya se han registrado indicadores para evitar doble edición
        $existente = $this->histModel
            ->where('id_usuario', $userId)
            ->where('periodo', $periodo)
            ->first();

        if ($existente) {
            return redirect()->to('/trabajador/mis_indicadores')
                ->with('error', 'Ya has registrado los resultados de este periodo. No puedes editarlos nuevamente.');
        }

        foreach ($post['resultado_real'] as $ipId => $valor) {
            $data = [
                'id_indicador_perfil' => $ipId,
                'id_usuario'          => $userId,
                'periodo'             => $periodo,
                'valores_json'        => json_encode(['valor' => $valor]),
                'resultado_real'      => $valor,
                'comentario'          => $post['comentario'][$ipId] ?? null
            ];
            $this->histModel->insert($data);
        }

        return redirect()->to('/trabajador/historial_resultados')->with('success', 'Resultados guardados.');


    }

    public function trabajadordashboard()
    {
        return view('trabajador/trabajadordashboard');
    }

    public function historial()
{
    $session = session();
    $userId = $session->get('id_users');

    $registros = $this->histModel
        ->select('historial_indicadores.*, indicadores.nombre AS indicador')
        ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
        ->where('historial_indicadores.id_usuario', $userId)
        ->orderBy('fecha_registro', 'DESC')
        ->findAll();

    return view('trabajador/historial_resultados', ['registros' => $registros]);
}
public function historialResultados()
{
    $session = session();
    $userId  = $session->get('id_users');

    $historial = $this->histModel
        ->select('
            historial_indicadores.*,
            indicadores.nombre,
            indicadores.formula,
            indicadores.formula_larga,
            indicadores.objetivo_proceso,
            indicadores_perfil.meta,
            indicadores_perfil.ponderacion,
            indicadores.unidad
        ')
        ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
        ->where('historial_indicadores.id_usuario', $userId)
        ->orderBy('fecha_registro', 'DESC')
        ->findAll();

    return view('trabajador/historial_resultados', ['historial' => $historial]);
}




}
