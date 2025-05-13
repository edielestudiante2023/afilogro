<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id_users';

    protected $allowedFields = [
        'nombre_completo',
        'documento_identidad',
        'correo',
        'cargo',
        'password',
        'id_roles',
        'activo',
        'id_areas',
        'id_perfil_cargo',
        'id_jefe',
    ];

    protected $returnType     = 'array';
    protected $useTimestamps  = false;

    protected $validationRules = [
        'nombre_completo'     => 'required|min_length[3]',
        'documento_identidad' => 'required|numeric',
        

        'password'            => 'permit_empty|min_length[6]',
        'id_roles'            => 'required|integer',
        'id_areas'            => 'permit_empty|is_not_unique[areas.id_areas]',
        'id_perfil_cargo'     => 'required|is_not_unique[perfiles_cargo.id_perfil_cargo]',
        'activo'              => 'required|in_list[0,1]',
        'id_jefe'             => 'permit_empty|is_natural_no_zero|is_not_unique[users.id_users]'

    ];
    
    /**
 * Retorna los usuarios que tienen como jefe al ID dado.
 *
 * @param int $idJefe
 * @return array
 */
public function getSubordinadosDeJefe($idJefe)
{
    return $this->select('users.*, roles.nombre_rol AS rol_nombre, areas.nombre_area AS area_nombre')
                ->join('roles', 'roles.id_roles = users.id_roles')
                ->join('areas', 'areas.id_areas = users.id_areas', 'left')
                ->where('users.id_jefe', $idJefe)
                ->where('users.activo', 1)
                ->findAll();
}

}

