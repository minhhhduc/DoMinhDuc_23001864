<?php
require __DIR__ . '/common/bootstrap.php';
if (isset($_SESSION['user_id'])) {
    header('Location: product_list.php');
    exit;
}
$title = 'Login';
$errors = [];
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if (!validCsrfToken()) {
        $errors[] = 'Invalid session token. Please reload the page.';
    }
    if ($username === '' || $password === '') {
        $errors[] = 'Please enter your username and password.';
    }
    if (!$errors) {
        try {
            $user = getUserByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                header('Location: product_list.php');
                exit;
            }
            $errors[] = 'Invalid username or password.';
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $errors[] = 'Unable to log in. Please try again.';
        }
    }
}
include __DIR__ . '/view/header.php';
include __DIR__ . '/view/user_form.php';
?>
<p>Don't have an account? <a href="register.php">Register</a></p>
<?php include __DIR__ . '/view/footer.php'; ?>
