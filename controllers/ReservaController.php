<?php
class ReservaController extends BaseController {

    private Configuracion $configModel;
    private Cita          $citaModel;
    private Cliente       $clienteModel;
    private Servicio      $servicioModel;
    private Barbero       $barberoModel;

    public function __construct() {
        $this->configModel   = new Configuracion();
        $this->citaModel     = new Cita();
        $this->clienteModel  = new Cliente();
        $this->servicioModel = new Servicio();
        $this->barberoModel  = new Barbero();
    }

    // GET /reservar
    public function mostrar(): void {
        $servicios = $this->servicioModel->getAll(soloActivos: true);
        $barberos  = $this->barberoModel->getAll(soloActivos: true);
        $cfg       = $this->configModel->getAll();

        $this->renderPublico('public/reservar', [
            'pageTitle'  => 'Reserva tu cita',
            'servicios'  => $servicios,
            'barberos'   => $barberos,
            'cfg'        => $cfg,
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // GET /reservar/horarios  (JSON API)
    public function horarios(): void {
        header('Content-Type: application/json; charset=utf-8');

        $fecha      = $_GET['fecha']        ?? '';
        $servicioId = (int) ($_GET['servicio_id'] ?? 0);
        $barberoId  = (int) ($_GET['barbero_id']  ?? 0);

        if (!$fecha || !$servicioId || strtotime($fecha) < strtotime('today')) {
            echo json_encode([]);
            exit;
        }

        $cfg       = $this->configModel->getAll();
        $excluirId = isset($_GET['excluir']) ? (int)$_GET['excluir'] : null;
        $slots     = $this->citaModel->getSlotsDisponibles($fecha, $servicioId, $cfg, $excluirId, $barberoId);
        echo json_encode($slots);
        exit;
    }

    // POST /reservar/guardar
    public function guardar(): void {
        $this->validateCsrf();

        $nombre     = trim($_POST['nombre']      ?? '');
        $telefono   = trim($_POST['telefono']    ?? '');
        $servicioId = (int)  ($_POST['servicio_id'] ?? 0);
        $barberoId  = (int)  ($_POST['barbero_id']  ?? 0) ?: null;
        $fecha      = $_POST['fecha']             ?? '';
        $horaInicio = $_POST['hora_inicio']       ?? '';
        $horaFin    = $_POST['hora_fin']          ?? '';

        $errores = [];
        if ($nombre === '')                                        $errores[] = 'El nombre es obligatorio.';
        if (!$servicioId)                                         $errores[] = 'Debes seleccionar un servicio.';
        if (!$fecha || strtotime($fecha) < strtotime('today'))    $errores[] = 'La fecha seleccionada no es válida.';
        if (!$horaInicio || !$horaFin)                            $errores[] = 'Debes seleccionar una hora disponible.';
        if (empty($errores) && $this->citaModel->existeConflicto($fecha, $horaInicio, $horaFin)) {
            $errores[] = 'Esa hora ya fue reservada por otra persona. Elige otra.';
        }

        if (!empty($errores)) {
            $this->renderPublico('public/reservar', [
                'pageTitle'  => 'Reserva tu cita',
                'servicios'  => $this->servicioModel->getAll(soloActivos: true),
                'barberos'   => $this->barberoModel->getAll(soloActivos: true),
                'cfg'        => $this->configModel->getAll(),
                'csrf_token' => $this->csrfToken(),
                'errores'    => $errores,
                'datos'      => $_POST,
            ]);
            return;
        }

        $clienteId = $this->clienteModel->findOrCreate($nombre, $telefono ?: null);

        $result = $this->citaModel->crear([
            'cliente_id'       => $clienteId,
            'servicio_id'      => $servicioId,
            'barbero_id'       => $barberoId,
            'fecha'            => $fecha,
            'hora_inicio'      => $horaInicio,
            'hora_fin'         => $horaFin,
            'nombre_cliente'   => $nombre,
            'telefono_cliente' => $telefono ?: null,
        ]);

        $this->redirect('reservar/gestionar?token=' . $result['token'] . '&nueva=1');
    }

    // GET /reservar/gestionar?token=
    public function gestionar(): void {
        $token = $_GET['token'] ?? '';
        $cita  = $token ? $this->citaModel->findByToken($token) : false;

        $cfg  = $this->configModel->getAll();
        $nueva = !empty($_GET['nueva']);

        $this->renderPublico('public/gestionar', [
            'pageTitle'  => 'Tu cita',
            'cita'       => $cita,
            'cfg'        => $cfg,
            'nueva'      => $nueva,
            'csrf_token' => $cita ? $this->csrfToken() : '',
        ]);
    }

    // POST /reservar/cancelar
    public function cancelar(): void {
        $this->validateCsrf();
        $token = $_POST['token'] ?? '';
        $cita  = $token ? $this->citaModel->findByToken($token) : false;

        if ($cita && in_array($cita['estado'], ['reservado', 'confirmado'], true)) {
            $this->citaModel->cancelar((int) $cita['id']);
        }

        $this->redirect('reservar/gestionar?token=' . urlencode($token));
    }

    // POST /reservar/reprogramar
    public function reprogramar(): void {
        $this->validateCsrf();
        $token      = $_POST['token']       ?? '';
        $fecha      = $_POST['fecha']       ?? '';
        $horaInicio = $_POST['hora_inicio'] ?? '';
        $horaFin    = $_POST['hora_fin']    ?? '';
        $cita       = $token ? $this->citaModel->findByToken($token) : false;

        $ok = false;
        if (
            $cita &&
            in_array($cita['estado'], ['reservado', 'confirmado'], true) &&
            $fecha && strtotime($fecha) >= strtotime('today') &&
            $horaInicio && $horaFin &&
            !$this->citaModel->existeConflicto($fecha, $horaInicio, $horaFin, (int) $cita['id'])
        ) {
            $ok = $this->citaModel->reprogramar((int) $cita['id'], $fecha, $horaInicio, $horaFin);
        }

        $qs = 'reservar/gestionar?token=' . urlencode($token) . ($ok ? '&reprogramada=1' : '&errorRep=1');
        $this->redirect($qs);
    }

    // ── Helpers ──────────────────────────────────────────
    private function renderPublico(string $view, array $data = []): void {
        if (!array_key_exists('flash', $data) && !empty($_SESSION['flash'])) {
            $data['flash'] = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        extract($data, EXTR_SKIP);
        include BASE_PATH . '/views/public/header.php';
        include BASE_PATH . '/views/' . $view . '.php';
        include BASE_PATH . '/views/public/footer.php';
    }
}
