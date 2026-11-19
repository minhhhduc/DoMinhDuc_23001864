<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

const MAX_IMAGE_SIZE = 2 * 1024 * 1024;

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(false, ['message' => 'Chỉ hỗ trợ phương thức POST'], 405);
}

$productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
if ($productId === false || $productId === null || $productId <= 0) {
    respond(false, ['message' => 'Mã sản phẩm không hợp lệ'], 400);
}

if (!isset($_FILES['image'])) {
    if (empty($_POST) && empty($_FILES)) {
        respond(false, ['message' => 'Dữ liệu gửi lên vượt quá giới hạn của server'], 413);
    }
    respond(false, ['message' => 'Vui lòng chọn ảnh cần tải lên'], 400);
}

$file = $_FILES['image'];
if (is_array($file['name'])) {
    respond(false, ['message' => 'Mỗi sản phẩm chỉ được chọn một ảnh'], 400);
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    $message = match ($file['error']) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ảnh vượt quá dung lượng tối đa 2 MB',
        UPLOAD_ERR_PARTIAL => 'Ảnh chỉ được tải lên một phần, vui lòng thử lại',
        UPLOAD_ERR_NO_FILE => 'Vui lòng chọn ảnh cần tải lên',
        default => 'Không thể tải ảnh lên, vui lòng thử lại',
    };
    respond(false, ['message' => $message], 400);
}

if (!is_uploaded_file($file['tmp_name'])) {
    respond(false, ['message' => 'Tệp tải lên không hợp lệ'], 400);
}

$reportedSize = (int) $file['size'];
$actualSize = filesize($file['tmp_name']);
if ($reportedSize > MAX_IMAGE_SIZE || $actualSize === false || $actualSize > MAX_IMAGE_SIZE) {
    respond(false, ['message' => 'Ảnh vượt quá dung lượng tối đa 2 MB'], 413);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = $finfo !== false ? finfo_file($finfo, $file['tmp_name']) : false;
if ($finfo !== false) {
    finfo_close($finfo);
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
];

if (!is_string($mimeType) || !isset($allowedTypes[$mimeType])) {
    respond(false, ['message' => 'Ảnh phải có định dạng JPG, JPEG hoặc PNG'], 400);
}

$imageInfo = @getimagesize($file['tmp_name']);
$allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
if ($imageInfo === false || !in_array($imageInfo[2], $allowedImageTypes, true)) {
    respond(false, ['message' => 'Tệp tải lên không phải là ảnh JPG, JPEG hoặc PNG hợp lệ'], 400);
}

$pdo = getConnection();
$statement = $pdo->prepare('SELECT id, image FROM products WHERE id = :id');
$statement->execute(['id' => $productId]);
$product = $statement->fetch();

if ($product === false) {
    respond(false, ['message' => "Không tìm thấy sản phẩm có id = $productId"], 404);
}

$uploadsDirectory = uploadsDirectory();
if (!is_dir($uploadsDirectory) && !mkdir($uploadsDirectory, 0775, true) && !is_dir($uploadsDirectory)) {
    respond(false, ['message' => 'Không thể tạo thư mục lưu ảnh'], 500);
}

$extension = $allowedTypes[$mimeType];
$fileName = sprintf('product_%d_%s.%s', $productId, bin2hex(random_bytes(8)), $extension);
$destination = $uploadsDirectory . DIRECTORY_SEPARATOR . $fileName;
$imagePath = 'uploads/' . $fileName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    respond(false, ['message' => 'Không thể lưu ảnh lên server'], 500);
}

try {
    $update = $pdo->prepare('UPDATE products SET image = :image WHERE id = :id');
    $update->execute(['image' => $imagePath, 'id' => $productId]);
} catch (Throwable $exception) {
    @unlink($destination);
    error_log($exception->getMessage());
    respond(false, ['message' => 'Không thể cập nhật đường dẫn ảnh'], 500);
}

deleteUploadedImage($product['image'] ?? null);

respond(true, [
    'message' => 'Upload ảnh thành công',
    'data' => [
        'id' => $productId,
        'image' => $imagePath,
    ],
]);
