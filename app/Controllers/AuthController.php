<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function login()
{
    $session = session();
    $model = new \App\Models\UserModel();

    $correo = $this->request->getVar('correo');
    $password = $this->request->getVar('password');

    // Buscar al usuario por correo
    $usuario = $model->where('correo', $correo)->first();

    if ($usuario) {
        if ($usuario['activo'] == 0) {
            return redirect()->to('/login')->with('error', 'La cuenta está inactiva.');
        }

        if (password_verify($password, $usuario['password'])) {
            $sessionData = [
                'id_users' => $usuario['id_users'],
                'nombre_completo' => $usuario['nombre_completo'],
                'correo' => $usuario['correo'],
                'id_roles' => $usuario['id_roles'],
                'logged_in' => true
            ];
            $session->set($sessionData);

            // Redirigir según rol
            switch ($usuario['id_roles']) {
                case 1: return redirect()->to('/superadmin/superadmindashboard');
                case 2: return redirect()->to('/admin/admindashboard');
                case 3: return redirect()->to('/jefatura/jefaturadashboard');
                case 4: return redirect()->to('/trabajador/trabajadordashboard');
                default: return redirect()->to('/');
            }
            
        } else {
            return redirect()->to('/login')->with('error', 'Contraseña incorrecta.');
        }
    } else {
        return redirect()->to('/login')->with('error', 'Correo no registrado.');
    }
}

public function logout()
{
    session()->destroy(); // Elimina toda la sesión
    return redirect()->to('/login')->with('success', 'Has cerrado sesión correctamente.');
}


}
