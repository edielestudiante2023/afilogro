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
        $session      = session();
        $jefeId       = $session->get('id_users');
        $subordinados = $this->userModel->getSubordinadosDeJefe($jefeId);

        return view('jefatura/jefaturadashboard', [
            'subordinados' => $subordinados,
        ]);
    }

    public function misIndicadoresComoJefe()
    {
        $session = session();
        $userId  = $session->get('id_users');
        $user    = $this->userModel->find($userId);
        $perfil  = $user['id_perfil_cargo'];

        // Obtener indicadores asociados al perfil de cargo del jefe
        $items   = $this->ipModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        // Obtener historial del periodo actual para pre-cargar valores ya registrados
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
                'historial_indicadores.*',
                'indicadores.nombre           AS nombre_indicador',
                'indicadores.periodicidad     AS periodicidad',
                'indicadores.meta_valor       AS meta_valor',
                'indicadores.meta_descripcion AS meta_descripcion',
                'indicadores.ponderacion      AS ponderacion',
                'indicadores.unidad           AS unidad',
                'indicadores.objetivo_proceso AS objetivo_proceso',
                'indicadores.objetivo_calidad AS objetivo_calidad',
                'indicadores.tipo_meta        AS tipo_meta',
                'indicadores.metodo_calculo   AS metodo_calculo',
                'indicadores.tipo_aplicacion  AS tipo_aplicacion',
                'indicadores.created_at       AS created_at',
                'historial_indicadores.resultado_real',
                'historial_indicadores.comentario',
                'users.nombre_completo        AS nombre_completo',
            ])
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->join('users', 'users.id_users = historial_indicadores.id_usuario')
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
                'indicadores.tipo_meta        AS tipo_meta',
                'indicadores.metodo_calculo   AS metodo_calculo',
                'indicadores.tipo_aplicacion  AS tipo_aplicacion',
                'indicadores.created_at       AS created_at',
            ])
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->where('historial_indicadores.id_usuario', $userId)
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        return view('jefatura/historialmisindicadoresfeje', [
            'historial' => $historial,
        ]);
    }

    public function historialLosIndicadoresDeMiEquipo()
    {
        $jefeId  = session()->get('id_users');
        $periodo = $this->request->getGet('periodo') ?? date('Y-m');

        $subIds = array_column(
            $this->userModel->getSubordinadosDeJefe($jefeId),
            'id_users'
        );

        $builder = $this->histModel->builder();
        $equipo  = $builder
            ->select([
                'historial_indicadores.*',
                'usuarios.nombre_completo      AS nombre_completo',
                'indicadores.nombre            AS nombre_indicador',
                'indicadores.periodicidad      AS periodicidad',
                'indicadores.meta_valor        AS meta_valor',
                'indicadores.meta_descripcion  AS meta_descripcion',
                'indicadores.ponderacion       AS ponderacion',
                'indicadores.unidad            AS unidad',
                'indicadores.objetivo_proceso  AS objetivo_proceso',
                'indicadores.objetivo_calidad  AS objetivo_calidad',
                'indicadores.tipo_meta         AS tipo_meta',
                'indicadores.metodo_calculo    AS metodo_calculo',
                'indicadores.tipo_aplicacion   AS tipo_aplicacion',
                'indicadores.created_at        AS created_at',
                'historial_indicadores.resultado_real',
                'historial_indicadores.comentario',
                'historial_indicadores.fecha_registro',
            ])
            ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores',        'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->join('users AS usuarios',  'usuarios.id_users = historial_indicadores.id_usuario')
            ->whereIn('historial_indicadores.id_usuario', $subIds)
            ->where('historial_indicadores.periodo', $periodo)
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->get()
            ->getResultArray();

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
    $post    = $this->request->getPost();
    $periodo = date('Y-m');

    foreach ($post['resultado_real'] as $clave => $valor) {
        // Separar la clave compuesta: id_indicador_perfil_id_usuario
        [$idIndicadorPerfil, $idUsuario] = explode('_', $clave);

        $registroExistente = $this->histModel
            ->where('id_indicador_perfil', $idIndicadorPerfil)
            ->where('id_usuario', $idUsuario)
            ->where('periodo', $periodo)
            ->first();

        $data = [
            'id_indicador_perfil' => $idIndicadorPerfil,
            'id_usuario'          => $idUsuario,
            'periodo'             => $periodo,
            'valores_json'        => json_encode(['valor' => $valor]),
            'resultado_real'      => $valor,
            'comentario'          => $post['comentario'][$clave] ?? null,
            'fecha_registro'      => date('Y-m-d H:i:s'),
        ];

        if ($registroExistente) {
            $this->histModel->update($registroExistente['id_historial'], $data);
        } else {
            $this->histModel->insertarSinDuplicar($data);
        }
    }

    return redirect()->to('/jefatura/losindicadoresdemiequipo')
        ->with('success', 'Los indicadores del equipo fueron actualizados correctamente.');
}

}
