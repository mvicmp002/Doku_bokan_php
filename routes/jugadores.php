<?php
// routes/jugadores.php
// Decide qué función del controlador atiende cada verbo HTTP.

require_once __DIR__ . '/../controllers/JugadorController.php';

function rutasJugadores(string $metodo, ?string $id): void {
    $c = new JugadorController();

    // /api/jugadores
    if ($id === null) {
        if ($metodo === 'POST') {
            $c->registrar();
        }
        metodoNoPermitido(['POST']);
    }

    // /api/jugadores/:id
    $id = (int) $id;
    switch ($metodo) {
        case 'GET':
            $c->mostrar($id);
            break;
        case 'PUT':
        case 'PATCH':
            $c->actualizar($id);
            break;
        case 'DELETE':
            $c->eliminar($id);
            break;
        default:
            metodoNoPermitido(['GET', 'PUT', 'PATCH', 'DELETE']);
    }
}