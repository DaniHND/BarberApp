<?php
class ClienteController extends BaseController {

    private Cliente         $clienteModel;
    private HistorialVisita $historialModel;

    public function __construct() {
        $this->clienteModel   = new Cliente();
        $this->historialModel = new HistorialVisita();
    }

    // GET /clientes
    public function index(): void {
        $this->auth();

        $clientes = $this->clienteModel->getAll();

        $this->render('clientes/index', [
            'pageTitle' => 'Clientes Frecuentes',
            'activeNav' => 'clientes',
            'clientes'  => $clientes,
        ]);
    }

    // GET /clientes/historial?id=X
    public function historial(): void {
        $this->auth();

        $id      = (int) ($_GET['id'] ?? 0);
        $cliente = $this->clienteModel->findById($id);

        if (!$cliente || (int)$cliente['total_visitas'] === 0) {
            $this->redirect('clientes');
        }

        $historial = $this->historialModel->getByCliente($id);

        $this->render('clientes/historial', [
            'pageTitle' => 'Historial — ' . $cliente['nombre'],
            'activeNav' => 'clientes',
            'cliente'   => $cliente,
            'historial' => $historial,
        ]);
    }
}
