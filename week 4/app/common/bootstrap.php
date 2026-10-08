<?php

session_start();
include __DIR__ . '/functions.php';
include __DIR__ . '/../model/product.php';
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
