<?php
include __DIR__ . '/common/bootstrap.php';
$title = 'Product list';
$errors = [];
$products = [];
try {
    $products = getAllProducts();
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    $errors[] = 'Unable to load products. Please check the products table in MySQL.';
}
include __DIR__ . '/view/header.php';
?>
<p><a href="product_add.php">Add product</a></p>
<div class="table-container">
<table>
    <thead><tr><th>ID</th><th>Product name</th><th>Price</th><th>Quantity</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= escape($product['id']) ?></td>
            <td><?= escape($product['name']) ?></td>
            <td><?= number_format((float) $product['price'], 2, '.', ',') ?></td>
            <td><?= escape($product['quantity']) ?></td>
            <td>
                <a href="product_edit.php?id=<?= (int) $product['id'] ?>">Edit</a>
                <a href="product_delete.php?id=<?= (int) $product['id'] ?>">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$products && !$errors): ?>
        <tr><td colspan="5">No products available.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
