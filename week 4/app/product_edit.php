<?php
include __DIR__ . '/common/bootstrap.php';
$title = 'Edit product';
$submitLabel = 'Save changes';
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
            [$product, $errors] = validateProduct($_POST);
            if (!validCsrfToken()) {
                $errors[] = 'Invalid session token. Please reload the page.';
            }
            if (!$errors) {
                updateProduct($id, $product['name'], $product['price'], $product['quantity']);
                redirectToList('Product updated successfully.');
            }
        }
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        $errors[] = 'Unable to load or update the product. Please try again.';
    }
}
include __DIR__ . '/view/header.php';
if ($product) {
    include __DIR__ . '/view/product_form.php';
} else {
    echo '<p><a href="product_list.php">Back to product list</a></p>';
}
include __DIR__ . '/view/footer.php';
