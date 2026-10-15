<form method="post">
    <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
    <label for="name">Product name</label>
    <input id="name" name="name" type="text" maxlength="100" required value="<?= escape($product['name']) ?>">

    <label for="price">Price</label>
    <input id="price" name="price" type="number" min="0.01" max="99999999.99" step="0.01" required value="<?= escape($product['price']) ?>"
           oninvalid="this.setCustomValidity(this.validity.rangeUnderflow ? 'Price must be greater than 0.' : '')"
           oninput="this.setCustomValidity('')">

    <label for="quantity">Quantity</label>
    <input id="quantity" name="quantity" type="number" min="0" max="2147483647" step="1" required value="<?= escape($product['quantity']) ?>">

    <button type="submit"><?= escape($submitLabel) ?></button>
    <a href="product_list.php">Cancel</a>
</form>
