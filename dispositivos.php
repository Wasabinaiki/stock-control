<?php
require_once "includes/config.php";
redirectIfNotLoggedIn();

// Procesar la eliminación de dispositivo si se solicita
if(isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])){
    $id = $_GET["id"];
    $sql = "DELETE FROM Dispositivos WHERE id_dispositivo = ? AND id_usuario = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION["id"]);
        if(mysqli_stmt_execute($stmt)){
            header("location: dispositivos.php?msg=delete_success");
            exit();
        } else {
            header("location: dispositivos.php?msg=delete_error");
            exit();
        }
    }
}

// Obtener dispositivos del usuario
$sql = "SELECT * FROM Dispositivos WHERE id_usuario = ?";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Dispositivos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Mis Dispositivos</h2>
        <a href="agregar_dispositivo.php" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Agregar Nuevo Dispositivo</a>
        
        <?php
        if(isset($_GET["msg"])){
            if($_GET["msg"] == "delete_success"){
                echo '<div class="alert alert-success">Dispositivo eliminado con éxito.</div>';
            } elseif($_GET["msg"] == "delete_error"){
                echo '<div class="alert alert-danger">Error al eliminar el dispositivo.</div>';
            }
        }
        ?>
        
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Fecha de Entrega</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['tipo_dispositivo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fecha_entrega']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                        echo "<td>";
                        echo '<a href="editar_dispositivo.php?id='. $row['id_dispositivo'] .'" class="btn btn-primary btn-sm mr-1" title="Editar"><i class="fas fa-edit"></i></a>';
                        echo '<a href="dispositivos.php?action=delete&id='. $row['id_dispositivo'] .'" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm(\'¿Está seguro de que desea eliminar este dispositivo?\')"><i class="fas fa-trash"></i></a>';
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No hay dispositivos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary">Volver al Dashboard</a>
    </div>
</body>
</html>