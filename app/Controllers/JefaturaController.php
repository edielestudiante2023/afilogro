<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\HistorialIndicadorModel;
use App\Models\IndicadorAuditoriaModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class JefaturaController extends BaseController
{
    protected $userModel;
    protected $histModel;
    protected $auditModel;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        helper(['url', 'session', 'form']);

        $this->userModel  = new UserModel();
        $this->histModel  = new HistorialIndicadorModel();
        $this->auditModel = new IndicadorAuditoriaModel();
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
    public function misIndicadoresComoJefe()
    {
        $userId  = session()->get('id_users');
        $perfil  = $this->userModel->find($userId)['id_perfil_cargo'];
        $items   = $this->histModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        // Cargar historial actual
        $history = $this->histModel
            ->where('id_usuario', $userId)
            ->where('periodo', $periodo)
            ->findAll();
        $histMap = [];
        foreach ($history as $h) {
            $histMap[$h['id_indicador_perfil']] = $h;
        }

        return view('jefatura/misindicadorescomojefe', [
            'items'   => $items,
            'histMap' => $histMap,
            'periodo' => $periodo,
        ]);
    }

    /**
     * Edición rápida de indicadores del equipo
     */
 public function losIndicadoresDeMiEquipo()
{
    $jefeId  = session()->get('id_users');
    $periodo = $this->request->getGet('periodo') ?? date('Y-m');

    $subsIds = array_column(
        $this->userModel->getSubordinadosDeJefe($jefeId),
        'id_users'
    );

    $equipo = $this->histModel
        ->select([
            'historial_indicadores.id_historial',
            'users.nombre_completo       AS nombre_completo',
            'indicadores.nombre          AS nombre_indicador',
            'indicadores.meta_valor      AS meta_valor',
            'indicadores.meta_descripcion AS meta_descripcion',
            'indicadores.tipo_meta       AS tipo_meta',
            'indicadores.metodo_calculo  AS metodo_calculo',
            'indicadores.unidad          AS unidad',
            'indicadores.objetivo_proceso AS objetivo_proceso',
            'indicadores.objetivo_calidad AS objetivo_calidad',
            'indicadores.tipo_aplicacion AS tipo_aplicacion',
            'indicadores.created_at      AS created_at',
            'indicadores.periodicidad    AS periodicidad',
            'indicadores.ponderacion     AS ponderacion',
            'historial_indicadores.resultado_real',
            'historial_indicadores.comentario',
            'historial_indicadores.fecha_registro',
        ])
        ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores',         'indicadores.id_indicador = indicadores_perfil.id_indicador')
        ->join('users',               'users.id_users = historial_indicadores.id_usuario')
        ->whereIn('historial_indicadores.id_usuario', $subsIds)
        ->where('historial_indicadores.periodo', $periodo)
        ->orderBy('users.nombre_completo', 'ASC')
        ->findAll();

    return view('jefatura/losindicadoresdemiequipo', [
        'equipo'  => $equipo,
        'periodo' => $periodo,
    ]);
}


    /**
     * Procesa la edición rápida y guarda los cambios + auditoría
     */
 public function guardarIndicadoresDeEquipo()
{
    $jefeId  = session()->get('id_users');
    $cambios = $this->request->getPost('cambios') ?? [];

    foreach ($cambios as $idHistorial => $datos) {
        $old = $this->histModel->find($idHistorial);
        $new = [
            'resultado_real' => $datos['resultado_real'],
            'comentario'     => $datos['comentario'],
        ];
        $this->histModel->update($idHistorial, $new);

        foreach (['resultado_real','comentario'] as $campo) {
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
        ->to('/jefatura/losindicadoresdemiequipo?periodo=' . ($this->request->getGet('periodo') ?? date('Y-m')))
        ->with('success', 'Indicadores del equipo actualizados correctamente.');
}

    /**
     * Historial de mis indicadores (todos los periodos)
     */
    public function historialMisIndicadoresFeje()
    {
        $userId = session()->get('id_users');

        $historial = $this->histModel
            ->select([
                'historial_indicadores.*',
                'indicadores.nombre           AS nombre_indicador',
                'indicadores.periodicidad     AS periodicidad',
                'indicadores.meta_valor       AS meta_valor',
                'indicadores.meta_descripcion AS meta_descripcion',
                'indicadores.ponderacion      AS ponderacion',
                'indicadores.unidad           AS unidad',
                'indicadores.objetivo_proceso AS objetivo_proceso',
                'indicadores.objetivo_calidad AS objetivo_calidad',
            ])
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores',         'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->where('historial_indicadores.id_usuario', $userId)
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        return view('jefatura/historialmisindicadoresfeje', [
            'historial' => $historial,
        ]);
    }

    /**
     * Historial de indicadores de mi equipo para un periodo
     */
    public function historialLosIndicadoresDeMiEquipo()
{
    $jefeId  = session()->get('id_users');
    $periodo = $this->request->getGet('periodo') ?? date('Y-m');

    $subIds = array_column(
        $this->userModel->getSubordinadosDeJefe($jefeId),
        'id_users'
    );

    $equipo = $this->histModel
        ->select([
            'historial_indicadores.*',
            'usuarios.nombre_completo      AS nombre_completo',
            'indicadores.nombre            AS nombre_indicador',
            'indicadores.meta_valor        AS meta_valor',
            'indicadores.meta_descripcion  AS meta_descripcion',
            'indicadores.tipo_meta         AS tipo_meta',
            'indicadores.metodo_calculo    AS metodo_calculo',
            'indicadores.unidad            AS unidad',
            'indicadores.objetivo_proceso  AS objetivo_proceso',
            'indicadores.objetivo_calidad  AS objetivo_calidad',
            'indicadores.tipo_aplicacion   AS tipo_aplicacion',
            'indicadores.created_at        AS created_at',
            'indicadores.periodicidad      AS periodicidad',
            'indicadores.ponderacion       AS ponderacion',
        ])
        ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores',        'indicadores.id_indicador = indicadores_perfil.id_indicador')
        ->join('users AS usuarios',  'usuarios.id_users = historial_indicadores.id_usuario')
        ->whereIn('historial_indicadores.id_usuario', $subIds)
        ->where('historial_indicadores.periodo', $periodo)
        ->orderBy('historial_indicadores.fecha_registro', 'DESC')
        ->findAll();

    return view('jefatura/historiallosindicadoresdemiequipo', [
        'equipo'  => $equipo,
        'periodo' => $periodo,
    ]);
}

}
