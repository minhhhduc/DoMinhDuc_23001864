<?php

function escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function validateProduct(array $input): array
{
    $errors = [];
    $name = is_string($input['name'] ?? null) ? trim($input['name']) : '';
    $price = is_string($input['price'] ?? null) ? trim($input['price']) : '';
    $quantity = is_string($input['quantity'] ?? null) ? trim($input['quantity']) : '';

    if ($name === '' || mb_strlen($name, 'UTF-8') > 100) {
        $errors[] = 'Product name is required and must not exceed 100 characters.';
    }
    if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $price) || (float) $price <= 0) {
        $errors[] = 'Price must be greater than 0, at most 99999999.99, and have no more than 2 decimal places.';
    }
    if (filter_var($quantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 2147483647]]) === false) {
        $errors[] = 'Quantity must be an integer between 0 and 2147483647.';
    }
    return [['name' => $name, 'price' => $price, 'quantity' => $quantity], $errors];
}

function validCsrfToken(): bool
{
    return is_string($_POST['csrf_token'] ?? null)
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function redirectToList(string $message): void
{
    $_SESSION['message'] = $message;
    header('Location: product_list.php');
    exit;
}
