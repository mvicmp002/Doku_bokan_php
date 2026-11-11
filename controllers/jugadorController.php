<?php
// controllers/JugadorController.php
// Valida los datos que manda el juego y coordina con el modelo.

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Jugador.php';
require_once __DIR__ . '/../models/Partida.php';

class JugadorController extends Controller {
    private Jugador $modelo;

    public function __construct() {
        $this->modelo = new Jugador();
    }

    // POST /api/jugadores  → registro
    public function registrar(): void {
        $d = $this->leerJson();

        $campos = [
            'nombre'   => $this->texto($d, 'nombre') ?? '',
            'apellido' => $this->texto($d, 'apellido') ?? '',
            'email'    => strtolower($this->texto($d, 'email') ?? ''),
            'password' => $this->texto($d, 'password', false) ?? '',
        ];

        $errores = [];
        foreach ($campos as $campo => $valor) {
            $e = $this->validarCampo($campo, $valor);
            if ($e) {
                $errores[$campo] = $e;
            }
        }
        if ($errores) {
            $this->error(400, 'Hay datos inválidos o faltantes', ['detalles' => $errores]);
        }

        if ($this->modelo->emailExiste($campos['email'])) {
            $this->error(409, 'Ya existe una cuenta con ese correo');
        }

        $id = $this->modelo->crear(
            $campos['nombre'],
            $campos['apellido'],
            $campos['email'],
            password_hash($campos['password'], PASSWORD_DEFAULT)
        );

        $jugador = $this->modelo->buscarPorId($id);
        $this->iniciarSesion($jugador); // después de registrarse queda con la sesión iniciada

        $this->responder(201, ['ok' => true, 'mensaje' => 'Jugador registrado', 'jugador' => $jugador]);
    }

    // POST /api/login  → inicio de sesión
    public function login(): void {
        $d = $this->leerJson();
        $email    = strtolower($this->texto($d, 'email') ?? '');
        $password = $this->texto($d, 'password', false) ?? '';

        if ($email === '' || $password === '') {
            $this->error(400, 'El correo y la contraseña son obligatorios');
        }

        $jugador = $this->modelo->buscarPorEmail($email);

        if (!$jugador || !password_verify($password, $jugador['password'])) {
            $this->error(401, 'Correo o contraseña incorrectos');
        }

        $this->iniciarSesion($jugador);
        $this->responder(200, [
            'ok'      => true,
            'mensaje' => 'Sesión iniciada',
            'jugador' => $this->modelo->buscarPorId((int) $jugador['id']),
        ]);
    }

    // GET /api/jugadores/:id  → perfil (misión, partidas y mejor tiempo)
    public function mostrar(int $id): void {
        $jugador = $this->cargarPropio($id);
        $jugador['partida_en_curso'] = (new Partida())->enCursoDe($id);
        $this->responder(200, ['ok' => true, 'jugador' => $jugador]);
    }

    // PUT/PATCH /api/jugadores/:id  → actualizar datos
    public function actualizar(int $id): void {
        $this->cargarPropio($id);
        $d = $this->leerJson();

        $cambios = [];
        $errores = [];

        foreach (['nombre', 'apellido', 'email', 'password'] as $campo) {
            $valor = $this->texto($d, $campo, $campo !== 'password');
            if ($valor === null) {
                continue; // no lo mandó, no se toca
            }
            if ($campo === 'email') {
                $valor = strtolower($valor);
            }
            $e = $this->validarCampo($campo, $valor);
            if ($e) {
                $errores[$campo] = $e;
                continue;
            }
            $cambios[$campo] = $valor;
        }

        if ($errores) {
            $this->error(400, 'Hay datos inválidos', ['detalles' => $errores]);
        }
        if (!$cambios) {
            $this->error(400, 'No enviaste ningún campo para actualizar');
        }
        if (isset($cambios['email']) && $this->modelo->emailExiste($cambios['email'], $id)) {
            $this->error(409, 'Ya existe una cuenta con ese correo');
        }
        if (isset($cambios['password'])) {
            $cambios['password'] = password_hash($cambios['password'], PASSWORD_DEFAULT);
        }

        $this->modelo->actualizar($id, $cambios);

        if (isset($cambios['nombre'])) {
            $_SESSION['nombre'] = $cambios['nombre'];
        }

        $this->responder(200, [
            'ok'      => true,
            'mensaje' => 'Datos actualizados',
            'jugador' => $this->modelo->buscarPorId($id),
        ]);
    }

    // DELETE /api/jugadores/:id  → borrar cuenta
    public function eliminar(int $id): void {
        $this->cargarPropio($id);
        $this->modelo->eliminar($id);

        $_SESSION = [];
        session_destroy();

        $this->responder(200, ['ok' => true, 'mensaje' => 'Cuenta eliminada']);
    }

    // ---------- Auxiliares ----------

    // Verifica sesión (401), que exista (404) y que sea de quien pregunta (403).
    private function cargarPropio(int $id): array {
        $sesionId = $this->jugadorLogueado();
        $jugador = $this->modelo->buscarPorId($id);

        if (!$jugador) {
            $this->error(404, 'Jugador no encontrado');
        }
        if ($sesionId !== $id) {
            $this->error(403, 'No podés acceder a los datos de otro jugador');
        }
        return $jugador;
    }

    // Devuelve el mensaje de error del campo, o null si está bien.
    private function validarCampo(string $campo, string $valor): ?string {
        switch ($campo) {
            case 'nombre':
            case 'apellido':
                if ($valor === '' || mb_strlen($valor) > 60) {
                    return 'Es obligatorio (máximo 60 caracteres)';
                }
                return null;
            case 'email':
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL) || mb_strlen($valor) > 120) {
                    return 'El correo no tiene un formato válido';
                }
                return null;
            case 'password':
                if (strlen($valor) < 6 || strlen($valor) > 72) {
                    return 'La contraseña debe tener entre 6 y 72 caracteres';
                }
                return null;
        }
        return null;
    }
}