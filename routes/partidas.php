<?php
// routes/partidas.php

require_once __DIR__ . '/../controllers/PartidaController.php';

function rutasPartidas(string $metodo, ?string $id): void {
    $c = new PartidaController();

    // /api/partidas
    if ($id === null) {
        if ($metodo === 'GET') {
            $c->ranking();
        }
        if ($metodo === 'POST') {
            $c->crear();
        }
        metodoNoPermitido(['GET', 'POST']);
    }

    // /api/partidas/:id
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