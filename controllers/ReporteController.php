<?php
class ReporteController extends BaseController {

    private Ingreso $ingresoModel;

    public function __construct() {
        $this->ingresoModel = new Ingreso();
    }

    // GET /reportes
    public function index(): void {
        $this->auth();

        $anio = (int) ($_GET['anio'] ?? date('Y'));
        $mes  = (int) ($_GET['mes']  ?? date('n'));

        // Normalizar navegación mes/año
        if ($mes < 1)  { $mes = 12; $anio--; }
        if ($mes > 12) { $mes = 1;  $anio++; }

        $prevMes  = $mes === 1  ? 12 : $mes - 1;
        $prevAnio = $mes === 1  ? $anio - 1 : $anio;
        $nextMes  = $mes === 12 ? 1  : $mes + 1;
        $nextAnio = $mes === 12 ? $anio + 1 : $anio;

        $resumenHoy = $this->ingresoModel->getResumenHoy();
        $resumenMes = $this->ingresoModel->getResumenMes($anio, $mes);
        $porDia     = $this->ingresoModel->getPorDiaEnMes($anio, $mes);
        $topSvcs    = $this->ingresoModel->getTopServiciosMes($anio, $mes);
        $ultimas    = $this->ingresoModel->getUltimasCitas();

        $this->render('reportes/index', [
            'pageTitle'  => 'Reportes',
            'activeNav'  => 'reportes',
            'anio'       => $anio,
            'mes'        => $mes,
            'prevMes'    => $prevMes,
            'prevAnio'   => $prevAnio,
            'nextMes'    => $nextMes,
            'nextAnio'   => $nextAnio,
            'resumenHoy' => $resumenHoy,
            'resumenMes' => $resumenMes,
            'porDia'     => $porDia,
            'topSvcs'    => $topSvcs,
            'ultimas'    => $ultimas,
        ]);
    }
}
