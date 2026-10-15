<?php

require_once __DIR__ . '/../common/dbConnect.php';

function getUserByUsername($username)
{
    $statement = getConnection()->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $statement->execute([$username]);
    return $statement->fetch();
}

function addUser($username, $password): bool
{
    $statement = getConnection()->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    return $statement->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
}
