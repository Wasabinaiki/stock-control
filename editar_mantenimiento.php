<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

$mantenimiento_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($mantenimiento_id === 0) {
    header("location: admin_mantenimientos.php");
    exit;
}

// Obtener detalles del mantenimiento
$sql = "SELECT m.*, d.marca, d.modelo, u.username 
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        JOIN usuarios u ON d.id_usuario = u.id_usuario
        WHERE m.id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $mantenimiento_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $mantenimiento = mysqli_fetch_assoc($result);
        } else {
            header("location: admin_mantenimientos.php");
            exit;
        }
    } else {
        echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
    }
    mysqli_stmt_close($stmt);
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_fecha = $_POST["fecha_programada"];
    $new_descripcion = $_POST["descripcion"];
    $new_estado = $_POST["estado"];

    $sql = "UPDATE mantenimientos SET fecha_programada = ?, descripcion = ?, estado = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssi", $new_fecha, $new_descripcion, $new_estado, $mantenimiento_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Mantenimiento actualizado con éxito.";
            header("location: admin_mantenimientos.php");
            exit();
        } else {
            $error_message = "Error al actualizar el mantenimiento.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Función para depurar valores
function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug: " . addslashes($output) . "');</script>";
}

// Depurar el valor del estado
debug_to_console("Estado actual: " . $mantenimiento['estado']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-bottom: 40px;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin-bottom: 30px;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-edit me-2"></i>Editar Mantenimiento</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_mantenimientos.php">
                            <i class="fas fa-tools me-2"></i>Volver a Mantenimientos
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Mantenimiento
                    #<?php echo $mantenimiento['id']; ?></h5>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $mantenimiento_id); ?>"
                    method="post">
                    <div class="mb-3">
                        <label for="dispositivo" class="form-label">Dispositivo</label>
                        <input type="text" class="form-control" id="dispositivo"
                            value="<?php echo htmlspecialchars($mantenimiento['marca'] . ' ' . $mantenimiento['modelo']); ?>"
                            readonly>
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario"
                            value="<?php echo htmlspecialchars($mantenimiento['username']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_programada" class="form-label">Fecha Programada</label>
                        <input type="date" class="form-control" id="fecha_programada" name="fecha_programada"
                            value="<?php echo htmlspecialchars($mantenimiento['fecha_programada']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                            required><?php echo htmlspecialchars($mantenimiento['descripcion']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="programado" <?php echo strtolower($mantenimiento['estado']) == 'programado' ? 'selected' : ''; ?>>Programado</option>
                            <option value="en_proceso" <?php echo strtolower($mantenimiento['estado']) == 'en_proceso' ? 'selected' : ''; ?>>En proceso</option>
                            <option value="completado" <?php echo strtolower($mantenimiento['estado']) == 'completado' ? 'selected' : ''; ?>>Completado</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>