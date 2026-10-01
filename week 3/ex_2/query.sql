USE shopping_cart;

-- 1. Thêm ít nhất 5 bộ phim.
INSERT INTO movies (title, price, total_seats, available_seats) VALUES
    ('Hành Trình Cuối Cùng', 120000, 120, 35),
    ('Đại Dương Sao', 90000, 100, 80),
    ('Ánh Đèn Thành Phố', 150000, 150, 50),
    ('Thế Giới Bí Ẩn', 110000, 90, 20),
    ('Ký Ức Mùa Hè', 80000, 80, 65),
    ('Bên Kia Núi', 130000, 110, 40);

-- 2. Hiển thị toàn bộ danh sách phim.
SELECT * FROM movies;

-- 3. Hiển thị phim có giá vé lớn hơn 100000.
SELECT * FROM movies WHERE price > 100000;

-- 4. Hiển thị phim còn nhiều hơn 50 ghế.
SELECT * FROM movies WHERE available_seats > 50;

-- 5. Sắp xếp phim theo giá vé giảm dần.
SELECT * FROM movies ORDER BY price DESC;

-- 6. Cập nhật số ghế còn lại của một phim.
UPDATE movies SET available_seats = 30 WHERE id = 1;

-- 7. Xóa một phim.
DELETE FROM movies WHERE id = 6;

-- 8. Hiển thị số vé đã bán của từng phim.
SELECT title, total_seats - available_seats AS tickets_sold
FROM movies;

-- 9. Tính doanh thu của từng phim.
SELECT title, (total_seats - available_seats) * price AS movie_revenue
FROM movies;

-- 10. Tính tổng doanh thu của tất cả các phim.
SELECT SUM((total_seats - available_seats) * price) AS total_revenue
FROM movies;

-- 11. Tìm phim có số vé bán ra nhiều nhất.
SELECT title, total_seats - available_seats AS tickets_sold
FROM movies
WHERE total_seats - available_seats = (
    SELECT MAX(total_seats - available_seats)
    FROM movies
);
