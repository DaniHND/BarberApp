<?php
class CitaController extends BaseController {

    private Cita            $citaModel;
    private Configuracion   $configModel;
    private HistorialVisita $historialModel;
    private Ingreso         $ingresoModel;

    public function __construct() {
        $this->citaModel      = new Cita();
        $this->configModel    = new Configuracion();
        $this->historialModel = new HistorialVisita();
        $this->ingresoModel   = new Ingreso();
    }

    // GET /citas
    public function index(): void {
        $this->auth();

        // Auto-marcar como no_presentado las citas cuya hora ya pasó
        $this->citaModel->autoLiberarExpirados();

        $fecha  = $_GET['fecha'] ?? date('Y-m-d');
        $citas  = $this->citaModel->getAgendaFecha($fecha);
        $cfg    = $this->configModel->getAll();

        $stats = [
            'total'          => count($citas),
            'reservados'     => count(array_filter($citas, fn($c) => $c['estado'] === 'reservado')),
            'confirmados'    => count(array_filter($citas, fn($c) => $c['estado'] === 'confirmado')),
            'atendidos'      => count(array_filter($citas, fn($c) => $c['estado'] === 'atendido')),
            'no_presentados' => count(array_filter($citas, fn($c) => $c['estado'] === 'no_presentado')),
        ];

        $this->render('citas/index', [
            'pageTitle'  => 'Agenda de Citas',
            'activeNav'  => 'citas',
            'citas'      => $citas,
            'eventosFC'  => $this->citaModel->getEventosFC($fecha),
            'fecha'      => $fecha,
            'fechaPrev'  => date('Y-m-d', strtotime($fecha . ' -1 day')),
            'fechaNext'  => date('Y-m-d', strtotime($fecha . ' +1 day')),
            'stats'      => $stats,
            'cfg'        => $cfg,
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // POST /citas/estado
    public function cambiarEstado(): void {
        $this->auth();
        $this->validateCsrf();

        $id     = (int) ($_POST['id']    ?? 0);
        $estado = $_POST['estado']        ?? '';
        $fecha  = $_POST['fecha']         ?? date('Y-m-d');

        // Registrar historial e ingreso cuando la cita pasa a atendido
        if ($estado === 'atendido') {
            $cita = $this->citaModel->findById($id);
            if ($cita) {
                if (!empty($cita['cliente_id'])) {
                    $this->historialModel->registrar(
                        (int)   $cita['cliente_id'],
                        $id,
                        (int)   $cita['servicio_id'],
                        (float) $cita['precio'],
                                $cita['fecha']
                    );
                }
                $this->ingresoModel->registrar(
                    $id,
                    (int)   $cita['servicio_id'],
                    (float) $cita['precio'],
                            $cita['fecha']
                );
            }
        }

        $this->citaModel->cambiarEstado($id, $estado);
        $this->redirect('citas?fecha=' . $fecha);
    }
}
