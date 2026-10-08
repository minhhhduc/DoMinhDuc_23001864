<?php
include __DIR__ . '/common/bootstrap.php';
$title = 'Add product';
$submitLabel = 'Add product';
$product = ['name' => '', 'price' => '', 'quantity' => '0'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$product, $errors] = validateProduct($_POST);
    if (!validCsrfToken()) {
        $errors[] = 'Invalid session token. Please reload the page.';
    }
    if (!$errors) {
        try {
            addProduct($product['name'], $product['price'], $product['quantity']);
            redirectToList('Product added successfully.');
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $errors[] = 'Unable to add the product. Please try again.';
        }
    }
}
include __DIR__ . '/view/header.php';
include __DIR__ . '/view/product_form.php';
include __DIR__ . '/view/footer.php';
