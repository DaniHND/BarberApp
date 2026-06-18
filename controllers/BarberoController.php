<?php
class BarberoController extends BaseController {

    private Barbero $model;

    public function __construct() {
        $this->model = new Barbero();
    }

    // GET /barberos
    public function index(): void {
        $this->auth();
        $barberos = $this->model->getAll();
        $this->render('barberos/index', [
            'pageTitle'  => 'Barberos',
            'activeNav'  => 'barberos',
            'barberos'   => $barberos,
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // GET /barberos/crear
    public function crearForm(): void {
        $this->auth();
        $this->render('barberos/crear', [
            'pageTitle'  => 'Agregar barbero',
            'activeNav'  => 'barberos',
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // POST /barberos/guardar
    public function guardar(): void {
        $this->auth();
        $this->validateCsrf();

        $nombre      = trim($_POST['nombre']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $orden       = (int) ($_POST['orden']     ?? 0);

        if (!$nombre) {
            $_SESSION['flash'] = ['type' => 'error', 'title' => 'Error', 'message' => 'El nombre es obligatorio.'];
            $this->redirect('barberos/crear');
        }

        $id = $this->model->crear(['nombre' => $nombre, 'descripcion' => $descripcion, 'orden' => $orden]);
        $_SESSION['flash'] = ['type' => 'success', 'title' => '¡Barbero agregado!', 'message' => "«{$nombre}» fue registrado."];
        $this->redirect('barberos/horario?id=' . $id);
    }

    // GET /barberos/editar?id=X
    public function editarForm(): void {
        $this->auth();
        $id      = (int) ($_GET['id'] ?? 0);
        $barbero = $id ? $this->model->findById($id) : false;
        if (!$barbero) { $this->redirect('barberos'); }

        $this->render('barberos/editar', [
            'pageTitle'  => 'Editar barbero',
            'activeNav'  => 'barberos',
            'csrf_token' => $this->csrfToken(),
            'barbero'    => $barbero,
        ]);
    }

    // POST /barberos/actualizar
    public function actualizar(): void {
        $this->auth();
        $this->validateCsrf();

        $id          = (int) ($_POST['id']          ?? 0);
        $nombre      = trim($_POST['nombre']         ?? '');
        $descripcion = trim($_POST['descripcion']    ?? '');
        $orden       = (int) ($_POST['orden']        ?? 0);

        if (!$id || !$nombre) {
            $this->redirect('barberos');
        }

        $this->model->actualizar($id, ['nombre' => $nombre, 'descripcion' => $descripcion, 'orden' => $orden]);
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Cambios guardados', 'message' => ''];
        $this->redirect('barberos');
    }

    // GET /barberos/horario?id=X
    public function horarioForm(): void {
        $this->auth();
        $id      = (int) ($_GET['id'] ?? 0);
        $barbero = $id ? $this->model->findById($id) : false;
        if (!$barbero) { $this->redirect('barberos'); }

        $this->render('barberos/horario', [
            'pageTitle'  => 'Horario — ' . $barbero['nombre'],
            'activeNav'  => 'barberos',
            'csrf_token' => $this->csrfToken(),
            'barbero'    => $barbero,
            'horarios'   => $this->model->getHorarios($id),
            'bloqueos'   => $this->model->getBloqueos($id, soloFuturos: true),
        ]);
    }

    // POST /barberos/horario/guardar
    public function horarioGuardar(): void {
        $this->auth();
        $this->validateCsrf();

        $id = (int) ($_POST['barbero_id'] ?? 0);
        if (!$id || !$this->model->findById($id)) { $this->redirect('barberos'); }

        $horarios = [];
        foreach (range(1, 6) as $dia) {
            $horarios[$dia] = [
                'activo'      => !empty($_POST['dias'][$dia]['activo']),
                'hora_inicio' => $_POST['dias'][$dia]['hora_inicio'] ?? '08:00',
                'hora_fin'    => $_POST['dias'][$dia]['hora_fin']    ?? '19:00',
            ];
        }
        $this->model->guardarHorarios($id, $horarios);
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Horario guardado', 'message' => ''];
        $this->redirect('barberos/horario?id=' . $id);
    }

    // POST /barberos/bloqueo/agregar
    public function bloqueoAgregar(): void {
        $this->auth();
        $this->validateCsrf();

        $barberoId = (int)   ($_POST['barbero_id'] ?? 0);
        $fecha     = trim($_POST['fecha']           ?? '');
        $motivo    = trim($_POST['motivo']          ?? '');

        if ($barberoId && $fecha && strtotime($fecha) !== false) {
            $this->model->agregarBloqueo($barberoId, $fecha, $motivo ?: null);
        }
        $this->redirect('barberos/horario?id=' . $barberoId);
    }

    // POST /barberos/bloqueo/eliminar
    public function bloqueoEliminar(): void {
        $this->auth();
        $this->validateCsrf();

        $id        = (int) ($_POST['id']         ?? 0);
        $barberoId = (int) ($_POST['barbero_id'] ?? 0);
        if ($id && $barberoId) {
            $this->model->eliminarBloqueo($id, $barberoId);
        }
        $this->redirect('barberos/horario?id=' . $barberoId);
    }

    // POST /barberos/toggle
    public function toggle(): void {
        $this->auth();
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) $this->model->toggle($id);
        $this->redirect('barberos');
    }

    // POST /barberos/eliminar
    public function eliminar(): void {
        $this->auth();
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) $this->model->eliminar($id);
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Barbero eliminado', 'message' => ''];
        $this->redirect('barberos');
    }
}
