<?php
// routes/auth.php  → /api/login

require_once __DIR__ . '/../controllers/JugadorController.php';

function rutasAuth(string $metodo): void {
    if ($metodo === 'POST') {
        (new JugadorController())->login();
    }
    metodoNoPermitido(['POST']);
}