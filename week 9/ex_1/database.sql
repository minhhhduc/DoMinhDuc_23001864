CREATE DATABASE IF NOT EXISTS product_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE product_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL CHECK (price > 0),
    stock INT NOT NULL CHECK (stock >= 0),
    image VARCHAR(255) NULL
);

SET @has_image := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'image'
);

SET @add_image_sql := IF(
    @has_image = 0,
    'ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL AFTER stock',
    'DO 0'
);

PREPARE add_image_statement FROM @add_image_sql;
EXECUTE add_image_statement;
DEALLOCATE PREPARE add_image_statement;

INSERT INTO products (name, price, stock)
SELECT seed.name, seed.price, seed.stock
FROM (
    SELECT 'Bút bi xanh' AS name, 5000 AS price, 120 AS stock
    UNION ALL SELECT 'Bút bi đỏ', 5000, 90
    UNION ALL SELECT 'Vở ô ly 200 trang', 15000, 45
    UNION ALL SELECT 'Vở kẻ ngang 120 trang', 12000, 60
    UNION ALL SELECT 'Thước kẻ 20cm', 8000, 30
    UNION ALL SELECT 'Bút chì HB', 6000, 75
    UNION ALL SELECT 'Gọt bút chì', 7000, 25
    UNION ALL SELECT 'Cục tẩy trắng', 4000, 100
    UNION ALL SELECT 'Hộp bút vải', 45000, 12
    UNION ALL SELECT 'Máy tính bỏ túi', 120000, 8
    UNION ALL SELECT 'Balo học sinh', 250000, 6
    UNION ALL SELECT 'Đèn bàn LED', 180000, 10
) AS seed
LEFT JOIN products AS existing ON existing.name = seed.name
WHERE existing.id IS NULL;
