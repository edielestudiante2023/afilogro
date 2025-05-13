<?php namespace App\Controllers;

use App\Models\IndicadorModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class IndicadorController extends BaseController
{
    /** @var IndicadorModel */
    protected $indicadorModel;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        helper(['url', 'form']);
        $this->indicadorModel = new IndicadorModel();
    }

    // Listar indicadores
    public function listIndicador()
    {
        $data['indicadores'] = $this->indicadorModel->orderBy('created_at', 'DESC')->findAll();
        return view('management/list_indicador', $data);
    }

    // Formulario crear indicador
    public function addIndicador()
    {
        return view('management/add_indicador');
    }

    // Procesar creación indicador
    public function addIndicadorPost()
    {
        $rules = [
            'nombre'             => 'required',
            'formula'            => 'required',
            'periodicidad'       => 'required',
            'ponderacion'       => 'required',
            'meta'               => 'required',
            'variables'          => 'required',
            'unidad'             => 'required',
            'objetivo_proceso'   => 'required',
            'objetivo_calidad'   => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                             ->with('errors', $this->validator->getErrors())
                             ->withInput();
        }

        $data = $this->request->getPost();
        $this->indicadorModel->insert($data);
        return redirect()->to('/indicadores')->with('success', 'Indicador creado.');
    }

    // Formulario editar indicador
    public function editIndicador($id)
    {
        $indicador = $this->indicadorModel->find($id);
        if (! $indicador) {
            throw new PageNotFoundException("Indicador con ID $id no existe");
        }
        return view('management/edit_indicador', ['indicador' => $indicador]);
    }

    // Procesar edición indicador
    public function editIndicadorPost($id)
    {
        $rules = [
            'nombre'             => 'required',
            'formula'            => 'required',
            'periodicidad'       => 'required',
            'ponderacion'       => 'required',
            'meta'               => 'required',
            'variables'          => 'required',
            'unidad'             => 'required',
            'objetivo_proceso'   => 'required',
            'objetivo_calidad'   => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                             ->with('errors', $this->validator->getErrors())
                             ->withInput();
        }

        $data = $this->request->getPost();
        $this->indicadorModel->update($id, $data);
        return redirect()->to('/indicadores')->with('success', 'Indicador actualizado.');
    }

    // Eliminar indicador
    public function deleteIndicador($id)
    {
        $this->indicadorModel->delete($id);
        return redirect()->to('/indicadores')->with('success', 'Indicador eliminado.');
    }
}
