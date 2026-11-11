<?php
// config/sesion.php
// Se incluye al principio de cada página para manejar la sesión.

session_start();

function estaLogueado(): bool {
    return isset($_SESSION['jugador_id']);
}

// Si no hay sesión iniciada, manda al login y corta la página.
function requerirSesion(): void {
    // Evita que el botón "atrás" muestre la página después de cerrar sesión
    header('Cache-Control: no-store, no-cache, must-revalidate');

    if (!estaLogueado()) {
        header('Location: login.php');
        exit;
    }
}

// Devuelve la partida activa del jugador y verifica que esté en la misión indicada.
function requerirPartida(int $mision): array {
    requerirSesion();
    require_once __DIR__ . '/../models/Partida.php';

    $id = $_SESSION['partida_id'] ?? null;
    $partida = $id ? (new Partida())->buscarPorId((int) $id) : null;

    // Sin partida activa, o que no es suya, o que ya terminó: vuelve a elegir
    if (!$partida
        || (int) $partida['jugador_id'] !== (int) $_SESSION['jugador_id']
        || $partida['estado'] !== 'en_curso') {
        unset($_SESSION['partida_id']);
        header('Location: partidas.php');
        exit;
    }

    // Si quiere entrar a una misión que no le toca, lo manda a la que le corresponde
    if ((int) $partida['mision_alcanzada'] !== $mision) {
        header('Location: jugar.php?id=' . (int) $partida['id']);
        exit;
    }

    return $partida;
}