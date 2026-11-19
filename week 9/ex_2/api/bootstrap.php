<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function respond(bool $success, array $payload = [], int $status = 200): void
{
    http_response_code($status);
    echo json_encode(array_merge(['success' => $success], $payload), JSON_UNESCAPED_UNICODE);
    exit;
}

function getConnection(): PDO
{
    static $connection = null;
    if ($connection === null) {
        try {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $database = getenv('DB_NAME') ?: 'product_db';
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
            respond(false, ['message' => 'Không thể kết nối MySQL'], 503);
        }
    }
    return $connection;
}

function uploadsDirectory(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
}

function deleteUploadedImage(?string $image): void
{
    if ($image === null || $image === '') {
        return;
    }

    $uploadsDirectory = realpath(uploadsDirectory());
    $path = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . $image);

    if ($uploadsDirectory === false || $path === false || !is_file($path)) {
        return;
    }

    $prefix = rtrim($uploadsDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (str_starts_with($path, $prefix)) {
        @unlink($path);
    }
}
