<?php
// agregar_dispositivo.php
session_start();
require_once "includes/config.php";

// Verificar si el usuario está logueado
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$tipo = $marca = $modelo = $fecha_entrega = $estado = "";
$tipo_err = $marca_err = $modelo_err = $fecha_entrega_err = $estado_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validar tipo
    if(empty(trim($_POST["tipo"]))){
        $tipo_err = "Por favor seleccione un tipo de dispositivo.";
    } else{
        $tipo = trim($_POST["tipo"]);
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
    
    // Verificar los errores de entrada antes de insertar en la base de datos
    if(empty($tipo_err) && empty($marca_err) && empty($modelo_err) && empty($fecha_entrega_err) && empty($estado_err)){
        $sql = "INSERT INTO dispositivos (id_usuario, tipo, marca, modelo, fecha_entrega, estado) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "isssss", $param_id_usuario, $param_tipo, $param_marca, $param_modelo, $param_fecha_entrega, $param_estado);
            
            $param_id_usuario = $_SESSION["id"];
            $param_tipo = $tipo;
            $param_marca = $marca;
            $param_modelo = $modelo;
            $param_fecha_entrega = $fecha_entrega;
            $param_estado = $estado;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: dispositivos.php");
                exit();
            } else{
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Dispositivo - Control Stock</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e1e1e 0%, #3d0000 100%);
            min-height: 100vh;
            padding-top: 60px;
        }
        .navbar {
            background-color: rgba(220, 53, 69, 0.9) !important;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            color: white;
        }
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-primary:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">Control Stock</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="agregar_dispositivo.php">Agregar Dispositivo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <h2 class="text-center mb-4">Agregar Nuevo Dispositivo</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Tipo de Dispositivo</label>
                            <select name="tipo" class="form-control <?php echo (!empty($tipo_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Seleccione un tipo</option>
                                <option value="Computadora" <?php echo ($tipo == "Computadora") ? 'selected' : ''; ?>>Computadora</option>
                                <option value="Tablet" <?php echo ($tipo == "Tablet") ? 'selected' : ''; ?>>Tablet</option>
                                <option value="Celular" <?php echo ($tipo == "Celular") ? 'selected' : ''; ?>>Celular</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $tipo_err; ?></span>
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
                                <option value="">Seleccione un estado</option>
                                <option value="Activo" <?php echo ($estado == "Activo") ? 'selected' : ''; ?>>Activo</option>
                                <option value="En reparación" <?php echo ($estado == "En reparación") ? 'selected' : ''; ?>>En reparación</option>
                                <option value="Inactivo" <?php echo ($estado == "Inactivo") ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $estado_err; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-block" value="Agregar Dispositivo">
                            <a href="dispositivos.php" class="btn btn-secondary btn-block">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>