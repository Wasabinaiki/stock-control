<?php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if(isset($_POST["id_dispositivo"]) && !empty($_POST["id_dispositivo"])){
    $sql = "DELETE FROM dispositivos WHERE id_dispositivo = ? AND id_usuario = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "ii", $param_id_dispositivo, $param_id_usuario);
        
        $param_id_dispositivo = trim($_POST["id_dispositivo"]);
        $param_id_usuario = $_SESSION["id"];
        
        if(mysqli_stmt_execute($stmt)){
            header("location: dispositivos.php?tipo=" . $_POST["tipo"]);
            exit();
        } else{
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
    }
     
    mysqli_stmt_close($stmt);
    
    mysqli_close($link);
} else{
    if(empty(trim($_GET["id"]))){
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Dispositivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            border: none;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #ee5253 0%, #ff6b6b 100%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-laptop me-2"></i>Control de Stock</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="wrapper">
            <h2 class="mb-4"><i class="fas fa-trash-alt me-2"></i>Eliminar Dispositivo</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="alert alert-danger">
                    <input type="hidden" name="id_dispositivo" value="<?php echo trim($_GET["id"]); ?>"/>
                    <input type="hidden" name="tipo" value="<?php echo isset($_GET["tipo"]) ? $_GET["tipo"] : ''; ?>"/>
                    <p>¿Está seguro que desea eliminar este dispositivo?</p>
                    <p>
                        <input type="submit" value="Sí" class="btn btn-danger">
                        <a href="dispositivos.php?tipo=<?php echo isset($_GET["tipo"]) ? $_GET["tipo"] : ''; ?>" class="btn btn-secondary">No</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>