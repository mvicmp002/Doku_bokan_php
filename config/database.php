<?php
// config/database.php
// Única clase que sabe cómo conectarse a MySQL.

require_once __DIR__ . '/env.php';

class Database {
    private static ?PDO $conexion = null;

    public static function getConexion(): PDO {
        // Si ya hay una conexión abierta, se reutiliza
        if (self::$conexion === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                env('DB_HOST'),
                env('DB_PORT'),
                env('DB_NAME')
            );

            self::$conexion = new PDO($dsn, env('DB_USER'), env('DB_PASS'), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$conexion;
    }
}