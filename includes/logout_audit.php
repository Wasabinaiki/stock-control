<?php
if (file_exists("includes/audit_functions.php")) {
    require_once "includes/audit_functions.php";

    if (isset($_SESSION["id"])) {
        registrar_cierre_sesion($_SESSION["id"]);
    }
}
