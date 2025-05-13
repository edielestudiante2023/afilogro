<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\IndicadorPerfilModel;
use App\Models\HistorialIndicadorModel;
use App\Models\IndicadorAuditoriaModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class JefaturaController extends BaseController
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

    public function jefaturadashboard()
    {
        $session     = session();
        $jefeId      = $session->get('id_users');
        $subordinados = $this->userModel->getSubordinadosDeJefe($jefeId);

        return view('jefatura/jefaturadashboard', [
            'subordinados' => $subordinados
        ]);
    }

    public function misIndicadoresComoJefe()
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

        return view('jefatura/misindicadorescomojefe', [
            'items'        => $items,
            'histMap'      => $histMap,
            'periodo'      => $periodo,
            'yaReportado'  => $yaReportado
        ]);
    }


    public function losIndicadoresDeMiEquipo()
    {
        $jefeId  = session()->get('id_users');
        $periodo = $this->request->getGet('periodo') ?? date('Y-m');


        $subsIds = array_column(
            $this->userModel->getSubordinadosDeJefe($jefeId),
            'id_users'
        );

        $equipo = $this->histModel
            ->select('
            historial_indicadores.*,
            indicadores.nombre          AS nombre_indicador,
            indicadores.formula_larga   AS formula_larga,
            indicadores_perfil.periodicidad,
            indicadores_perfil.meta,
            historial_indicadores.resultado_real,
            historial_indicadores.comentario,
            users.nombre_completo       AS nombre_completo
        ')
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores',        'indicadores.id_indicador          = indicadores_perfil.id_indicador')
            ->join('users',              'users.id_users                    = historial_indicadores.id_usuario')
            ->whereIn('historial_indicadores.id_usuario', $subsIds)
            ->where('historial_indicadores.periodo', $periodo)
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        return view('jefatura/losindicadoresdemiequipo', [
            'equipo'  => $equipo,
            'periodo' => $periodo,
        ]);
    }

    public function historialMisIndicadoresFeje()
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
    
        return view('jefatura/historialmisindicadoresfeje', ['historial' => $historial]);
    }
    

    public function historialLosIndicadoresDeMiEquipo()
    {
        $jefeId  = session()->get('id_users');
        $periodo = $this->request->getGet('periodo') ?? date('Y-m');


        $subIds = array_column(
            $this->userModel->getSubordinadosDeJefe($jefeId),
            'id_users'
        );

        $equipo = $this->histModel
            ->select('
            historial_indicadores.*,
            usuarios.nombre_completo   AS nombre_completo,
            indicadores.nombre         AS nombre_indicador,
            indicadores.formula_larga  AS formula_larga,
            indicadores_perfil.periodicidad,
            indicadores_perfil.meta,
            historial_indicadores.resultado_real,
            historial_indicadores.comentario,
            historial_indicadores.fecha_registro
        ')
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->join('users AS usuarios', 'usuarios.id_users = historial_indicadores.id_usuario')
            ->whereIn('historial_indicadores.id_usuario', $subIds)
            ->where('historial_indicadores.periodo', $periodo)
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        return view('jefatura/historiallosindicadoresdemiequipo', [
            'equipo'  => $equipo,
            'periodo' => $periodo,
        ]);
    }

    public function guardarEquipoIndicador($idHistorial)
    {
        helper('form');
        $post = $this->request->getPost();

        if (! $this->validate([
            'resultado_real' => 'required|decimal',
            'comentario'     => 'permit_empty',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $old = $this->histModel->find($idHistorial);

        $nuevosDatos = [
            'resultado_real' => $post['resultado_real'],
            'comentario'     => $post['comentario'],
        ];

        $this->histModel->update($idHistorial, $nuevosDatos);

        $auditModel = new IndicadorAuditoriaModel();
        $userId     = session()->get('id_users');
        $auditModel->insert([
            'id_historial'   => $idHistorial,
            'editor_id'      => $userId,
            'campo'          => 'resultado_real',
            'valor_anterior' => $old['resultado_real'],
            'valor_nuevo'    => $nuevosDatos['resultado_real'],
        ]);

        return redirect()->back()->with('success', 'Registro actualizado y auditado correctamente.');
    }

    public function saveIndicadoresComoJefe()
    {
        $session = session();
        $jefeId  = $session->get('id_users');
        $periodo = date('Y-m');
        $post    = $this->request->getPost();

        // Verificar si ya reportó como jefe en este periodo
       /*  $yaReportado = $this->histModel
            ->where('id_usuario', $jefeId)
            ->where('periodo', $periodo)
            ->first();

        if ($yaReportado) {
            return redirect()->to('/jefatura/misindicadorescomojefe')
                ->with('error', 'Ya has registrado tus resultados como jefe para este periodo.');
        } */

        foreach ($post['resultado_real'] as $ipId => $valor) {
            $data = [
                'id_indicador_perfil' => $ipId,
                'id_usuario'          => $jefeId,
                'periodo'             => $periodo,
                'valores_json'        => json_encode(['valor' => $valor]),
                'resultado_real'      => $valor,
                'comentario'          => $post['comentario'][$ipId] ?? null
            ];
            $this->histModel->insert($data);
        }

        return redirect()->to('/jefatura/historialmisindicadoresfeje')
            ->with('success', 'Resultados como jefe registrados correctamente.');
    }
}
