<form method="post">
    <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
    <label for="username">Username</label>
    <input id="username" name="username" maxlength="50" required autocomplete="username" value="<?= escape($username) ?>">
    <label for="password">Password</label>
    <input id="password" name="password" type="password" required autocomplete="<?= $title === 'Register' ? 'new-password' : 'current-password' ?>">
    <button type="submit"><?= escape($title) ?></button>
</form>
