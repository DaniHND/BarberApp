<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Reserva') ?> — <?= htmlspecialchars($cfg['nombre_barberia'] ?? APP_NAME) ?></title>

    <!-- Tailwind CSS -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        barber: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .slot-btn { transition: all .15s ease; }
        .slot-btn:active { transform: scale(.96); }
    </style>
</head>
<body class="bg-stone-100 min-h-screen">

<!-- Barber pole stripe -->
<div style="height:4px;background:repeating-linear-gradient(90deg,#dc2626 0,#dc2626 16px,#fff 16px,#fff 24px,#2563eb 24px,#2563eb 40px,#fff 40px,#fff 48px);"></div>

<!-- Top bar -->
<header class="bg-zinc-900 text-white shadow-md">
    <div class="max-w-lg mx-auto px-4 py-3.5 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="scissors" class="w-4 h-4 text-white"></i>
            </div>
            <span class="font-bold text-base"><?= htmlspecialchars($cfg['nombre_barberia'] ?? APP_NAME) ?></span>
        </div>
        <a href="<?= url('reservar') ?>"
           class="text-xs text-zinc-400 hover:text-white transition-colors flex items-center gap-1">
            <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
            Nueva cita
        </a>
    </div>
</header>

<!-- BASE_URL para JS -->
<script>const baseUrl = '<?= BASE_URL ?>';</script>
