<?php
require_once __DIR__ . '/config/sesion.php';
requerirSesion();
require_once __DIR__ . '/models/Partida.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$partida = $id ? (new Partida())->buscarPorId($id) : null;

// Si no existe, no es suya o ya está completada, vuelve a la pantalla de espacios
if (!$partida
    || (int) $partida['jugador_id'] !== (int) $_SESSION['jugador_id']
    || $partida['estado'] === 'completada') {
    header('Location: partidas.php');
    exit;
}

$_SESSION['partida_id'] = (int) $partida['id'];

$destinos = [1 => 'mision1.php', 2 => 'mision2.php', 3 => 'mision3.php', 4 => 'misionfinal.php'];
header('Location: ' . ($destinos[(int) $partida['mision_alcanzada']] ?? 'mision1.php'));
exit;