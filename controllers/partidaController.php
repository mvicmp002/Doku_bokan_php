<?php
// controllers/PartidaController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Partida.php';

class PartidaController extends Controller {
    private Partida $modelo;

    public function __construct() {
        $this->modelo = new Partida();
    }

    // GET /api/partidas  → ranking de mejores tiempos (?limite=10)
    public function ranking(): void {
        $limite = $this->entero($_GET['limite'] ?? 10);
        if ($limite === null || $limite < 1 || $limite > 50) {
            $limite = 10;
        }
        $this->responder(200, ['ok' => true, 'ranking' => $this->modelo->ranking($limite)]);
    }

    // GET /api/partidas/:id
    public function mostrar(int $id): void {
        $this->responder(200, ['ok' => true, 'partida' => $this->cargarPropia($id)]);
    }

     // POST /api/partidas  → empezar una partida (máximo 3 por jugador)
    // Body opcional: { "slot": 2 }. Si no se manda, usa el primer espacio libre.
    public function crear(): void {
        $jugadorId = $this->jugadorLogueado();
        $d = $this->leerJson();

        $ocupados = array_map(fn($p) => (int) $p['slot'], $this->modelo->deJugador($jugadorId));

        if (count($ocupados) >= Partida::MAX_PARTIDAS) {
            $this->error(409, 'Ya tenés ' . Partida::MAX_PARTIDAS . ' partidas guardadas. Borrá una para empezar otra', [
                'maximo' => Partida::MAX_PARTIDAS,
            ]);
        }

        $slot = null;
        if (array_key_exists('slot', $d)) {
            $slot = $this->entero($d['slot']);
            if ($slot === null || $slot < 1 || $slot > Partida::MAX_PARTIDAS) {
                $this->error(400, 'slot debe ser un número entre 1 y ' . Partida::MAX_PARTIDAS);
            }
            if (in_array($slot, $ocupados, true)) {
                $this->error(409, 'Ese espacio ya está ocupado');
            }
        }

        $id = $this->modelo->crear($jugadorId, $slot);
        if ($id === null) {
            $this->error(409, 'No se pudo crear la partida: el espacio está ocupado o no hay lugar');
        }

        $this->responder(201, [
            'ok'      => true,
            'mensaje' => 'Partida creada',
            'partida' => $this->modelo->buscarPorId($id),
        ]);
    }

      // PUT/PATCH /api/partidas/:id  → guardar progreso o finalizar
    // Progreso:   { "mision_alcanzada": 2 }
    // Finalizar:  { "estado": "completada" }
    public function actualizar(int $id): void {
        $partida   = $this->cargarPropia($id);
        $jugadorId = $this->jugadorLogueado();
        $d = $this->leerJson();

        if ($partida['estado'] === 'completada') {
            $this->error(400, 'La partida ya está completada');
        }

        $estado = $d['estado'] ?? null;
        if ($estado !== null && !in_array($estado, ['en_curso', 'completada'], true)) {
            $this->error(400, "El estado debe ser 'en_curso' o 'completada'");
        }

        $actual = (int) $partida['mision_alcanzada'];

        // Solo se puede quedar en la misma misión o avanzar a la siguiente
        $mision = null;
        if (array_key_exists('mision_alcanzada', $d)) {
            $mision = $this->entero($d['mision_alcanzada']);
            if ($mision === null || $mision < 1 || $mision > 4) {
                $this->error(400, 'mision_alcanzada debe ser un número entre 1 y 4');
            }
            if ($mision < $actual || $mision > $actual + 1) {
                $this->error(400, 'No podés saltear misiones ni volver a una anterior');
            }
        }

        if ($estado === 'completada') {
            if ($actual < 4) {
                $this->error(400, 'Todavía no llegaste a la misión final');
            }
            $this->modelo->completar($id, $jugadorId);
        } elseif ($mision !== null) {
            $this->modelo->guardarProgreso($id, $jugadorId, $mision);
        } else {
            $this->error(400, 'Enviá mision_alcanzada o estado completada');
        }

        $this->responder(200, [
            'ok'      => true,
            'mensaje' => 'Partida actualizada',
            'partida' => $this->modelo->buscarPorId($id),
        ]);
    }
    // DELETE /api/partidas/:id
    public function eliminar(int $id): void {
        $this->cargarPropia($id);
        $this->modelo->eliminar($id, $this->jugadorLogueado());
        $this->responder(200, ['ok' => true, 'mensaje' => 'Partida eliminada']);
    }

    // Verifica sesión (401), que exista (404) y que sea del jugador logueado (403).
    private function cargarPropia(int $id): array {
        $jugadorId = $this->jugadorLogueado();
        $partida = $this->modelo->buscarPorId($id);

        if (!$partida) {
            $this->error(404, 'Partida no encontrada');
        }
        if ((int) $partida['jugador_id'] !== $jugadorId) {
            $this->error(403, 'Esa partida no es tuya');
        }
        return $partida;
    }
}