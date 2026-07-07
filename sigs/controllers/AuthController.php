<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../dao/UsuarioDAO.php';

class AuthController {
    private $usuarioDAO;

    public function __construct() {
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function login($username, $password) {
        $usuario = $this->usuarioDAO->obtenerPorUsuario($username);
        if ($usuario && password_verify($password, $usuario->contrasena)) {
            $_SESSION['id_usuario'] = $usuario->id_usuario;
            $_SESSION['usuario'] = $usuario->usuario;
            $_SESSION['rol'] = $usuario->rol;
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }
}
?>
