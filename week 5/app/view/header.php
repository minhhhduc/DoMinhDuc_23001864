<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($title) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Account: <strong><?= escape($_SESSION['username']) ?></strong> | <a href="logout.php">Logout</a></p>
    <?php endif; ?>
    <h1><?= escape($title) ?></h1>
    <?php if (!empty($_SESSION['message'])): ?>
        <p class="success" role="status"><?= escape($_SESSION['message']) ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <ul class="errors" role="alert">
            <?php foreach ($errors as $error): ?>
                <li><?= escape($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
