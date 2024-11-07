<?php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else{
            header("location: error.php");
            exit();
        }
    } else{
        echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
    }
    mysqli_stmt_close($stmt);
}

$sql_devices = "SELECT tipo, COUNT(*) as count FROM dispositivos WHERE id_usuario = ? GROUP BY tipo";
if($stmt = mysqli_prepare($link, $sql_devices)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result_devices = mysqli_stmt_get_result($stmt);
    } else{
        echo "Oops! Algo salió mal al obtener los dispositivos.";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-info {
            margin-bottom: 20px;
        }
        .profile-info h3 {
            color: #764ba2;
            margin-bottom: 15px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
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
            <h2 class="mb-4"><i class="fas fa-user me-2"></i>Perfil de Usuario</h2>
            <div class="profile-info">
                <h3>Información Personal</h3>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($row["nombre"] . " " . $row["apellido"]); ?></p>
                <p><strong>ID - Usuario:</strong> <?php echo htmlspecialchars($row["id_usuario"]); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($row["email"]); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($row["telefono"]); ?></p>
                <p><strong>Área:</strong> <?php echo htmlspecialchars($row["area"]); ?></p>
            </div>
            <div class="profile-info">
                <h3>Dispositivos Asignados</h3>
                <ul>
                    <?php
                    while($device = mysqli_fetch_array($result_devices)){
                        echo "<li>" . ucfirst($device['tipo']) . ": " . $device['count'] . "</li>";
                    }
                    ?>
                </ul>
            </div>
            <a href="editar_perfil.php" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Editar Perfil</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>