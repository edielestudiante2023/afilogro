<?php

namespace App\Controllers;

use App\Models\HistorialIndicadorModel;
use App\Models\IndicadorPerfilModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class HistorialIndicadorController extends BaseController
{
    /** @var HistorialIndicadorModel */
    protected $histModel;
    /** @var IndicadorPerfilModel */
    protected $ipModel;
    /** @var UserModel */
    protected $userModel;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        helper(['url', 'form']);
        $this->histModel = new HistorialIndicadorModel();
        $this->ipModel   = new IndicadorPerfilModel();
        $this->userModel = new UserModel();
    }

    // Listar historial
    public function listHistorialIndicador()
    {
        $data['records'] = $this->histModel
            ->select('historial_indicadores.*, i.nombre AS indicador, p.nombre_cargo AS perfil, u.nombre_completo AS usuario')
            ->join('indicadores_perfil ip', 'ip.id_indicador_perfil = historial_indicadores.id_indicador_perfil')
            ->join('indicadores i', 'i.id_indicador = ip.id_indicador')
            ->join('perfiles_cargo p', 'p.id_perfil_cargo = ip.id_perfil_cargo')
            ->join('users u', 'u.id_users = historial_indicadores.id_usuario')
            ->orderBy('fecha_registro', 'DESC')
            ->findAll();
        return view('management/list_historial_indicador', $data);
    }

    // Formulario crear registro
    public function addHistorialIndicador()
    {
        $data = [
            'asignaciones' => $this->ipModel->findAll(),
            'users'        => $this->userModel->where('activo', 1)->findAll()
        ];
        return view('management/add_historial_indicador', $data);
    }

    // Procesar creación
    public function addHistorialIndicadorPost()
    {
        $rules = [
            'id_indicador_perfil' => 'required|integer',
            'id_usuario'          => 'required|integer',
            'periodo'             => 'required',
            'valores_json'        => 'required',
            'resultado_real'      => 'required|decimal',
            'comentario'          => 'permit_empty'
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
        $this->histModel->insert($this->request->getPost());
        return redirect()->to('/historial_indicador')->with('success', 'Registro creado.');
    }

    // Formulario editar
    public function editHistorialIndicador($id)
    {
        $record = $this->histModel->find($id);
        if (! $record) throw new PageNotFoundException("Registro con ID $id no existe");
        $data = [
            'record'      => $record,
            'asignaciones' => $this->ipModel
                ->select('indicadores_perfil.id_indicador_perfil, indicadores.nombre AS nombre_indicador, perfiles_cargo.nombre_cargo')
                ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
                ->join('perfiles_cargo', 'perfiles_cargo.id_perfil_cargo = indicadores_perfil.id_perfil_cargo')
                ->findAll(),

            'users'       => $this->userModel->where('activo', 1)->findAll()
        ];
        return view('management/edit_historial_indicador', $data);
    }

    // Procesar edición
    public function editHistorialIndicadorPost($id)
    {
        $rules = [
            'id_indicador_perfil' => 'required|integer',
            'id_usuario'          => 'required|integer',
            'periodo'             => 'required',
            'valores_json'        => 'required',
            'resultado_real'      => 'required|decimal',
            'comentario'          => 'permit_empty'
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
        $this->histModel->update($id, $this->request->getPost());
        return redirect()->to('/historial_indicador')->with('success', 'Registro actualizado.');
    }

    // Eliminar registro
    public function deleteHistorialIndicador($id)
    {
        $this->histModel->delete($id);
        return redirect()->to('/historial_indicador')->with('success', 'Registro eliminado.');
    }
}
