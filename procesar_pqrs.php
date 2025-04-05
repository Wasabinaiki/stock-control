<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST["tipo"];
    $descripcion = $_POST["descripcion"];
    $id_usuario = $_SESSION["id"];
    $estado = "pendiente";

    $sql = "INSERT INTO pqrs (id_usuario, tipo, descripcion, estado) VALUES (?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "isss", $id_usuario, $tipo, $descripcion, $estado);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "PQRS registrado correctamente.";
            header("location: pqrs.php");
        } else {
            $_SESSION['error_message'] = "Error al registrar el PQRS.";
            header("location: pqrs.php");
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
    exit();
}
?>