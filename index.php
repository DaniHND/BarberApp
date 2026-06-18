<?php
// ── Bootstrap ─────────────────────────────────────────────
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// ── Sesión ────────────────────────────────────────────────
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => BASE_URL_PATH ?: '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ── Autoload ──────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $dirs = [
        BASE_PATH . '/models/',
        BASE_PATH . '/controllers/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Router ────────────────────────────────────────────────
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = trim(str_replace(BASE_URL_PATH, '', $uri), '/');
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET' => [
        ''                    => ['DashboardController', 'index'],
        'dashboard'           => ['DashboardController', 'index'],
        'login'               => ['AuthController',      'showLogin'],
        'logout'              => ['AuthController',      'logout'],
        'servicios'           => ['ServicioController',  'index'],
        'servicios/crear'     => ['ServicioController',  'crear'],
        'servicios/editar'    => ['ServicioController',  'editar'],
        'reservar'            => ['ReservaController',   'mostrar'],
        'reservar/horarios'   => ['ReservaController',   'horarios'],
        'reservar/gestionar'  => ['ReservaController',   'gestionar'],
        'citas'               => ['CitaController',       'index'],
        'espera'              => ['EsperaController',     'index'],
        'clientes'              => ['ClienteController',    'index'],
        'clientes/historial'    => ['ClienteController',    'historial'],
        'reportes'              => ['ReporteController',    'index'],
        'barberos'              => ['BarberoController',    'index'],
        'barberos/crear'        => ['BarberoController',    'crearForm'],
        'barberos/editar'       => ['BarberoController',    'editarForm'],
        'barberos/horario'      => ['BarberoController',    'horarioForm'],
    ],
    'POST' => [
        'login'                   => ['AuthController',     'login'],
        'servicios/guardar'       => ['ServicioController', 'guardar'],
        'servicios/actualizar'    => ['ServicioController', 'actualizar'],
        'servicios/eliminar'      => ['ServicioController', 'eliminar'],
        'servicios/toggle'        => ['ServicioController', 'toggle'],
        'reservar/guardar'        => ['ReservaController',  'guardar'],
        'reservar/cancelar'       => ['ReservaController',  'cancelar'],
        'reservar/reprogramar'    => ['ReservaController',  'reprogramar'],
        'citas/estado'            => ['CitaController',     'cambiarEstado'],
        'espera/agregar'          => ['EsperaController',   'agregar'],
        'espera/atender'          => ['EsperaController',   'atender'],
        'espera/cancelar'         => ['EsperaController',   'cancelar'],
        'barberos/guardar'        => ['BarberoController',  'guardar'],
        'barberos/actualizar'     => ['BarberoController',  'actualizar'],
        'barberos/toggle'         => ['BarberoController',  'toggle'],
        'barberos/eliminar'       => ['BarberoController',  'eliminar'],
        'barberos/horario/guardar'=> ['BarberoController',  'horarioGuardar'],
        'barberos/bloqueo/agregar'=> ['BarberoController',  'bloqueoAgregar'],
        'barberos/bloqueo/eliminar'=> ['BarberoController', 'bloqueoEliminar'],
    ],
];

$route = $routes[$method][$uri] ?? null;

if ($route) {
    [$controllerName, $action] = $route;
    $controller = new $controllerName();
    $controller->$action();
} else {
    // Ruta no encontrada → redirigir a dashboard (o login si no hay sesión)
    $target = empty($_SESSION['admin_id']) ? 'login' : 'dashboard';
    header('Location: ' . url($target));
    exit;
}
