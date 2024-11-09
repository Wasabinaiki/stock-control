<?php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador"){
    header("location: login.php");
    exit;
}

// Gestión de roles
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_role"])){
    $user_id = $_POST["user_id"];
    $new_role = $_POST["new_role"];
    
    $sql = "UPDATE usuarios SET rol = ? WHERE id_usuario = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "si", $new_role, $user_id);
        if(mysqli_stmt_execute($stmt)){
            $success_message = "Rol actualizado con éxito.";
        } else {
            $error_message = "Error al actualizar el rol.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Obtener lista de usuarios
$sql = "SELECT id_usuario, username, email, rol FROM usuarios";
$result_users = mysqli_query($link, $sql);

// Obtener lista de PQRs
$sql = "SELECT p.*, u.username FROM pqrs p JOIN usuarios u ON p.id_usuario = u.id_usuario ORDER BY p.fecha_creacion DESC";
$result_pqrs = mysqli_query($link, $sql);

// Obtener lista de mantenimientos
$sql = "SELECT * FROM mantenimientos ORDER BY fecha_programada DESC";
$result_mantenimientos = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
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
        .table {
            margin-bottom: 0;
        }
        .table th {
            border-top: none;
        }
        .form-select-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-user-shield me-2"></i>Panel de Administración</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bodega.php">
                            <i class="fas fa-warehouse me-2"></i>Bodega
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
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Gestión de Roles</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result_users)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $row['rol'] == 'administrador' ? 'bg-primary' : 'bg-secondary'; ?>">
                                                <?php echo htmlspecialchars($row['rol']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id_usuario']; ?>">
                                                <select name="new_role" class="form-select form-select-sm" style="width: auto;">
                                                    <option value="usuario" <?php echo $row['rol'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                                    <option value="administrador" <?php echo $row['rol'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                                </select>
                                                <button type="submit" name="update_role" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>PQRs Registrados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($result_pqrs) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($result_pqrs)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                            <td>
                                                <span class="badge <?php 
                                                    switch($row['estado']) {
                                                        case 'pendiente':
                                                            echo 'bg-warning';
                                                            break;
                                                        case 'en_proceso':
                                                            echo 'bg-info';
                                                            break;
                                                        case 'resuelto':
                                                            echo 'bg-success';
                                                            break;
                                                        default:
                                                            echo 'bg-secondary';
                                                    }
                                                ?>">
                                                    <?php echo htmlspecialchars($row['estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['fecha_creacion']))); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No hay PQRs registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Mantenimientos Programados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha Programada</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result_mantenimientos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_programada']); ?></td>
                                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                        <td><?php echo htmlspecialchars($row['estado']); ?></td>
                                        <td>
                                            <a href="ver_mantenimiento.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Ver detalles</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informes</h5>
                    </div>
                    <div class="card-body">
                        <p>Gestiona todos los informes del sistema.</p>
                        <a href="admin_informes.php" class="btn btn-primary">Ir a Informes</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Reportes</h5>
                    </div>
                    <div class="card-body">
                        <p>Administra todos los reportes generados.</p>
                        <a href="admin_reportes.php" class="btn btn-primary">Ir a Reportes</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Facturas</h5>
                    </div>
                    <div class="card-body">
                        <p>Gestiona todas las facturas del sistema.</p>
                        <a href="admin_facturas.php" class="btn btn-primary">Ir a Facturas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>