<?php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador"){
    header("location: login.php");
    exit;
}

$id = $usuario_id = $direccion_destino = $estado_envio = $fecha_salida = $fecha_llegada = "";
$id_err = "";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);
    
    $sql = "SELECT * FROM envios WHERE id_envio = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                $usuario_id = $row["usuario_id"];
                $direccion_destino = $row["direccion_destino"];
                $estado_envio = $row["estado_envio"];
                $fecha_salida = $row["fecha_salida"];
                $fecha_llegada = $row["fecha_llegada"];
            } else{
                header("location: error.php");
                exit();
            }
        } else{
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
    }
    mysqli_stmt_close($stmt);
} else{
    header("location: error.php");
    exit();
}

// Obtener lista de usuarios para el select
$sql_usuarios = "SELECT id_usuario, username FROM usuarios";
$result_usuarios = mysqli_query($link, $sql_usuarios);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["id"]))){
        $id_err = "Por favor ingrese un ID.";
    } else{
        $id = trim($_POST["id"]);
    }
    
    if(empty($id_err)){
        $sql = "UPDATE envios SET usuario_id=?, direccion_destino=?, estado_envio=?, fecha_salida=?, fecha_llegada=? WHERE id_envio=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "issssi", $param_usuario_id, $param_direccion_destino, $param_estado_envio, $param_fecha_salida, $param_fecha_llegada, $param_id);
            
            $param_usuario_id = $_POST["usuario_id"];
            $param_direccion_destino = $_POST["direccion_destino"];
            $param_estado_envio = $_POST["estado_envio"];
            $param_fecha_salida = !empty($_POST["fecha_salida"]) ? $_POST["fecha_salida"] : NULL;
            $param_fecha_llegada = !empty($_POST["fecha_llegada"]) ? $_POST["fecha_llegada"] : NULL;
            $param_id = $id;
            
            if(mysqli_stmt_execute($stmt)){
                $_SESSION["success_message"] = "Envío actualizado exitosamente.";
                header("location: admin_envios.php");
                exit();
            } else{
                $_SESSION["error_message"] = "Error al actualizar el envío: " . mysqli_error($link);
                header("location: admin_envios.php");
                exit();
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Envío</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #764ba2;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .btn-default {
            background-color: #6c757d;
            color: white;
            margin-left: 10px;
        }
        .btn-default:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><i class="fas fa-edit me-2"></i>Editar Envío</h2>
        <p>Por favor edite los valores del envío.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <div class="form-group">
                <label>ID del Envío</label>
                <input type="text" class="form-control" value="<?php echo $id; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <select name="usuario_id" class="form-control">
                    <?php
                    if (mysqli_num_rows($result_usuarios) > 0) {
                        while($row_usuario = mysqli_fetch_assoc($result_usuarios)) {
                            $selected = ($row_usuario['id_usuario'] == $usuario_id) ? 'selected' : '';
                            echo "<option value='" . $row_usuario['id_usuario'] . "' " . $selected . ">" . htmlspecialchars($row_usuario['username']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dirección Destino</label>
                <input type="text" name="direccion_destino" class="form-control" value="<?php echo $direccion_destino; ?>">
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado_envio" class="form-control">
                    <option value="En Proceso" <?php echo ($estado_envio == "En Proceso") ? "selected" : ""; ?>>En Proceso</option>
                    <option value="Completado" <?php echo ($estado_envio == "Completado") ? "selected" : ""; ?>>Completado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha de Salida</label>
                <input type="date" name="fecha_salida" class="form-control" value="<?php echo $fecha_salida; ?>">
            </div>
            <div class="form-group">
                <label>Fecha de Llegada</label>
                <input type="date" name="fecha_llegada" class="form-control" value="<?php echo $fecha_llegada; ?>">
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Actualizar">
                <a href="admin_envios.php" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

