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
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .device-list {
            list-style-type: none;
            padding-left: 0;
        }
        .device-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .device-list li:last-child {
            border-bottom: none;
        }
        .device-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
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
                <h3><i class="fas fa-id-card me-2"></i>Información Personal</h3>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($row["nombre"] . " " . $row["apellido"]); ?></p>
                        <p><strong>Usuario:</strong> <?php echo htmlspecialchars($row["username"]); ?></p>
                        <p><strong>Correo:</strong> <?php echo htmlspecialchars($row["email"]); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Teléfono:</strong> <?php echo !empty($row["telefono"]) ? htmlspecialchars($row["telefono"]) : "No especificado"; ?></p>
                        <p><strong>Área:</strong> <?php echo !empty($row["area"]) ? htmlspecialchars($row["area"]) : "No especificada"; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="profile-info">
                <h3><i class="fas fa-laptop me-2"></i>Dispositivos Asignados</h3>
                <ul class="device-list">
                    <?php
                    $has_devices = false;
                    $device_icons = [
                        'computadora' => 'fas fa-desktop',
                        'tablet' => 'fas fa-tablet-alt',
                        'celular' => 'fas fa-mobile-alt'
                    ];
                    
                    if (mysqli_num_rows($result_devices) > 0) {
                        $has_devices = true;
                        while($device = mysqli_fetch_array($result_devices)){
                            $icon_class = isset($device_icons[strtolower($device['tipo'])]) ? $device_icons[strtolower($device['tipo'])] : 'fas fa-hdd';
                            echo "<li><span class='device-icon'><i class='" . $icon_class . "'></i></span> " . ucfirst($device['tipo']) . ": " . $device['count'] . "</li>";
                        }
                    }
                    
                    if (!$has_devices) {
                        echo "<li>No hay dispositivos asignados</li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="editar_perfil.php" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Editar Perfil</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>