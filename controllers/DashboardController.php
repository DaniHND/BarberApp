<?php
class DashboardController extends BaseController {

    private Dashboard $dashboard;

    public function __construct() {
        $this->dashboard = new Dashboard();
    }

    // GET /  |  GET /dashboard
    public function index(): void {
        $this->auth();

        $stats        = $this->dashboard->getStatsHoy();
        $citasSemana  = $this->dashboard->getCitasUltimosDias(7);
        $topServicios = $this->dashboard->getTopServicios(5);

        $this->render('dashboard/index', [
            'pageTitle'    => 'Dashboard',
            'activeNav'    => 'dashboard',
            'stats'        => $stats,
            'citasSemana'  => $citasSemana,
            'topServicios' => $topServicios,
        ]);
    }
}
