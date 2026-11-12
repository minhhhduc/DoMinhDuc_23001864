<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
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

function validateInput(array $input): array
{
    $name = trim((string) ($input['name'] ?? ''));
    if ($name === '' || mb_strlen($name) > 100) {
        throw new InvalidArgumentException('Tên sản phẩm phải từ 1 đến 100 ký tự');
    }

    $price = $input['price'] ?? null;
    if (!is_int($price) || $price <= 0) {
        throw new InvalidArgumentException('Đơn giá phải là số nguyên lớn hơn 0');
    }

    $stock = $input['stock'] ?? null;
    if (!is_int($stock) || $stock < 0) {
        throw new InvalidArgumentException('Số lượng tồn phải là số nguyên không âm');
    }

    return [$name, $price, $stock];
}

function readJsonBody(): array
{
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if (!is_array($input)) {
        respond(false, ['message' => 'Dữ liệu gửi lên phải là JSON hợp lệ'], 400);
    }
    return $input;
}

function getIdFromUrl(): int
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === false || $id === null || $id <= 0) {
        respond(false, ['message' => 'id trên URL không hợp lệ'], 400);
    }
    return $id;
}

function productExists(PDO $pdo, int $id): bool
{
    $statement = $pdo->prepare('SELECT id FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    return (bool) $statement->fetch();
}

function getProducts(PDO $pdo): void
{
    $rows = $pdo->query('SELECT id, name, price, stock FROM products ORDER BY id')->fetchAll();
    respond(true, ['data' => $rows]);
}

function addProduct(PDO $pdo, array $input): void
{
    [$name, $price, $stock] = validateInput($input);
    $statement = $pdo->prepare('INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)');
    $statement->execute(['name' => $name, 'price' => $price, 'stock' => $stock]);
    respond(true, ['message' => 'Đã thêm sản phẩm', 'id' => (int) $pdo->lastInsertId()], 201);
}

function updateProduct(PDO $pdo, array $input, int $id): void
{
    if (!productExists($pdo, $id)) {
        respond(false, ['message' => "Không tìm thấy sản phẩm có id = $id"], 404);
    }
    [$name, $price, $stock] = validateInput($input);
    $statement = $pdo->prepare('UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id');
    $statement->execute(['name' => $name, 'price' => $price, 'stock' => $stock, 'id' => $id]);
    respond(true, ['message' => 'Đã cập nhật sản phẩm']);
}

function deleteProduct(PDO $pdo, int $id): void
{
    $statement = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    if ($statement->rowCount() === 0) {
        respond(false, ['message' => "Không tìm thấy sản phẩm có id = $id"], 404);
    }
    respond(true, ['message' => 'Đã xóa sản phẩm']);
}

$pdo = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            getProducts($pdo);
            break;
        case 'POST':
            addProduct($pdo, readJsonBody());
            break;
        case 'PUT':
            updateProduct($pdo, readJsonBody(), getIdFromUrl());
            break;
        case 'DELETE':
            deleteProduct($pdo, getIdFromUrl());
            break;
        default:
            respond(false, ['message' => 'Phương thức không được hỗ trợ'], 405);
    }
} catch (InvalidArgumentException $exception) {
    respond(false, ['message' => $exception->getMessage()], 400);
}
