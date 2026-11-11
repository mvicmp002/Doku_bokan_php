<?php
// api/index.php
// Todo pedido de la API entra por acá.

header('Content-Type: application/json; charset=utf-8');

function enviarError(int $codigo, string $mensaje): void {
    http_response_code($codigo);
    echo json_encode(['ok' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

function metodoNoPermitido(array $permitidos): void {
    header('Allow: ' . implode(', ', $permitidos));
    enviarError(405, 'Método no permitido. Usá: ' . implode(', ', $permitidos));
}

try {
    require_once __DIR__ . '/../config/env.php';
    require_once __DIR__ . '/../config/sesion.php';

    // ----- CORS: permite que el juego (cliente web) pida datos sin que lo bloquee el navegador -----
    $origenPermitido = env('CORS_ORIGIN');
    $origen = $_SERVER['HTTP_ORIGIN'] ?? '';

    if ($origen !== '' && $origen === $origenPermitido) {
        header('Access-Control-Allow-Origin: ' . $origen);
        header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    $metodo = $_SERVER['REQUEST_METHOD'];

    // El navegador manda un OPTIONS de "consulta previa" antes de algunos pedidos
    if ($metodo === 'OPTIONS') {
        http_response_code(204);
        exit;
    }

    // ----- Lectura de la URL: /doku_bokan/api/jugadores/5 → ['jugadores', '5'] -----
    $ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if ($base !== '' && str_starts_with($ruta, $base)) {
        $ruta = substr($ruta, strlen($base));
    }
    $partes = array_values(array_filter(explode('/', trim($ruta, '/')), 'strlen'));

    if (($partes[0] ?? '') === 'index.php') {
        array_shift($partes);
    }

    $recurso = $partes[0] ?? '';
    $id      = $partes[1] ?? null;

    if (count($partes) > 2) {
        enviarError(404, 'Ruta no encontrada');
    }
    if ($id !== null && !ctype_digit($id)) {
        enviarError(400, 'El ID debe ser un número');
    }

    // ----- Enrutador: cada recurso en su módulo -----
    switch ($recurso) {
        case 'jugadores':
            require_once __DIR__ . '/../routes/jugadores.php';
            rutasJugadores($metodo, $id);
            break;
        case 'partidas':
            require_once __DIR__ . '/../routes/partidas.php';
            rutasPartidas($metodo, $id);
            break;
        case 'login':
            require_once __DIR__ . '/../routes/auth.php';
            rutasAuth($metodo);
            break;
        default:
            enviarError(404, 'Ruta no encontrada');
    }

} catch (Throwable $e) {
    // Cualquier fallo inesperado: se guarda en el log y se responde 500 sin mostrar detalles
    error_log('[Doku Bokan API] ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
    enviarError(500, 'Error interno del servidor');
}