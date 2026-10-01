USE shopping_cart;

-- 1. Insert at least five products.
INSERT INTO cart_items (name, price, quantity) VALUES
    ('Keyboard', 250000, 2),
    ('Mouse', 120000, 6),
    ('Headphones', 350000, 3),
    ('USB', 80000, 10),
    ('Mouse pad', 50000, 4),
    ('Monitor', 2500000, 1);

-- 2. Show all products.
SELECT * FROM cart_items;

-- 3. Show products priced above 100000.
SELECT * FROM cart_items WHERE price > 100000;

-- 4. Show products with a quantity above 5.
SELECT * FROM cart_items WHERE quantity > 5;

-- 5. Sort products by price in descending order.
SELECT * FROM cart_items ORDER BY price DESC;

-- 6. Update a product's price.
UPDATE cart_items SET price = 270000 WHERE id = 1;

-- 7. Update a product's quantity.
UPDATE cart_items SET quantity = 8 WHERE id = 2;

-- 8. Delete a product.
DELETE FROM cart_items WHERE id = 5;

-- 9. Show each product's name, price, quantity, and line total.
SELECT name, price, quantity, price * quantity AS line_total
FROM cart_items;

-- 10. Calculate the cart total.
SELECT SUM(price * quantity) AS cart_total
FROM cart_items;
