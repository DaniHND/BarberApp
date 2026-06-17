<?php
class ServicioController extends BaseController {

    private Servicio $model;

    public function __construct() {
        $this->model = new Servicio();
    }

    // GET /servicios
    public function index(): void {
        $this->auth();
        $servicios = $this->model->getAll();
        $this->render('servicios/index', [
            'pageTitle'  => 'Servicios',
            'activeNav'  => 'servicios',
            'servicios'  => $servicios,
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // GET /servicios/crear
    public function crear(): void {
        $this->auth();
        $this->render('servicios/crear', [
            'pageTitle'  => 'Nuevo Servicio',
            'activeNav'  => 'servicios',
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // POST /servicios/guardar
    public function guardar(): void {
        $this->auth();
        $this->validateCsrf();

        $datos   = $this->extraer($_POST);
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $this->render('servicios/crear', [
                'pageTitle'  => 'Nuevo Servicio',
                'activeNav'  => 'servicios',
                'csrf_token' => $this->csrfToken(),
                'errores'    => $errores,
                'datos'      => $datos,
            ]);
            return;
        }

        $this->model->crear($datos);
        $_SESSION['flash'] = [
            'type'    => 'success',
            'title'   => 'Servicio creado',
            'message' => "«{$datos['nombre']}» fue agregado correctamente.",
        ];
        $this->redirect('servicios');
    }

    // GET /servicios/editar?id=X
    public function editar(): void {
        $this->auth();
        $id       = (int) ($_GET['id'] ?? 0);
        $servicio = $this->model->findById($id);
        if (!$servicio) { $this->redirect('servicios'); }

        $this->render('servicios/editar', [
            'pageTitle'  => 'Editar Servicio',
            'activeNav'  => 'servicios',
            'csrf_token' => $this->csrfToken(),
            'servicio'   => $servicio,
        ]);
    }

    // POST /servicios/actualizar
    public function actualizar(): void {
        $this->auth();
        $this->validateCsrf();

        $id      = (int) ($_POST['id'] ?? 0);
        $datos   = $this->extraer($_POST);
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $this->render('servicios/editar', [
                'pageTitle'  => 'Editar Servicio',
                'activeNav'  => 'servicios',
                'csrf_token' => $this->csrfToken(),
                'servicio'   => array_merge(['id' => $id], $datos),
                'errores'    => $errores,
            ]);
            return;
        }

        $this->model->actualizar($id, $datos);
        $_SESSION['flash'] = [
            'type'    => 'success',
            'title'   => 'Servicio actualizado',
            'message' => "«{$datos['nombre']}» fue actualizado correctamente.",
        ];
        $this->redirect('servicios');
    }

    // POST /servicios/eliminar
    public function eliminar(): void {
        $this->auth();
        $this->validateCsrf();

        $id       = (int) ($_POST['id'] ?? 0);
        $servicio = $this->model->findById($id);

        if ($servicio) {
            $accion = $this->model->eliminar($id);
            $msg    = $accion === 'eliminado'
                ? "«{$servicio['nombre']}» fue eliminado."
                : "«{$servicio['nombre']}» fue desactivado porque tiene citas asociadas.";
            $_SESSION['flash'] = ['type' => 'success', 'title' => 'Operación exitosa', 'message' => $msg];
        }
        $this->redirect('servicios');
    }

    // POST /servicios/toggle
    public function toggle(): void {
        $this->auth();
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $this->model->toggleActivo($id);
        $this->redirect('servicios');
    }

    // ── Helpers ──────────────────────────────────────────
    private function extraer(array $post): array {
        return [
            'nombre'           => trim($post['nombre'] ?? ''),
            'descripcion'      => trim($post['descripcion'] ?? ''),
            'precio'           => $post['precio'] ?? '',
            'duracion_minutos' => $post['duracion_minutos'] ?? '',
            'activo'           => isset($post['activo']) ? 1 : 0,
        ];
    }

    private function validar(array $d): array {
        $e = [];
        if ($d['nombre'] === '')
            $e[] = 'El nombre del servicio es obligatorio.';
        if (!is_numeric($d['precio']) || (float) $d['precio'] < 0)
            $e[] = 'El precio debe ser un número mayor o igual a cero.';
        if (!is_numeric($d['duracion_minutos']) || (int) $d['duracion_minutos'] < 1)
            $e[] = 'La duración debe ser al menos 1 minuto.';
        return $e;
    }
}
