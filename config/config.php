<?php
// ── Zona horaria ──────────────────────────────────────────
date_default_timezone_set('America/Tegucigalpa');

// ── Entorno ───────────────────────────────────────────────
define('APP_NAME',    'BarberApp');
define('APP_VERSION', '1.0');
define('BASE_PATH',   dirname(__DIR__));

// ── URL base (auto-detectada) ────────────────────────────
$_protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host       = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_scriptDir  = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

define('BASE_URL',      $_protocol . '://' . $_host . $_scriptDir);
define('BASE_URL_PATH', $_scriptDir);

unset($_protocol, $_host, $_scriptDir);

// ── Sesión ────────────────────────────────────────────────
define('SESSION_LIFETIME', 28800); // 8 horas

// ── Reporte de errores (desactivar en producción) ─────────
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ── Helper: URL ──────────────────────────────────────────
function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

// ── Helper: formato de moneda ────────────────────────────
function moneda(float $monto): string {
    return 'L. ' . number_format($monto, 2);
}

// ── Helper: duración en formato legible ─────────────────
function duracionFmt(int $minutos): string {
    if ($minutos < 60) return "{$minutos} min";
    $h = intdiv($minutos, 60);
    $m = $minutos % 60;
    return $m > 0 ? "{$h} h {$m} min" : "{$h} h";
}

// ── Helper: fecha en español ─────────────────────────────
function fechaEsp(string|int $timestamp = '', string $formato = 'completa'): string {
    $ts    = is_int($timestamp) ? $timestamp : ($timestamp ? strtotime($timestamp) : time());
    $dias  = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes',
              'Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
    $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril',
              'May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto',
              'September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
    $dia = $dias[date('l', $ts)];
    $mes = $meses[date('F', $ts)];
    return match($formato) {
        'corta'    => date('d', $ts) . ' ' . $mes . ' ' . date('Y', $ts),
        'completa' => $dia . ', ' . date('d', $ts) . ' de ' . $mes . ' de ' . date('Y', $ts),
        default    => date($formato, $ts),
    };
}
