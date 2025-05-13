<?php

namespace App\Models;

use CodeIgniter\Model;

class IndicadorPerfilModel extends Model
{
    protected $table            = 'indicadores_perfil';
    protected $primaryKey       = 'id_indicador_perfil';
    protected $allowedFields    = ['id_indicador', 'id_perfil_cargo', 'meta', 'periodicidad', 'ponderacion'];
    protected $returnType       = 'array';

    public function getIndicadoresPorPerfil($idPerfil)
    {
        return $this->select('
                indicadores_perfil.*,
                indicadores.nombre,
                indicadores.formula,
                indicadores.formula_larga,
                indicadores.variables,
                indicadores.unidad,
                indicadores.objetivo_proceso,
                indicadores.objetivo_calidad,
                indicadores.created_at
            ')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->where('indicadores_perfil.id_perfil_cargo', $idPerfil)
            ->findAll();
    }

    public function getIndicadoresConNombreCargo()
    {
        return $this->select('
            indicadores_perfil.*,
            indicadores.nombre AS nombre_indicador,
            perfiles_cargo.nombre_cargo
        ')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->join('perfiles_cargo', 'perfiles_cargo.id_perfil_cargo = indicadores_perfil.id_perfil_cargo')
            ->orderBy('perfiles_cargo.nombre_cargo', 'ASC')
            ->findAll();
    }

    public function getIndicadoresConCargoYArea()
    {
        return $this->select('
            indicadores_perfil.*,
            indicadores.nombre AS nombre_indicador,
            perfiles_cargo.nombre_cargo,
            perfiles_cargo.area,
            areas.nombre_area
        ')
            ->join('indicadores', 'indicadores.id_indicador = indicadores_perfil.id_indicador')
            ->join('perfiles_cargo', 'perfiles_cargo.id_perfil_cargo = indicadores_perfil.id_perfil_cargo')
            ->join('areas', 'areas.nombre_area = perfiles_cargo.area')
            ->where('areas.estado_area', 'activa')
            ->orderBy('areas.nombre_area', 'ASC')
            ->findAll();
    }
}
