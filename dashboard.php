<?php
// dashboard.php
session_start();
require_once "includes/config.php";

// Verificar si el usuario está logueado
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Obtener los dispositivos del usuario
$user_id = $_SESSION["id"];
$sql = "SELECT * FROM dispositivos WHERE id_usuario = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$dispositivos = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Control Stock</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e1e1e 0%, #3d0000 100%);
            min-height: 100vh;
            padding-top: 60px;
        }
        .navbar {
            background-color: rgba(220, 53, 69, 0.9) !important;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            margin-bottom: 20px;
        }
        .card-body {
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
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="agregar_dispositivo.php">Agregar Dispositivo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-white mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Computadoras</h5>
                        <p class="card-text">
                            <i class="fas fa-desktop fa-3x mb-3"></i><br>
                            <?php echo count(array_filter($dispositivos, function($d) { return $d['tipo'] == 'Computadora'; })); ?> dispositivos
                        </p>
                        <a href="dispositivos.php?tipo=Computadora" class="btn btn-primary">Ver Computadoras</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tablets</h5>
                        <p class="card-text">
                            <i class="fas fa-tablet-alt fa-3x mb-3"></i><br>
                            <?php echo count(array_filter($dispositivos, function($d) { return $d['tipo'] == 'Tablet'; })); ?> dispositivos
                        </p>
                        <a href="dispositivos.php?tipo=Tablet" class="btn btn-primary">Ver Tablets</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Celulares</h5>
                        <p class="card-text">
                            <i class="fas fa-mobile-alt fa-3x mb-3"></i><br>
                            <?php echo count(array_filter($dispositivos, function($d) { return $d['tipo'] == 'Celular'; })); ?> dispositivos
                        </p>
                        <a href="dispositivos.php?tipo=Celular" class="btn btn-primary">Ver Celulares</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>