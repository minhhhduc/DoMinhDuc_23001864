<?php
include __DIR__ . '/common/bootstrap.php';
$title = 'Delete product';
$errors = [];
$product = false;
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
if ($id === false) {
    http_response_code(400);
    $errors[] = 'Invalid product ID.';
} else {
    try {
        $product = getProductById($id);
        if (!$product) {
            http_response_code(404);
            $errors[] = 'Product not found.';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validCsrfToken()) {
                $errors[] = 'Invalid session token. Please reload the page.';
            } elseif (deleteProduct($id)) {
                redirectToList('Product deleted successfully.');
            } else {
                http_response_code(404);
                $product = false;
                $errors[] = 'The product no longer exists.';
            }
        }
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        $errors[] = 'Unable to load or delete the product. Please try again.';
    }
}
include __DIR__ . '/view/header.php';
?>
<?php if ($product): ?>
    <p>Are you sure you want to delete the product <strong><?= escape($product['name']) ?></strong> (ID <?= escape($id) ?>)?</p>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
        <button type="submit">Confirm deletion</button>
        <a href="product_list.php">Cancel</a>
    </form>
<?php else: ?>
    <p><a href="product_list.php">Back to product list</a></p>
<?php endif; ?>
<?php include __DIR__ . '/view/footer.php'; ?>
