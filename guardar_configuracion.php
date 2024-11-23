<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$usuario_id = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

if ($usuario_id === null) {
    session_destroy();
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_notificaciones = $_POST["email_notificaciones"];
    $tamanio_texto = $_POST["tamanio_texto"];
    $tema = $_POST["tema"];
    $idioma = $_POST["idioma"];

    $sql = "UPDATE usuarios SET email = ?, tamanio_texto = ?, tema = ?, idioma = ? WHERE id_usuario = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $email_notificaciones, $tamanio_texto, $tema, $idioma, $usuario_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["success_message"] = "Configuración actualizada con éxito.";
        } else {
            $_SESSION["error_message"] = "Hubo un error al actualizar la configuración: " . mysqli_error($link);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION["error_message"] = "Error en la preparación de la consulta: " . mysqli_error($link);
    }
    
    header("location: configuracion.php");
    exit;
}

mysqli_close($link);
?>