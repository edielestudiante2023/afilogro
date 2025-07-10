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
        $session    = session();
        $jefeId     = $session->get('id_users');
        $perfil     = $session->get('id_perfil_cargo');

        // Opcional: permitir filtrar por rango de fechas, si lo deseas
        // $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
        // $fechaHasta = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');

        // 1) Obtengo los indicadores asignados a mi perfil
        $items   = $this->ipModel->getIndicadoresPorPerfil($perfil);
        $periodo = date('Y-m');

        return view('jefatura/misindicadorescomojefe', [
            'items'   => $items,
            'periodo' => $periodo,
            //'fecha_desde' => $fechaDesde,
            //'fecha_hasta' => $fechaHasta,
        ]);
    }

    /**
     * Procesa el POST de "Mis Indicadores como Jefatura"
     */
public function saveIndicadoresComoJefe()
    {
        $session      = session();
        $jefeId       = $session->get('id_users');
        $periodo      = date('Y-m');
        $resultados   = $this->request->getPost('resultado_real') ?? [];
        $comentarios  = $this->request->getPost('comentario')      ?? [];

        // Si vienen filas individuales (botón "single"), guardo solo esa
        if ($single = $this->request->getPost('single')) {
            $valor = trim($resultados[$single] ?? '');
            if ($valor !== '') {
                $this->histModel->insert([
                    'id_indicador_perfil' => $single,
                    'id_usuario'          => $jefeId,
                    'periodo'             => $periodo,
                    'valores_json'        => json_encode(['valor' => $valor]),
                    'resultado_real'      => $valor,
                    'comentario'          => trim($comentarios[$single] ?? ''),
                    'fecha_registro'      => date('Y-m-d H:i:s'),
                ]);
            }
            return redirect()->back()->with('success','Resultado guardado.');
        }

        // Si no, guardo todas las filas que tengan valor
        foreach ($resultados as $ipId => $valor) {
            $valor = trim($valor);
            if ($valor === '') {
                continue;
            }
            $this->histModel->insert([
                'id_indicador_perfil' => $ipId,
                'id_usuario'          => $jefeId,
                'periodo'             => $periodo,
                'valores_json'        => json_encode(['valor' => $valor]),
                'resultado_real'      => $valor,
                'comentario'          => trim($comentarios[$ipId] ?? ''),
                'fecha_registro'      => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->back()->with('success','Resultados guardados correctamente.');
    }





    /**
     * Edición rápida de indicadores del equipo
     */
 public function losIndicadoresDeMiEquipo()
    {
        // 1) Filtros de rango de fecha (YYYY-MM-DD)
        $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
        $fechaHasta = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');

        // 2) IDs de subordinados + el jefe
        $jefeId = session()->get('id_users');
        $subs   = $this->userModel->getSubordinadosDeJefe($jefeId);
        $subIds = array_unique(array_column($subs, 'id_users'));
        $subIds[] = $jefeId;

        // 3) Consulta de indicadores en el rango de fecha
        $equipo = $this->histModel
            ->select([
                'historial_indicadores.id_historial',
                'usuarios.nombre_completo AS nombre_completo',
                'i.nombre AS nombre_indicador',
                'i.meta_valor AS meta_valor',
                'i.tipo_meta AS tipo_meta',
                'i.metodo_calculo AS metodo_calculo',
                'i.unidad AS unidad',
                'historial_indicadores.resultado_real',
                'historial_indicadores.comentario',
            ])
            ->join('indicadores_perfil ip', 'ip.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores i',            'i.id_indicador = ip.id_indicador')
            ->join('users AS usuarios',        'usuarios.id_users = historial_indicadores.id_usuario')
            ->whereIn('historial_indicadores.id_usuario', $subIds)
            ->where('historial_indicadores.fecha_registro >=', $fechaDesde)
            ->where('historial_indicadores.fecha_registro <=', $fechaHasta)
            ->orderBy('usuarios.nombre_completo', 'ASC')
            ->orderBy('historial_indicadores.fecha_registro', 'DESC')
            ->findAll();

        // 4) Retornar vista con datos y filtros
        return view('jefatura/losindicadoresdemiequipo', [
            'equipo'        => $equipo,
            'fecha_desde'   => $fechaDesde,
            'fecha_hasta'   => $fechaHasta,
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

    // 1) Lectura de filtros o valores por defecto
    $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
    $fechaHasta = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');

    // 2) Consulta
    $historial = $this->histModel
        ->select([
            'historial_indicadores.*',
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
        ->join('indicadores_perfil', 'indicadores_perfil.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
        ->join('indicadores',         'indicadores.id_indicador = indicadores_perfil.id_indicador')
        ->where('historial_indicadores.id_usuario', $userId)
        ->where('historial_indicadores.fecha_registro >=', $fechaDesde . ' 00:00:00')
        ->where('historial_indicadores.fecha_registro <=', $fechaHasta . ' 23:59:59')
        ->orderBy('historial_indicadores.fecha_registro', 'DESC')
        ->findAll();

    return view('jefatura/historialmisindicadoresfeje', [
        'historial'   => $historial,
        'fecha_desde' => $fechaDesde,
        'fecha_hasta' => $fechaHasta,
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

    // Lee filtros o pone valores por defecto
    $fechaDesde = $this->request->getGet('fecha_desde') ?? date('Y-m-01');
    $fechaHasta = $this->request->getGet('fecha_hasta') ?? date('Y-m-d');

    // Busca IDs de subordinados
    $subsIds = array_column(
        $this->userModel->getSubordinadosDeJefe($jefeId),
        'id_users'
    );
    if (empty($subsIds)) {
        $equipo = [];
    } else {
        // Consulta con joins y filtro por rango de fecha
        $equipo = $this->histModel
            ->select([
                'historial_indicadores.*',
                'users.nombre_completo       AS nombre_completo',
                'indicadores.nombre          AS nombre_indicador',
                'indicadores.meta_valor',
                'indicadores.meta_descripcion',
                'indicadores.tipo_meta',
                'indicadores.metodo_calculo',
                'indicadores.unidad',
                'indicadores.objetivo_proceso',
                'indicadores.objetivo_calidad',
                'indicadores.tipo_aplicacion',
                'indicadores.created_at',
                'indicadores.periodicidad    AS periodicidad',
                'indicadores_perfil.meta      AS meta_texto',
                'indicadores_perfil.ponderacion',
                'historial_indicadores.resultado_real',
                'historial_indicadores.comentario',
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

    return view('jefatura/historiallosindicadoresdemiequipo', [
        'equipo'      => $equipo,
        'fecha_desde' => $fechaDesde,
        'fecha_hasta' => $fechaHasta,
    ]);
}


}





