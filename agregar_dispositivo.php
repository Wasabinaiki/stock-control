<?php
require_once "includes/config.php";
redirectIfNotLoggedIn();

$tipo_dispositivo = $marca = $modelo = $fecha_entrega = "";
$tipo_dispositivo_err = $marca_err = $modelo_err = $fecha_entrega_err = "";

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
    
    // Verificar los errores de entrada antes de insertar en la base de datos
    if(empty($tipo_dispositivo_err) && empty($marca_err) && empty($modelo_err) && empty($fecha_entrega_err)){
        
        $sql = "INSERT INTO Dispositivos (id_usuario, tipo_dispositivo, marca, modelo, fecha_entrega, estado) VALUES (?, ?, ?, ?, ?, 'Activo')";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "issss", $param_id_usuario, $param_tipo_dispositivo, $param_marca, $param_modelo, $param_fecha_entrega);
            
            $param_id_usuario = $_SESSION["id"];
            $param_tipo_dispositivo = $tipo_dispositivo;
            $param_marca = $marca;
            $param_modelo = $modelo;
            $param_fecha_entrega = $fecha_entrega;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: dispositivos.php");
                exit();
            } else{
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Dispositivo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Agregar Nuevo Dispositivo</h2>
        <p>Por favor complete este formulario para agregar un nuevo dispositivo.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Tipo de Dispositivo</label>
                <select name="tipo_dispositivo" class="form-control <?php echo (!empty($tipo_dispositivo_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Seleccione un tipo</option>
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
                <input type="submit" class="btn btn-primary" value="Agregar Dispositivo">
                <a href="dispositivos.php" class="btn btn-secondary ml-2">Cancelar</a>
            </div>
        </form>
    </div>    
</body>
</html>