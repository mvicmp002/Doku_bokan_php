<?php
// models/Partida.php
// Consultas a la tabla "partidas". Mantiene sincronizados los datos del jugador
// (misión actual, partidas jugadas y mejor tiempo).

require_once __DIR__ . '/../config/database.php';

class Partida {
    public const MAX_PARTIDAS = 3;

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConexion();
    }

    // Ranking global: los mejores tiempos de partidas completadas.
    public function ranking(int $limite): array {
        $st = $this->db->prepare(
            "SELECT p.id, CONCAT(j.nombre, ' ', LEFT(j.apellido, 1), '.') AS jugador,
                    p.tiempo_segundos, p.creado_en
             FROM partidas p
             JOIN jugadores j ON j.id = p.jugador_id
             WHERE p.estado = 'completada' AND p.tiempo_segundos IS NOT NULL
             ORDER BY p.tiempo_segundos ASC, p.id ASC
             LIMIT :limite"
        );
        $st->bindValue(':limite', $limite, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function buscarPorId(int $id): ?array {
        $st = $this->db->prepare('SELECT * FROM partidas WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    // Todas las partidas de un jugador (hasta 3), ordenadas por espacio.
    public function deJugador(int $jugadorId): array {
        $st = $this->db->prepare('SELECT * FROM partidas WHERE jugador_id = :j ORDER BY slot ASC');
        $st->execute([':j' => $jugadorId]);
        return $st->fetchAll();
    }

    public function enCursoDe(int $jugadorId): ?array {
        $st = $this->db->prepare(
            "SELECT * FROM partidas WHERE jugador_id = :j AND estado = 'en_curso' ORDER BY actualizado_en DESC, id DESC LIMIT 1"
        );
        $st->execute([':j' => $jugadorId]);
        return $st->fetch() ?: null;
    }

    // Empieza una partida nueva en la misión 1.
    // Devuelve null si el espacio pedido está ocupado o si ya hay 3 partidas.
    public function crear(int $jugadorId, ?int $slot = null): ?int {
        $this->db->beginTransaction();
        try {
            // Bloquea la fila del jugador: si llegan dos pedidos a la vez, se atienden de a uno
            $st = $this->db->prepare('SELECT id FROM jugadores WHERE id = :j FOR UPDATE');
            $st->execute([':j' => $jugadorId]);

            $st = $this->db->prepare('SELECT slot FROM partidas WHERE jugador_id = :j');
            $st->execute([':j' => $jugadorId]);
            $ocupados = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));

            if ($slot === null) {
                $libres = array_diff(range(1, self::MAX_PARTIDAS), $ocupados);
                $slot = $libres ? min($libres) : null;
            }

            if ($slot === null || in_array($slot, $ocupados, true)) {
                $this->db->rollBack();
                return null;
            }

            $st = $this->db->prepare('INSERT INTO partidas (jugador_id, slot) VALUES (:j, :s)');
            $st->execute([':j' => $jugadorId, ':s' => $slot]);
            $id = (int) $this->db->lastInsertId();

            $st = $this->db->prepare(
                'UPDATE jugadores SET mision_actual = 1, partidas_jugadas = partidas_jugadas + 1 WHERE id = :j'
            );
            $st->execute([':j' => $jugadorId]);

            $this->db->commit();
            return $id;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function guardarProgreso(int $id, int $jugadorId, int $mision): void {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare('UPDATE partidas SET mision_alcanzada = :m WHERE id = :id');
            $st->execute([':m' => $mision, ':id' => $id]);

            $st = $this->db->prepare('UPDATE jugadores SET mision_actual = :m WHERE id = :j');
            $st->execute([':m' => $mision, ':j' => $jugadorId]);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

      // Termina la partida: el tiempo es lo que pasó desde que se creó hasta ahora.
    public function completar(int $id, int $jugadorId): void {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare(
                "UPDATE partidas
                 SET estado = 'completada', mision_alcanzada = 4,
                     tiempo_segundos = GREATEST(TIMESTAMPDIFF(SECOND, creado_en, NOW()), 1)
                 WHERE id = :id"
            );
            $st->execute([':id' => $id]);

            $st = $this->db->prepare('SELECT tiempo_segundos FROM partidas WHERE id = :id');
            $st->execute([':id' => $id]);
            $tiempo = (int) $st->fetchColumn();

            $st = $this->db->prepare(
                'UPDATE jugadores
                 SET mejor_tiempo = LEAST(COALESCE(mejor_tiempo, :t1), :t2)
                 WHERE id = :j'
            );
            $st->bindValue(':t1', $tiempo, PDO::PARAM_INT);
            $st->bindValue(':t2', $tiempo, PDO::PARAM_INT);
            $st->bindValue(':j', $jugadorId, PDO::PARAM_INT);
            $st->execute();

            $this->recalcularMisionActual($jugadorId);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function eliminar(int $id, int $jugadorId): void {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare('SELECT estado FROM partidas WHERE id = :id');
            $st->execute([':id' => $id]);
            $estado = $st->fetchColumn();

            $st = $this->db->prepare('DELETE FROM partidas WHERE id = :id');
            $st->execute([':id' => $id]);

            // Si era una partida a medias, ya no cuenta como jugada
            if ($estado === 'en_curso') {
                $st = $this->db->prepare(
                    'UPDATE jugadores
                     SET partidas_jugadas = CASE WHEN partidas_jugadas > 0 THEN partidas_jugadas - 1 ELSE 0 END
                     WHERE id = :j'
                );
                $st->execute([':j' => $jugadorId]);
            }

            // El mejor tiempo se recalcula con las partidas que quedan
            $st = $this->db->prepare(
                "UPDATE jugadores
                 SET mejor_tiempo = (
                     SELECT MIN(tiempo_segundos) FROM partidas
                     WHERE jugador_id = :j1 AND estado = 'completada'
                 )
                 WHERE id = :j2"
            );
            $st->execute([':j1' => $jugadorId, ':j2' => $jugadorId]);

            $this->recalcularMisionActual($jugadorId);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // La misión actual del jugador es la de su partida en curso más reciente (0 si no tiene).
    private function recalcularMisionActual(int $jugadorId): void {
        $st = $this->db->prepare(
            "UPDATE jugadores
             SET mision_actual = COALESCE((
                 SELECT mision_alcanzada FROM partidas
                 WHERE jugador_id = :j1 AND estado = 'en_curso'
                 ORDER BY actualizado_en DESC, id DESC LIMIT 1
             ), 0)
             WHERE id = :j2"
        );
        $st->execute([':j1' => $jugadorId, ':j2' => $jugadorId]);
    }
}