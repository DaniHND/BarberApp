<?php
class Database {

    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function connect(): PDO {
        if (self::$instance === null) {
            $host    = 'localhost';
            $dbname  = 'barberapp';
            $user    = 'root';
            $pass    = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                http_response_code(500);
                die('Error de conexión a la base de datos. Verifica que MySQL esté activo y que la base de datos "barberapp" exista.');
            }
        }
        return self::$instance;
    }
}
