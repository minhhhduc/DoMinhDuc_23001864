<?php
require __DIR__ . '/common/bootstrap.php';
if (isset($_SESSION['user_id'])) {
    header('Location: product_list.php');
    exit;
}
$title = 'Register';
$errors = [];
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if (!validCsrfToken()) {
        $errors[] = 'Invalid session token. Please reload the page.';
    }
    if ($username === '' || mb_strlen($username, 'UTF-8') > 50) {
        $errors[] = 'Username is required and must not exceed 50 characters.';
    }
    if (trim($password) === '' || strlen($password) > 72) {
        $errors[] = 'Password is required and must not exceed 72 bytes.';
    }
    if (!$errors) {
        try {
            if (getUserByUsername($username)) {
                $errors[] = 'Username already exists.';
            } else {
                addUser($username, $password);
                $_SESSION['message'] = 'Registration successful. Please log in.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $errors[] = ($exception->errorInfo[1] ?? null) === 1062
                ? 'Username already exists.'
                : 'Unable to register. Please try again.';
        }
    }
}
include __DIR__ . '/view/header.php';
include __DIR__ . '/view/user_form.php';
?>
<p>Already have an account? <a href="login.php">Login</a></p>
<?php include __DIR__ . '/view/footer.php'; ?>
