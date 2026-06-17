<?php
class EsperaController extends BaseController {

    private ListaEspera $modelo;
    private Servicio    $servicioModel;

    public function __construct() {
        $this->modelo        = new ListaEspera();
        $this->servicioModel = new Servicio();
    }

    // GET /espera
    public function index(): void {
        $this->auth();

        $this->render('espera/index', [
            'pageTitle'  => 'Lista de Espera',
            'activeNav'  => 'espera',
            'lista'      => $this->modelo->getEsperando(),
            'servicios'  => $this->servicioModel->getAll(soloActivos: true),
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    // POST /espera/agregar
    public function agregar(): void {
        $this->auth();
        $this->validateCsrf();

        $nombre     = trim($_POST['nombre']      ?? '');
        $telefono   = trim($_POST['telefono']    ?? '');
        $servicioId = (int) ($_POST['servicio_id'] ?? 0);

        if ($nombre !== '') {
            $this->modelo->agregar([
                'nombre'      => $nombre,
                'telefono'    => $telefono ?: null,
                'servicio_id' => $servicioId ?: null,
            ]);
            $_SESSION['flash'] = [
                'type'    => 'success',
                'title'   => 'Agregado a la espera',
                'message' => "{$nombre} fue añadido a la lista.",
            ];
        }
        $this->redirect('espera');
    }

    // POST /espera/atender
    public function atender(): void {
        $this->auth();
        $this->validateCsrf();
        $this->modelo->atender((int) ($_POST['id'] ?? 0));
        $this->redirect('espera');
    }

    // POST /espera/cancelar
    public function cancelar(): void {
        $this->auth();
        $this->validateCsrf();
        $this->modelo->cancelar((int) ($_POST['id'] ?? 0));
        $this->redirect('espera');
    }
}
