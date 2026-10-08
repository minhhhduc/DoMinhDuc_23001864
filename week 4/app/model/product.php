<?php

include __DIR__ . '/../common/dbConnect.php';

function getAllProducts(): array
{
    return getConnection()->query('SELECT id, name, price, quantity FROM products ORDER BY id')->fetchAll();
}

function getProductById($id)
{
    $statement = getConnection()->prepare('SELECT id, name, price, quantity FROM products WHERE id = ?');
    $statement->execute([$id]);
    return $statement->fetch();
}

function addProduct($name, $price, $quantity): bool
{
    $statement = getConnection()->prepare('INSERT INTO products (name, price, quantity) VALUES (?, ?, ?)');
    return $statement->execute([$name, $price, $quantity]);
}

function updateProduct($id, $name, $price, $quantity): bool
{
    $statement = getConnection()->prepare('UPDATE products SET name = ?, price = ?, quantity = ? WHERE id = ?');
    return $statement->execute([$name, $price, $quantity, $id]);
}

function deleteProduct($id): bool
{
    $statement = getConnection()->prepare('DELETE FROM products WHERE id = ?');
    $statement->execute([$id]);
    return $statement->rowCount() > 0;
}
