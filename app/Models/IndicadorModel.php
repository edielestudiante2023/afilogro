<?php
namespace App\Models;

use CodeIgniter\Model;

class IndicadorModel extends Model
{
    protected $table            = 'indicadores';
    protected $primaryKey       = 'id_indicador';
    protected $allowedFields    = [
        'nombre',
        'formula',
        'periodicidad',
        'ponderacion',
        'meta',
        'variables',
        'unidad',
        'objetivo_proceso',
        'objetivo_calidad',
        'formula_larga',
        'created_at'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // Si deseas manejar updated_at, cámbialo por el nombre del campo
}
