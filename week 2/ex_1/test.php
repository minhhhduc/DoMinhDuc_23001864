<?php
include __DIR__ . '/ShoppingCart.php';

function assertTest($condition, $message) {
    if (!$condition) {
        throw new RuntimeException('Test failed: ' . $message);
    }

    echo 'PASS: ' . $message . '<br>';
}

function testEmptyCart() {
    $cart = new ShoppingCart();

    assertTest($cart->isEmpty(), 'A new cart has no CartItem objects');
    assertTest($cart->calculateTotal() === 0, 'An empty cart has a total of 0');
}

function testShoppingCartManagesCartItems() {
    $cart = new ShoppingCart([
        new CartItem('Keyboard', 50, 2),
        new CartItem('Mouse', 25, 1),
        new CartItem('Headset', 75, 3)
    ]);

    assertTest($cart->getLength() === 3, 'ShoppingCart manages CartItem objects');
    assertTest($cart->getItem(0) instanceof CartItem, 'Cart items are CartItem objects');
}

function testAddCartItem() {
    $cart = new ShoppingCart();
    $cart->addItems(new CartItem('Keyboard', 50, 2));

    assertTest($cart->getLength() === 1, 'addItems adds a CartItem to the cart');
}

function testCalculateTotal() {
    $cart = new ShoppingCart([
        new CartItem('Keyboard', 50, 2),
        new CartItem('Mouse', 25, 1),
        new CartItem('Headset', 75, 3)
    ]);

    assertTest($cart->calculateTotal() === 350, 'calculateTotal gets each item total through CartItem::getTotal');
}

function testInvalidPrice() {
    foreach ([0, -1] as $price) {
        try {
            new CartItem('Keyboard', $price, 1);
            assertTest(false, 'Price less than or equal to 0 is rejected');
        } catch (InvalidArgumentException $error) {
            assertTest($error->getMessage() === 'Product price must be a number greater than 0.', 'Price less than or equal to 0 is rejected');
        }
    }
}

function testInvalidQuantity() {
    foreach ([0, -1] as $quantity) {
        try {
            new CartItem('Keyboard', 10, $quantity);
            assertTest(false, 'Quantity less than or equal to 0 is rejected');
        } catch (InvalidArgumentException $error) {
            assertTest($error->getMessage() === 'Quantity must be a positive integer.', 'Quantity less than or equal to 0 is rejected');
        }
    }
}

function testRemoveMissingItem() {
    $cart = new ShoppingCart([new CartItem('Keyboard', 50, 1)]);

    try {
        $cart->removeItem('Mouse');
        assertTest(false, 'Removing a missing item shows an error');
    } catch (RuntimeException $error) {
        assertTest($error->getMessage() === 'Product not found.', 'Removing a missing item shows an error');
    }
}

try {
    testEmptyCart();
    testShoppingCartManagesCartItems();
    testAddCartItem();
    testCalculateTotal();
    testInvalidPrice();
    testInvalidQuantity();
    testRemoveMissingItem();
} catch (Throwable $error) {
    echo $error->getMessage() . '<br>';
    exit(1);
}
?>