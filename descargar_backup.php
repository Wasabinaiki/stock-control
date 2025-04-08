<?php
session_start();
require_once "includes/config.php";
require_once "includes/audit_functions.php";
require_once "includes/backup_functions.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id_backup = $_GET["id"];

    registrar_auditoria($_SESSION["id"], "Descarga de backup", "backups", $id_backup);

    if (!descargar_backup($id_backup)) {
        $_SESSION["error_message"] = "Error al descargar el backup. El archivo no existe o no se puede acceder a él.";
        header("location: admin_backup.php");
        exit;
    }
} else {
    header("location: admin_backup.php");
    exit;
}
?>