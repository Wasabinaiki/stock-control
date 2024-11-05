<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'control-stock');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

// Función para verificar el rol del usuario
function hasRole($role) {
    return isset($_SESSION["rol"]) && $_SESSION["rol"] === $role;
}

// Función para redirigir si no está logueado
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("location: login.php");
        exit;
    }
}

// Función para redirigir si no tiene el rol adecuado
function redirectIfNotAdmin() {
    if (!hasRole('admin')) {
        header("location: index.php");
        exit;
    }
}
?>