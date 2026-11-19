<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

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

function getPaginationParams(): array
{
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if ($page === false) {
        respond(false, ['message' => 'page phải là số nguyên dương'], 400);
    }
    if ($page === null) {
        $page = 1;
    }
    if ($page < 1) {
        respond(false, ['message' => 'page phải là số nguyên dương'], 400);
    }

    $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
    if ($limit === false) {
        respond(false, ['message' => 'limit phải bằng 5'], 400);
    }
    if ($limit === null) {
        $limit = 5;
    }
    if ($limit !== 5) {
        respond(false, ['message' => 'limit phải bằng 5'], 400);
    }

    return [$page, $limit];
}

function productExists(PDO $pdo, int $id): bool
{
    $statement = $pdo->prepare('SELECT id FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    return (bool) $statement->fetch();
}

function getProducts(PDO $pdo): void
{
    [$page, $limit] = getPaginationParams();

    $total = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $totalPages = max(1, (int) ceil($total / $limit));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $limit;
    $statement = $pdo->prepare(
        'SELECT id, name, price, stock, image
         FROM products
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset'
    );
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();

    respond(true, [
        'data' => $statement->fetchAll(),
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => $totalPages,
        ],
    ]);
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
    $statement = $pdo->prepare('SELECT image FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    $product = $statement->fetch();

    if ($product === false) {
        respond(false, ['message' => "Không tìm thấy sản phẩm có id = $id"], 404);
    }

    $delete = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $delete->execute(['id' => $id]);

    deleteUploadedImage($product['image'] ?? null);
    respond(true, ['message' => 'Đã xóa sản phẩm']);
}

$pdo = getConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

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
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    respond(false, ['message' => 'Lỗi cơ sở dữ liệu'], 500);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    respond(false, ['message' => 'Có lỗi xảy ra trên server'], 500);
}
