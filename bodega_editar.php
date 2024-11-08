<?php
// bodega_editar.php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador"){
    header("location: login.php");
    exit;
}

$id = $tipo = $marca = $modelo = $fecha_entrega = $estado = $licencias = $procesador = $almacenamiento = $ram = $serial = "";
$id_err = "";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);
    
    $sql = "SELECT * FROM dispositivos WHERE id = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                $tipo = $row["tipo"];
                $marca = $row["marca"];
                $modelo = $row["modelo"];
                $fecha_entrega = $row["fecha_entrega"];
                $estado = $row["estado"];
                $licencias = $row["licencias"];
                $procesador = $row["procesador"];
                $almacenamiento = $row["almacenamiento"];
                $ram = $row["ram"];
                $serial = $row["serial"];
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

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["id"]))){
        $id_err = "Por favor ingrese un ID.";
    } else{
        $id = trim($_POST["id"]);
    }
    
    if(empty($id_err)){
        $sql = "UPDATE dispositivos SET tipo=?, marca=?, modelo=?, fecha_entrega=?, estado=?, licencias=?, procesador=?, almacenamiento=?, ram=?, serial=? WHERE id=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssssssssssi", $param_tipo, $param_marca, $param_modelo, $param_fecha_entrega, $param_estado, $param_licencias, $param_procesador, $param_almacenamiento, $param_ram, $param_serial, $param_id);
            
            $param_tipo = $tipo;
            $param_marca = $marca;
            $param_modelo = $modelo;
            $param_fecha_entrega = $fecha_entrega;
            $param_estado = $estado;
            $param_licencias = $licencias;
            $param_procesador = $procesador;
            $param_almacenamiento = $almacenamiento;
            $param_ram = $ram;
            $param_serial = $serial;
            $param_id = $id;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: bodega.php");
                exit();
            } else{
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
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
    <title>Editar Dispositivo en Bodega</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Editar Dispositivo en Bodega</h2>
        <p>Por favor edite los valores del dispositivo.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Tipo</label>
                <input type="text" name="tipo" class="form-control" value="<?php echo $tipo; ?>">
            </div>    
            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" class="form-control" value="<?php echo $marca; ?>">
            </div>
            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo" class="form-control" value="<?php echo $modelo; ?>">
            </div>
            <div class="form-group">
                <label>Fecha de Entrega</label>
                <input type="date" name="fecha_entrega" class="form-control" value="<?php echo $fecha_entrega; ?>">
            </div>
            <div class="form-group">
                <label>Estado</label>
                <input type="text" name="estado" class="form-control" value="<?php echo $estado; ?>">
            </div>
            <div class="form-group">
                <label>Licencias</label>
                <input type="text" name="licencias" class="form-control" value="<?php echo $licencias; ?>">
            </div>
            <div class="form-group">
                <label>Procesador</label>
                <input type="text" name="procesador" class="form-control" value="<?php echo $procesador; ?>">
            </div>
            <div class="form-group">
                <label>Almacenamiento</label>
                <input type="text" name="almacenamiento" class="form-control" value="<?php echo $almacenamiento; ?>">
            </div>
            <div class="form-group">
                <label>RAM</label>
                <input type="text" name="ram" class="form-control" value="<?php echo $ram; ?>">
            </div>
            <div class="form-group">
                <label>Serial</label>
                <input type="text" name="serial" class="form-control" value="<?php echo $serial; ?>">
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Actualizar">
                <a href="bodega.php" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>    
</body>
</html>