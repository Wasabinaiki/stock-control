<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'control-stock');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

function hasRole($role)
{
    return isset($_SESSION["rol"]) && $_SESSION["rol"] === $role;
}

function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        header("location: login.php");
        exit;
    }
}

function redirectIfNotAdmin()
{
    if (!hasRole('admin')) {
        header("location: index.php");
        exit;
    }
}
?>