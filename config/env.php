<?php
// config/env.php
// Lee el archivo .env y deja las variables disponibles con env('NOMBRE').

function cargarEnv(string $ruta): void {
    if (!is_readable($ruta)) {
        throw new RuntimeException('No se encontró el archivo .env');
    }

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        $linea = trim($linea);

        // Se ignoran los comentarios (#) y las líneas sin "="
        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
            continue;
        }

        [$clave, $valor] = explode('=', $linea, 2);
        $_ENV[trim($clave)] = trim($valor, " \t\"'");
    }
}

function env(string $clave, ?string $defecto = null): ?string {
    return $_ENV[$clave] ?? $defecto;
}

cargarEnv(__DIR__ . '/../.env');