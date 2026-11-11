<?php
// controllers/Controller.php
// Funciones comunes a todos los controladores.

abstract class Controller {

    // Envía una respuesta JSON con el código HTTP indicado y corta la ejecución.
    protected function responder(int $codigo, array $datos = []): void {
        http_response_code($codigo);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function error(int $codigo, string $mensaje, array $extra = []): void {
        $this->responder($codigo, array_merge(['ok' => false, 'error' => $mensaje], $extra));
    }

    // Lee el cuerpo de la petición (JSON) y lo devuelve como array.
    protected function leerJson(): array {
        $crudo = file_get_contents('php://input');
        if (trim($crudo) === '') {
            return [];
        }
        $datos = json_decode($crudo, true);
        if (!is_array($datos)) {
            $this->error(400, 'El cuerpo de la petición no es un JSON válido');
        }
        return $datos;
    }

    // Devuelve el texto de un campo, o null si el campo no vino.
    protected function texto(array $datos, string $clave, bool $recortar = true): ?string {
        if (!array_key_exists($clave, $datos)) {
            return null;
        }
        if (!is_string($datos[$clave])) {
            return '';
        }
        return $recortar ? trim($datos[$clave]) : $datos[$clave];
    }

    // Convierte a número entero, o devuelve null si no es un entero válido.
    protected function entero(mixed $valor): ?int {
        $r = filter_var($valor, FILTER_VALIDATE_INT);
        return $r === false ? null : $r;
    }

    // Devuelve el id del jugador logueado, o responde 401 si no hay sesión.
    protected function jugadorLogueado(): int {
        if (!estaLogueado()) {
            $this->error(401, 'Tenés que iniciar sesión');
        }
        return (int) $_SESSION['jugador_id'];
    }

    // Guarda al jugador en la sesión (queda "logueado").
    protected function iniciarSesion(array $jugador): void {
        session_regenerate_id(true);
        $_SESSION['jugador_id'] = (int) $jugador['id'];
        $_SESSION['nombre']     = $jugador['nombre'];
    }
}