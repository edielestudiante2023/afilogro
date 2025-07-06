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

        // Si no hay sesión válida, forzamos login
        if (! $session->has('id_users') || ! $session->has('id_perfil_cargo')) {
            $session->destroy();
            return redirect()->to('/login')
                             ->with('error', 'Tu sesión ha expirado. Por favor vuelve a ingresar.');
        }

        $userId = $session->get('id_users');
        $perfil = $session->get('id_perfil_cargo');

        // Traemos los indicadores según el perfil
        $items   = $this->ipModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        // Histórico de este periodo (para mostrar valores previos, si los hay)
        $history = $this->histModel
                        ->where('id_usuario', $userId)
                        ->where('periodo', $periodo)
                        ->findAll();

        // Creamos un map de historial por id_indicador_perfil
        $histMap = [];
        foreach ($history as $h) {
            $histMap[$h['id_indicador_perfil']] = $h;
        }

        return view('trabajador/mis_indicadores', [
            'items'   => $items,
            'histMap' => $histMap,
            'periodo' => $periodo
        ]);
    }

    /**
     * Guarda los resultados de indicadores del trabajador
     */
    public function saveIndicadores()
    {
        $session = session();
        $userId  = $session->get('id_users');
        $periodo = date('Y-m');
        $post    = $this->request->getPost();

        // Evitar doble registro en el mismo periodo
        if ($this->histModel->where('id_usuario', $userId)
                            ->where('periodo', $periodo)
                            ->first()) {
            return redirect()->to('/trabajador/misIndicadores')
                             ->with('error', 'Ya has registrado los resultados de este periodo.');
        }

        foreach ($post['resultado_real'] as $ipId => $valor) {
            $this->histModel->insert([
                'id_indicador_perfil' => $ipId,
                'id_usuario'          => $userId,
                'periodo'             => $periodo,
                'valores_json'        => json_encode(['valor' => $valor]),
                'resultado_real'      => $valor,
                'comentario'          => $post['comentario'][$ipId] ?? null
            ]);
        }

        return redirect()->to('/trabajador/historialResultados')
                         ->with('success', 'Resultados guardados.');
    }

    /**
     * Historial simple (puede usarse o eliminarse si no la necesitas)
     */
    public function historial()
    {
        $session = session();
        $userId  = $session->get('id_users');

        $registros = $this->histModel
            ->select('historial_indicadores.*, indicadores.nombre AS indicador')
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->where('historial_indicadores.id_usuario', $userId)
            ->orderBy('fecha_registro', 'DESC')
            ->findAll();

        return view('trabajador/historial_resultados', [
            'historial' => $registros
        ]);
    }

    /**
     * Historial detallado de resultados del trabajador
     */
    public function historialResultados()
    {
        $session = session();
        $userId  = $session->get('id_users');

        $historial = $this->histModel
            ->select('
                historial_indicadores.*,
                indicadores.nombre,
                indicadores.objetivo_proceso,
                indicadores.unidad,
                indicadores_perfil.meta,
                indicadores_perfil.ponderacion,
                indicadores.metodo_calculo
            ')
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->where('historial_indicadores.id_usuario', $userId)
            ->orderBy('fecha_registro', 'DESC')
            ->findAll();

        return view('trabajador/historial_resultados', [
            'historial' => $historial
        ]);
    }
}
