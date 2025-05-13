<?php

namespace App\Controllers;

use App\Models\IndicadorAuditoriaModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class AuditoriaController extends Controller
{
    protected $auditoriaModel;
    protected $userModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->auditoriaModel = new IndicadorAuditoriaModel();
        $this->userModel      = new UserModel();
    }

    /**
     * Muestra el listado de auditorías de edición de indicadores
     */
    public function listAuditoria()
    {
        $auditorias = $this->auditoriaModel
            ->select('indicador_auditoria.*, users.nombre_completo AS editor_nombre')
            ->join('users', 'users.id_users = indicador_auditoria.editor_id')
            ->orderBy('fecha_edicion', 'DESC')
            ->findAll();

        return view('management/list_auditoria', ['auditorias' => $auditorias]);
    }
}
