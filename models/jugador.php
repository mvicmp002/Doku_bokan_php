<?php
// models/Jugador.php
// Consultas a la tabla "jugadores". No valida nada: eso lo hace el controlador.

require_once __DIR__ . '/../config/database.php';

class Jugador {
    private PDO $db;

    // Campos que se pueden mostrar (nunca la contraseña)
    private const PUBLICOS = 'id, nombre, apellido, email, mision_actual, partidas_jugadas, mejor_tiempo, creado_en';

    public function __construct() {
        $this->db = Database::getConexion();
    }

    public function crear(string $nombre, string $apellido, string $email, string $passwordHash): int {
        $st = $this->db->prepare(
            'INSERT INTO jugadores (nombre, apellido, email, password)
             VALUES (:nombre, :apellido, :email, :password)'
        );
        $st->execute([
            ':nombre'   => $nombre,
            ':apellido' => $apellido,
            ':email'    => $email,
            ':password' => $passwordHash,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function buscarPorId(int $id): ?array {
        $st = $this->db->prepare('SELECT ' . self::PUBLICOS . ' FROM jugadores WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    // Incluye la contraseña hasheada: solo se usa para el login.
    public function buscarPorEmail(string $email): ?array {
        $st = $this->db->prepare('SELECT * FROM jugadores WHERE email = :email');
        $st->execute([':email' => $email]);
        return $st->fetch() ?: null;
    }

    public function emailExiste(string $email, ?int $excluirId = null): bool {
        $st = $this->db->prepare('SELECT 1 FROM jugadores WHERE email = :email AND id <> :id');
        $st->execute([':email' => $email, ':id' => $excluirId ?? 0]);
        return (bool) $st->fetchColumn();
    }

    public function actualizar(int $id, array $cambios): void {
        $permitidos = ['nombre', 'apellido', 'email', 'password'];
        $sets = [];
        $params = [':id' => $id];

        foreach ($cambios as $campo => $valor) {
            if (!in_array($campo, $permitidos, true)) {
                continue;
            }
            $sets[] = "$campo = :$campo";
            $params[":$campo"] = $valor;
        }

        if (!$sets) {
            return;
        }

        $st = $this->db->prepare('UPDATE jugadores SET ' . implode(', ', $sets) . ' WHERE id = :id');
        $st->execute($params);
    }

    public function eliminar(int $id): void {
        // Sus partidas se borran solas (ON DELETE CASCADE)
        $st = $this->db->prepare('DELETE FROM jugadores WHERE id = :id');
        $st->execute([':id' => $id]);
    }
}