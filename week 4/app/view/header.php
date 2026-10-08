<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="/web/week%204/app/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($title) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main>
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
