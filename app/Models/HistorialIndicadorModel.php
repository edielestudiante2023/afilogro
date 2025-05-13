<?php
namespace App\Models;

use CodeIgniter\Model;

class HistorialIndicadorModel extends Model
{
    protected $table            = 'historial_indicadores';
    protected $primaryKey       = 'id_historial';
    protected $allowedFields    = [
        'id_indicador_perfil', 'id_usuario', 'periodo', 'valores_json', 'resultado_real', 'comentario', 'fecha_registro'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'fecha_registro';
    protected $updatedField     = '';
}
