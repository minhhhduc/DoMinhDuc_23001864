<?php

function getConnection(): PDO
{
    static $connection = null;
    if ($connection === null) {
        try {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $database = getenv('DB_NAME') ?: 'shopping_cart';
            $connection = new PDO(
                "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
                getenv('DB_USER') ?: 'root',
                getenv('DB_PASSWORD') ?: '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                 PDO::ATTR_EMULATE_PREPARES => false]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            http_response_code(503);
            exit('Unable to connect to MySQL. Please check the connection settings and the shopping_cart database.');
        }
    }
    return $connection;
}
