<?php

namespace App\Controllers;

use App\Models\IndicadorPerfilModel;
use App\Models\IndicadorModel;
use CodeIgniter\Controller;

class IndicadorPerfilController extends Controller
{
    protected $indicadorPerfilModel;
    protected $indicadorModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->indicadorPerfilModel = new IndicadorPerfilModel();
        $this->indicadorModel = new IndicadorModel();
    }

    public function listIndicadorPerfil()
    {
        $data['indicadores_perfil'] = $this->indicadorPerfilModel->getIndicadoresConCargoYArea();
        return view('management/list_indicador_perfil', $data);
    }



    public function addIndicadorPerfil()
    {
        $perfilCargoModel = new \App\Models\PerfilCargoModel();
        $areaModel = new \App\Models\AreaModel();

        $data['indicadores'] = $this->indicadorModel->findAll();
        $data['perfiles'] = $perfilCargoModel->findAll();
        $data['areas'] = $areaModel->where('estado_area', 'activa')->findAll();

        return view('management/add_indicador_perfil', $data);
    }



    public function addIndicadorPerfilPost()
    {
        $this->indicadorPerfilModel->save($this->request->getPost());
        return redirect()->to('/indicadores_perfil')->with('success', 'Asignación creada correctamente.');
    }

    public function editIndicadorPerfil($id)
    {
        $perfilCargoModel = new \App\Models\PerfilCargoModel();
        $areaModel = new \App\Models\AreaModel();

        $data['registro'] = $this->indicadorPerfilModel->find($id);
        $data['indicadores'] = $this->indicadorModel->findAll();
        $data['perfiles'] = $perfilCargoModel->findAll();
        $data['areas'] = $areaModel->where('estado_area', 'activa')->findAll();

        return view('management/edit_indicador_perfil', $data);
    }



    public function editIndicadorPerfilPost($id)
    {
        $this->indicadorPerfilModel->update($id, $this->request->getPost());
        return redirect()->to('/indicadores_perfil')->with('success', 'Asignación actualizada correctamente.');
    }

    public function deleteIndicadorPerfil($id)
    {
        $this->indicadorPerfilModel->delete($id);
        return redirect()->to('/indicadores_perfil')->with('success', 'Asignación eliminada correctamente.');
    }
}
