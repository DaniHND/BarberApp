<?php
class AuthController extends BaseController {

    private Administrador $adminModel;

    public function __construct() {
        $this->adminModel = new Administrador();
    }

    // GET /login
    public function showLogin(): void {
        $this->guest();
        $csrf_token = $this->csrfToken();
        $this->render('auth/login', compact('csrf_token'), withLayout: false);
    }

    // POST /login
    public function login(): void {
        $this->guest();
        $this->validateCsrf();

        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($usuario === '' || $password === '') {
            $csrf_token = $this->csrfToken();
            $error = 'Completa todos los campos.';
            $this->render('auth/login', compact('csrf_token', 'error', 'usuario'), withLayout: false);
            return;
        }

        $admin = $this->adminModel->findByUsuario($usuario);

        if (!$admin || !password_verify($password, $admin['password'])) {
            $csrf_token = $this->csrfToken();
            $error = 'Usuario o contraseña incorrectos.';
            $this->render('auth/login', compact('csrf_token', 'error', 'usuario'), withLayout: false);
            return;
        }

        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION['admin_id']     = $admin['id'];
        $_SESSION['admin_nombre'] = $admin['nombre'];
        $_SESSION['admin_usuario']= $admin['usuario'];

        $this->redirect('dashboard');
    }

    // GET /logout
    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        $this->redirect('login');
    }
}
