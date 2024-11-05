<?php
require_once "includes/config.php";
redirectIfNotLoggedIn();

$tipo_dispositivo = $marca = $modelo = $fecha_entrega = $estado = "";
$tipo_dispositivo_err = $marca_err = $modelo_err = $fecha_entrega_err = $estado_err = "";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);
    
    $sql = "SELECT * FROM Dispositivos WHERE id_dispositivo = ? AND id_usuario = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION["id"]);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $tipo_dispositivo = $row["tipo_dispositivo"];
                $marca = $row["marca"];
                $modelo = $row["modelo"];
                $fecha_entrega = $row["fecha_entrega"];
                $estado = $row["estado"];
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
    }
    mysqli_stmt_close($stmt);
} else {
    header("location: error.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validar tipo de dispositivo
    if(empty(trim($_POST["tipo_dispositivo"]))){
        $tipo_dispositivo_err = "Por favor seleccione el tipo de dispositivo.";
    } else{
        $tipo_dispositivo = trim($_POST["tipo_dispositivo"]);
    }
    
    // Validar marca
    if(empty(trim($_POST["marca"]))){
        $marca_err = "Por favor ingrese la marca del dispositivo.";
    } else{
        $marca = trim($_POST["marca"]);
    }
    
    // Validar modelo
    if(empty(trim($_POST["modelo"]))){
        $modelo_err = "Por favor ingrese el modelo del dispositivo.";
    } else{
        $modelo = trim($_POST["modelo"]);
    }
    
    // Validar fecha de entrega
    if(empty(trim($_POST["fecha_entrega"]))){
        $fecha_entrega_err = "Por favor ingrese la fecha de entrega.";
    } else{
        $fecha_entrega = trim($_POST["fecha_entrega"]);
    }
    
    // Validar estado
    if(empty(trim($_POST["estado"]))){
        $estado_err = "Por favor seleccione el estado del dispositivo.";
    } else{
        $estado = trim($_POST["estado"]);
    }
    
    // Verificar los errores de entrada antes de actualizar en la base de datos
    if(empty($tipo_dispositivo_err) && empty($marca_err) && empty($modelo_err) && empty($fecha_entrega_err) && empty($estado_err)){
        $sql = "UPDATE Dispositivos SET tipo_dispositivo = ?, marca = ?, modelo = ?, fecha_entrega = ?, estado = ? WHERE id_dispositivo = ? AND id_usuario = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sssssii", $param_tipo_dispositivo, $param_marca, $param_modelo, $param_fecha_entrega, $param_estado, $param_id, $param_id_usuario);
            
            $param_tipo_dispositivo = $tipo_dispositivo;
            $param_marca = $marca;
            $param_modelo = $modelo;
            $param_fecha_entrega = $fecha_entrega;
            $param_estado = $estado;
            $param_id = $id;
            $param_id_usuario = $_SESSION["id"];
            
            if(mysqli_stmt_execute($stmt)){
                header("location: dispositivos.php");
                exit();
            } else{
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <title>Editar Dispositivo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Editar Dispositivo</h2>
        <p>Por favor edite los valores y envíe para actualizar el dispositivo.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <div class="form-group">
                <label>Tipo de Dispositivo</label>
                <select name="tipo_dispositivo" class="form-control <?php echo (!empty($tipo_dispositivo_err)) ? 'is-invalid' : ''; ?>">
                    <option value="Computador" <?php echo ($tipo_dispositivo == "Computador") ? 'selected' : ''; ?>>Computador</option>
                    <option value="Tablet" <?php echo ($tipo_dispositivo == "Tablet") ? 'selected' : ''; ?>>Tablet</option>
                    <option value="Celular" <?php echo ($tipo_dispositivo == "Celular") ? 'selected' : ''; ?>>Celular</option>
                </select>
                <span class="invalid-feedback"><?php echo $tipo_dispositivo_err; ?></span>
            </div>
            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" class="form-control <?php echo (!empty($marca_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $marca; ?>">
                <span class="invalid-feedback"><?php echo $marca_err; ?></span>
            </div>
            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo" class="form-control <?php echo (!empty($modelo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $modelo; ?>">
                <span class="invalid-feedback"><?php echo $modelo_err; ?></span>
            </div>
            <div class="form-group">
                <label>Fecha de Entrega</label>
                <input type="date" name="fecha_entrega" class="form-control <?php echo (!empty($fecha_entrega_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fecha_entrega; ?>">
                <span class="invalid-feedback"><?php echo $fecha_entrega_err; ?></span>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control <?php echo (!empty($estado_err)) ? 'is-invalid' : ''; ?>">
                    <option value="Activo" <?php echo ($estado == "Activo") ? 'selected' : ''; ?>>Activo</option>
                    <option value="En Reparación" <?php echo ($estado == "En Reparación") ? 'selected' : ''; ?>>En Reparación</option>
                    <option value="Inactivo" <?php echo ($estado == "Inactivo") ? 'selected' : ''; ?>>Inactivo</option>
                </select>
                <span class="invalid-feedback"><?php echo $estado_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Guardar Cambios">
                <a href="dispositivos.php" class="btn btn-secondary ml-2">Cancelar</a>
            </div>
        </form>
    </div>    
</body>
</html>