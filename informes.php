<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verificar si id_usuario está definido en la sesión
if (!isset($_SESSION["id"])) {
    header("location: error.php?mensaje=Sesión inválida");
    exit;
}

$id_usuario = $_SESSION["id"];

// Obtener información del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt_usuario = mysqli_prepare($link, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
mysqli_stmt_execute($stmt_usuario);
$result_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);

// Obtener dispositivos del usuario
$sql_dispositivos = "SELECT * FROM dispositivos WHERE id_usuario = ?";
$stmt_dispositivos = mysqli_prepare($link, $sql_dispositivos);
mysqli_stmt_bind_param($stmt_dispositivos, "i", $id_usuario);
mysqli_stmt_execute($stmt_dispositivos);
$result_dispositivos = mysqli_stmt_get_result($stmt_dispositivos);

// Obtener mantenimientos programados del usuario
$sql_mantenimientos = "SELECT m.* FROM mantenimientos m 
                       JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo 
                       WHERE d.id_usuario = ? AND m.estado != 'completado'";
$stmt_mantenimientos = mysqli_prepare($link, $sql_mantenimientos);
mysqli_stmt_bind_param($stmt_mantenimientos, "i", $id_usuario);
mysqli_stmt_execute($stmt_mantenimientos);
$result_mantenimientos = mysqli_stmt_get_result($stmt_mantenimientos);

// Obtener informes del usuario
$sql_informes = "SELECT * FROM informes WHERE id_usuario = ? ORDER BY fecha_creacion DESC";
$stmt_informes = mysqli_prepare($link, $sql_informes);
mysqli_stmt_bind_param($stmt_informes, "i", $id_usuario);
mysqli_stmt_execute($stmt_informes);
$result_informes = mysqli_stmt_get_result($stmt_informes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Informes</title>
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
            <a class="navbar-brand" href="#"><i class="fas fa-file-alt me-2"></i>Mis Informes</a>
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

    <div class="container mt-4">
        <h2 class="mb-4">Mis Informes</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></p>
                        <p><strong>Área:</strong> <?php echo htmlspecialchars($usuario['area']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Mis Dispositivos</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result_dispositivos) > 0): ?>
                            <ul>
                                <?php while ($dispositivo = mysqli_fetch_assoc($result_dispositivos)): ?>
                                    <li><?php echo htmlspecialchars($dispositivo['tipo'] . ' - ' . $dispositivo['marca'] . ' ' . $dispositivo['modelo']); ?></li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p>No tienes dispositivos registrados.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Mantenimientos Programados</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_mantenimientos) > 0): ?>
                    <ul>
                        <?php while ($mantenimiento = mysqli_fetch_assoc($result_mantenimientos)): ?>
                            <li>
                                <?php echo htmlspecialchars($mantenimiento['descripcion']); ?> - 
                                Fecha: <?php echo htmlspecialchars($mantenimiento['fecha_programada']); ?> - 
                                Estado: <?php echo htmlspecialchars($mantenimiento['estado']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No tienes mantenimientos programados pendientes.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Mis Informes</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_informes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Fecha de Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($informe = mysqli_fetch_assoc($result_informes)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($informe['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($informe['fecha_creacion']); ?></td>
                                        <td>
                                            <a href="ver_informe.php?id=<?php echo $informe['id']; ?>" class="btn btn-primary btn-sm">Ver</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No tienes informes disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>