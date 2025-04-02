<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$usuario_id = $_SESSION["id"];
$mensaje = "";
$error = "";

// Obtener los dispositivos del usuario para el dropdown
$sql_dispositivos = "SELECT id_dispositivo, tipo, marca, modelo FROM dispositivos WHERE id_usuario = ?";
$stmt_dispositivos = mysqli_prepare($link, $sql_dispositivos);
mysqli_stmt_bind_param($stmt_dispositivos, "i", $usuario_id);
mysqli_stmt_execute($stmt_dispositivos);
$result_dispositivos = mysqli_stmt_get_result($stmt_dispositivos);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $id_dispositivo = $_POST["id_dispositivo"];
    $descripcion = $_POST["descripcion"];
    $fecha_programada = $_POST["fecha_programada"];
    $direccion_recogida = $_POST["direccion_recogida"];
    
    // Insertar en la tabla de mantenimientos
    $sql_mantenimiento = "INSERT INTO mantenimientos (id_dispositivo, fecha_programada, descripcion, estado) 
                         VALUES (?, ?, ?, 'programado')";
    
    if ($stmt = mysqli_prepare($link, $sql_mantenimiento)) {
        mysqli_stmt_bind_param($stmt, "iss", $id_dispositivo, $fecha_programada, $descripcion);
        
        if (mysqli_stmt_execute($stmt)) {
            $mantenimiento_id = mysqli_insert_id($link);
            
            // Insertar en la tabla de envíos
            $sql_envio = "INSERT INTO envios (usuario_id, direccion_destino, estado_envio, fecha_envio, fecha_salida) 
                         VALUES (?, ?, 'En Proceso', CURDATE(), CURDATE())";
            
            if ($stmt_envio = mysqli_prepare($link, $sql_envio)) {
                $direccion_destino = "Bodega Central"; // Dirección predeterminada de la bodega
                mysqli_stmt_bind_param($stmt_envio, "is", $usuario_id, $direccion_destino);
                
                if (mysqli_stmt_execute($stmt_envio)) {
                    $mensaje = "Mantenimiento programado con éxito. Se ha registrado el envío correspondiente.";
                } else {
                    $error = "Error al registrar el envío: " . mysqli_error($link);
                }
                
                mysqli_stmt_close($stmt_envio);
            } else {
                $error = "Error al preparar la consulta de envío: " . mysqli_error($link);
            }
        } else {
            $error = "Error al programar el mantenimiento: " . mysqli_error($link);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error al preparar la consulta: " . mysqli_error($link);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programar Mantenimiento</title>
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
        .container {
            max-width: 800px;
            margin-top: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
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
            <a class="navbar-brand" href="#"><i class="fas fa-tools me-2"></i>Programar Mantenimiento</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if(!empty($mensaje)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Programar Mantenimiento</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="id_dispositivo" class="form-label">Seleccione un Dispositivo</label>
                        <select class="form-select" id="id_dispositivo" name="id_dispositivo" required>
                            <option value="">Seleccione un dispositivo</option>
                            <?php while($row = mysqli_fetch_assoc($result_dispositivos)): ?>
                                <option value="<?php echo $row['id_dispositivo']; ?>">
                                    <?php echo htmlspecialchars($row['tipo'] . ' - ' . $row['marca'] . ' ' . $row['modelo']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción del Problema</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_programada" class="form-label">Fecha Programada</label>
                        <input type="date" class="form-control" id="fecha_programada" name="fecha_programada" required>
                    </div>
                    <div class="mb-3">
                        <label for="direccion_recogida" class="form-label">Dirección de Recogida</label>
                        <input type="text" class="form-control" id="direccion_recogida" name="direccion_recogida" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Programar Mantenimiento</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
mysqli_close($link);
?>