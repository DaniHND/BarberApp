<?php
abstract class BaseController {

    // ── Renderizar vista con layout ──────────────────────
    protected function render(string $view, array $data = [], bool $withLayout = true): void {
        extract($data, EXTR_SKIP);

        if ($withLayout) {
            $viewFile = BASE_PATH . '/views/' . $view . '.php';
            require BASE_PATH . '/views/layouts/header.php';
            require $viewFile;
            require BASE_PATH . '/views/layouts/footer.php';
        } else {
            require BASE_PATH . '/views/' . $view . '.php';
        }
    }

    // ── Redirección ──────────────────────────────────────
    protected function redirect(string $path = ''): never {
        header('Location: ' . url($path));
        exit;
    }

    // ── Respuesta JSON ───────────────────────────────────
    protected function json(mixed $data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ── Proteger rutas: requiere sesión activa ───────────
    protected function auth(): void {
        if (empty($_SESSION['admin_id'])) {
            $this->redirect('login');
        }
    }

    // ── Proteger rutas: solo para no autenticados ────────
    protected function guest(): void {
        if (!empty($_SESSION['admin_id'])) {
            $this->redirect('dashboard');
        }
    }

    // ── Token CSRF ───────────────────────────────────────
    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrf(): void {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Token de seguridad inválido. Recarga la página e intenta de nuevo.');
        }
    }
}
