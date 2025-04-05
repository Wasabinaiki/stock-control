<?php
require_once "includes/config.php";

$username = "wng_dngstruque";

$sql = "UPDATE usuarios SET rol = 'administrador' WHERE username = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    if (mysqli_stmt_execute($stmt)) {
        echo "Usuario $username actualizado a administrador con éxito.";
    } else {
        echo "Error al actualizar el rol del usuario.";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error en la preparación de la consulta.";
}

mysqli_close($link);
?>