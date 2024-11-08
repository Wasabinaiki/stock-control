<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['id'];

    $sql = "DELETE FROM usuarios WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        if (mysqli_stmt_execute($stmt)) {
            session_destroy();
            header("Location: login.php?mensaje=cuenta_eliminada");
            exit;
        } else {
            $_SESSION['mensaje'] = "Hubo un error al eliminar la cuenta.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensaje'] = "Error en la preparación de la consulta.";
        $_SESSION['tipo_mensaje'] = "danger";
    }

    mysqli_close($link);
    header("Location: configuracion.php");
    exit;
} else {
    header("Location: configuracion.php");
    exit;
}
?>