<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\HistorialIndicadorModel;
use App\Models\IndicadorAuditoriaModel;
use App\Models\IndicadorPerfilModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class JefaturaController extends BaseController
{
    protected $userModel;
    protected $histModel;
    protected $auditModel;
    protected $ipModel;

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
        $this->ipModel    = new IndicadorPerfilModel();
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
        $userId     = session()->get('id_users');
        $perfil     = $this->userModel->find($userId)['id_perfil_cargo'];
        // Obtiene los indicadores definidos para el perfil
        $items      = $this->ipModel->getIndicadoresPorPerfil($perfil);

        // Rango de fechas con mini-datepicker
        $fechaDesde = $this->request->getGet('fecha_desde')
            ?? date('Y-m-01');        // primer día del mes actual
        $fechaHasta = $this->request->getGet('fecha_hasta')
            ?? date('Y-m-d');        // hoy

        // Recupera el historial en ese rango
        $history = $this->histModel
            ->where('id_usuario', $userId)
            ->where('fecha_registro >=', $fechaDesde)
            ->where('fecha_registro <=', $fechaHasta)
            ->findAll();

        // Mapea cada indicador de perfil a su último registro en el rango
        $histMap = [];
        foreach ($history as $h) {
            $histMap[$h['id_indicador_perfil']] = $h;
        }

        return view('jefatura/misindicadorescomojefe', [
            'items'        => $items,
            'histMap'      => $histMap,
            'fecha_desde'  => $fechaDesde,
            'fecha_hasta'  => $fechaHasta,
        ]);
    }

    /**
     * Procesa el POST de "Mis Indicadores como Jefatura"
     */
public function saveIndicadoresComoJefe()
{
    $session    = session();
    $userId     = $session->get('id_users');
    $periodo    = date('Y-m');                  // o lo que uses
    $post       = $this->request->getPost();

    // Recorre SOLO los que tienen valor
    foreach ($post['resultado_real'] as $ipId => $valor) {
        $valor = trim($valor);
        if ($valor === '') {
            continue;
        }
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

    return redirect()->to('/jefatura/misindicadorescomojefe')
                     ->with('success','Resultados guardados correctamente.');
}





    /**
     * Edición rápida de indicadores del equipo
     */
public function losIndicadoresDeMiEquipo()
{
    // 1) Filtro por mes (YYYY-MM), o mes actual por defecto
    $periodo = $this->request->getGet('periodo') ?? date('Y-m');

    // 2) IDs de subordinados + el jefe
    $jefeId = session()->get('id_users');
    $subs   = $this->userModel->getSubordinadosDeJefe($jefeId);
    $subIds = array_unique(array_column($subs, 'id_users'));
    $subIds[] = $jefeId;

    // 3) Traigo solo las filas de historial que coinciden con ese periodo y esos usuarios
    $equipo = $this->histModel
        ->select([
            'historial_indicadores.id_historial',
            'usuarios.nombre_completo      AS nombre_completo',
            'i.nombre                       AS nombre_indicador',
            'i.meta_valor                   AS meta_valor',
            'i.meta_descripcion             AS meta_descripcion',
            'i.tipo_meta                    AS tipo_meta',
            'i.metodo_calculo               AS metodo_calculo',
            'i.unidad                       AS unidad',
            'i.objetivo_proceso             AS objetivo_proceso',
            'i.objetivo_calidad             AS objetivo_calidad',
            'i.tipo_aplicacion              AS tipo_aplicacion',
            'i.created_at                   AS created_at',
            'historial_indicadores.fecha_registro',
            'i.periodicidad                 AS periodicidad',
            'i.ponderacion                  AS ponderacion',
            'historial_indicadores.resultado_real',
            'historial_indicadores.comentario',
        ])
        ->join('indicadores_perfil ip', 'ip.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores i',           'i.id_indicador         = ip.id_indicador')
        ->join('users AS usuarios',       'usuarios.id_users     = historial_indicadores.id_usuario')
        ->whereIn('historial_indicadores.id_usuario', $subIds)
        ->where('historial_indicadores.periodo', $periodo)
        ->orderBy('usuarios.nombre_completo', 'ASC')
        ->orderBy('historial_indicadores.fecha_registro', 'DESC')
        ->findAll();

    // 4) Retorno la vista con $equipo y $periodo
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
    $userId = session()->get('id_users');

    $historial = $this->histModel
        ->select([
            'historial_indicadores.*',
            'indicadores.nombre            AS nombre_indicador',
            'indicadores.meta_valor        AS meta_valor',
            'indicadores.meta_descripcion  AS meta_descripcion',
            'indicadores.tipo_meta         AS tipo_meta',         // ← añadido
            'indicadores.metodo_calculo    AS metodo_calculo',    // ← añadido
            'indicadores.unidad            AS unidad',
            'indicadores.objetivo_proceso  AS objetivo_proceso',
            'indicadores.objetivo_calidad  AS objetivo_calidad',
            'indicadores.tipo_aplicacion   AS tipo_aplicacion',   // ← añadido
            'indicadores.created_at        AS created_at',        // ← añadido
            'indicadores.periodicidad      AS periodicidad',
            'indicadores.ponderacion       AS ponderacion',
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
    /**
 * Historial de indicadores de mi equipo en un rango de fechas
 */
/**
 * Historial de indicadores de mi equipo en un rango de fechas
 */
public function historialLosIndicadoresDeMiEquipo()
{
    // 1) Recoge el 'periodo' (YYYY-MM) o pone el mes actual
    $periodo = $this->request->getGet('periodo') ?? date('Y-m');

    // 2) IDs del jefe y de sus subordinados
    $jefeId = session()->get('id_users');
    $subs   = $this->userModel->getSubordinadosDeJefe($jefeId);
    $subIds = array_unique(array_column($subs, 'id_users'));
    $subIds[] = $jefeId;

    // 3) Consulta sobre historial_indicadores + joins a perfil, indicador y usuario
    $equipo = $this->histModel
        ->select([
            'historial_indicadores.id_historial',
            'usuarios.nombre_completo      AS nombre_completo',
            'i.nombre                       AS nombre_indicador',
            'i.meta_valor                   AS meta_valor',
            'i.meta_descripcion             AS meta_descripcion',
            'i.tipo_meta                    AS tipo_meta',
            'i.metodo_calculo               AS metodo_calculo',
            'i.unidad                       AS unidad',
            'i.objetivo_proceso             AS objetivo_proceso',
            'i.objetivo_calidad             AS objetivo_calidad',
            'i.tipo_aplicacion              AS tipo_aplicacion',
            'i.created_at                   AS created_at',
            'historial_indicadores.fecha_registro',
            'i.periodicidad                 AS periodicidad',
            'i.ponderacion                  AS ponderacion',
            'historial_indicadores.resultado_real',
            'historial_indicadores.comentario',
        ])
        ->join('indicadores_perfil ip', 'ip.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores i',           'i.id_indicador         = ip.id_indicador')
        ->join('users AS usuarios',       'usuarios.id_users     = historial_indicadores.id_usuario')
        ->whereIn('historial_indicadores.id_usuario', $subIds)
        ->where('historial_indicadores.periodo', $periodo)
        ->orderBy('historial_indicadores.fecha_registro', 'DESC')
        ->findAll();

    // 4) Pasa al view, incluyendo el periodo para el month-picker
    return view('jefatura/historiallosindicadoresdemiequipo', [
        'equipo'  => $equipo,
        'periodo' => $periodo,
    ]);
}

}





