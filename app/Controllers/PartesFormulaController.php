<?php

namespace App\Controllers;

use App\Models\PartesFormulaModel;
use App\Models\IndicadorModel;
use CodeIgniter\Controller;

class PartesFormulaController extends Controller
{
    public function listPartesFormulaModel()
    {
        $model = new \App\Models\PartesFormulaModel();
        $data['partes'] = $model
            ->select('partes_formula_indicador.*, indicadores.nombre AS nombre_indicador')
            ->join('indicadores', 'indicadores.id_indicador = partes_formula_indicador.id_indicador')
            ->orderBy('partes_formula_indicador.id_indicador ASC, orden ASC')
            ->findAll();

        return view('management/list_partes_formula', $data);
    }


    public function addPartesFormulaModel()
    {
        $indicadorModel = new \App\Models\IndicadorModel();
        $data['indicadores'] = $indicadorModel->orderBy('nombre', 'ASC')->findAll();
        return view('management/add_partes_formula', $data);
    }


    public function addPartesFormulaModelPost()
    {
        $model = new PartesFormulaModel();
        $model->insert([
            'id_indicador' => $this->request->getPost('id_indicador'),
            'tipo_parte'   => $this->request->getPost('tipo_parte'),
            'valor'        => $this->request->getPost('valor'),
            'orden'        => $this->request->getPost('orden'),
        ]);
        return redirect()->to(site_url('partesformula/list'));
    }

    public function editPartesFormulaModel($id)
    {
        $model = new \App\Models\PartesFormulaModel();
        $indicadorModel = new \App\Models\IndicadorModel();

        $data['parte'] = $model->find($id);
        $data['indicadores'] = $indicadorModel->orderBy('nombre', 'ASC')->findAll();

        return view('management/edit_partes_formula', $data);
    }

    public function editPartesFormulaModelPost($id)
    {
        $model = new PartesFormulaModel();
        $model->update($id, [
            'id_indicador' => $this->request->getPost('id_indicador'),
            'tipo_parte'   => $this->request->getPost('tipo_parte'),
            'valor'        => $this->request->getPost('valor'),
            'orden'        => $this->request->getPost('orden'),
        ]);
        return redirect()->to(site_url('partesformula/list'));
    }

    public function deletePartesFormulaModel($id)
    {
        $model = new PartesFormulaModel();
        $model->delete($id);
        return redirect()->to(site_url('partesformula/list'));
    }

    public function uploadCSVForm()
    {
        return view('management/upload_partes_formula');
    }

    public function uploadCSVPost()
    {
        $file = $this->request->getFile('csv_file');

        if ($file->isValid() && $file->getClientExtension() === 'csv') {
            $path = $file->getTempName();
            $handle = fopen($path, "r");

            $model = new \App\Models\PartesFormulaModel();
            $headers = fgetcsv($handle); // skip headers

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $model->insert([
                    'id_indicador' => $row[0],
                    'tipo_parte'   => $row[1],
                    'valor'        => $row[2],
                    'orden'        => $row[3],
                ]);
            }

            fclose($handle);
            return redirect()->to(site_url('partesformula/list'))->with('success', 'CSV cargado correctamente.');
        }

        return redirect()->back()->with('error', 'Error al subir el archivo.');
    }
}
