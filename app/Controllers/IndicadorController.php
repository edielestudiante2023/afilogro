<?php namespace App\Controllers;

use App\Models\IndicadorModel;
use App\Models\PartesFormulaModel;
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
        $indicadores = $this->indicadorModel->orderBy('created_at', 'DESC')->findAll();

        // Renderizar fórmula desde partes_formula_indicador
        $formulaModel = new PartesFormulaModel();
        foreach ($indicadores as &$i) {
            $i['formula_renderizada'] = $formulaModel->getFormulaComoTexto($i['id_indicador']);
        }

        return view('management/list_indicador', ['indicadores' => $indicadores]);
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
            'periodicidad'       => 'required',
            'ponderacion'        => 'required',
            'meta'               => 'required',
            'unidad'             => 'required',
            'objetivo_proceso'   => 'required',
            'objetivo_calidad'   => 'required',
            'tipo_aplicacion'    => 'required|in_list[cargo,area]'
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
            'periodicidad'       => 'required',
            'ponderacion'        => 'required',
            'meta'               => 'required',
            'unidad'             => 'required',
            'objetivo_proceso'   => 'required',
            'objetivo_calidad'   => 'required',
            'tipo_aplicacion'    => 'required|in_list[cargo,area]'
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

    // 1. Mostrar formulario de diligenciamiento
    public function fillIndicador($id)
    {
        $indicador = $this->indicadorModel->find($id);
        if (! $indicador) throw new PageNotFoundException();

        $partesModel = new PartesFormulaModel();
        $partes      = $partesModel->getPartesPorIndicador($id);

        return view('management/fill_indicador', [
            'indicador' => $indicador,
            'partes'    => $partes
        ]);
    }

    // 2. Recibir datos, armar y evaluar
  public function fillIndicadorPost($id)
{
    // Carga las partes de la fórmula para el indicador
    $partesModel = new PartesFormulaModel();
    $partes      = $partesModel->getPartesPorIndicador($id);
    $inputs      = $this->request->getPost('dato'); // e.g. ['VENTAS_ACTUAL' => 100, …]

    // Construir la expresión matemática sustituyendo cada 'dato' por su valor
    $formula = '';
    foreach ($partes as $p) {
        if ($p['tipo_parte'] === 'dato') {
            // Toma el valor ingresado (o 0 si no existe)
            $val = isset($inputs[$p['valor']]) ? floatval($inputs[$p['valor']]) : 0;
            $formula .= " {$val}";
        } else {
            $formula .= " {$p['valor']}";
        }
    }

    // Evaluar la expresión y devolver el resultado
    $resultado = eval("return {$formula};");

    // Renderizar la vista con el indicador, la fórmula evaluada y el resultado
    return view('management/fill_result', [
        'indicador' => $this->indicadorModel->find($id),
        'formula'   => $formula,
        'resultado' => $resultado,
    ]);
}

}
