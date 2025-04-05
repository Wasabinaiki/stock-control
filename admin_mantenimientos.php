<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

// Procesar eliminación si se recibe un ID
if (isset($_POST['eliminar_id']) && !empty($_POST['eliminar_id'])) {
    $id_mantenimiento = $_POST['eliminar_id'];
    
    // Iniciar transacción
    mysqli_begin_transaction($link);
    
    try {
        // Eliminar el mantenimiento
        $sql_delete = "DELETE FROM mantenimientos WHERE id = ?";
        $stmt_delete = mysqli_prepare($link, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $id_mantenimiento);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
        
        // Confirmar la transacción
        mysqli_commit($link);
        
        // Establecer mensaje de éxito
        $_SESSION['success_message'] = "Mantenimiento eliminado correctamente.";
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        mysqli_rollback($link);
        
        // Establecer mensaje de error
        $_SESSION['error_message'] = "Error al eliminar el mantenimiento: " . $e->getMessage();
    }
    
    // Redirigir para evitar reenvío del formulario
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Filtros
$estado_filtro = isset($_GET['estado_filtro']) ? $_GET['estado_filtro'] : '';
$orden_fecha = isset($_GET['orden_fecha']) ? $_GET['orden_fecha'] : 'desc';

// Función para formatear el estado
function formatearEstado($estado)
{
    // Primero convertir a minúsculas y reemplazar guiones bajos por espacios
    $estado = strtolower(str_replace('_', ' ', $estado));
    // Capitalizar la primera letra de cada palabra
    return ucwords($estado);
}

// Obtener lista de mantenimientos con filtros
$sql = "SELECT m.*, d.marca, d.modelo, u.username 
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        JOIN usuarios u ON d.id_usuario = u.id_usuario
        WHERE 1=1";

if (!empty($estado_filtro)) {
    $sql .= " AND m.estado = '" . mysqli_real_escape_string($link, $estado_filtro) . "'";
}

$sql .= " ORDER BY m.fecha_programada " . ($orden_fecha == 'asc' ? 'ASC' : 'DESC');

$result_mantenimientos = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimientos Programados</title>
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
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            border: none;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #ee5253 0%, #ff6b6b 100%);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
        }

        /* Estados estandarizados */
        .bg-pendiente {
            background-color: #ffc107 !important;
            /* Amarillo para pendiente/programado */
            color: #000 !important;
        }

        .bg-en-proceso {
            background-color: #0d6efd !important;
            /* Azul para en proceso */
            color: #fff !important;
        }

        .bg-completado {
            background-color: #198754 !important;
            /* Verde para completado */
            color: #fff !important;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-form select,
        .filter-form button {
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .filter-form select,
            .filter-form button {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            color: white;
        }
        
        .modal-title {
            font-weight: bold;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-tools me-2"></i>Mantenimientos Programados</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Administrador
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
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Mantenimientos Programados</h5>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form action="" method="GET" class="filter-form">
                    <select name="estado_filtro" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="programado" <?php echo $estado_filtro == 'programado' ? 'selected' : ''; ?>>
                            Programado</option>
                        <option value="en_proceso" <?php echo $estado_filtro == 'en_proceso' ? 'selected' : ''; ?>>En
                            proceso</option>
                        <option value="completado" <?php echo $estado_filtro == 'completado' ? 'selected' : ''; ?>>
                            Completado</option>
                    </select>

                    <select name="orden_fecha" class="form-select">
                        <option value="desc" <?php echo $orden_fecha == 'desc' ? 'selected' : ''; ?>>Fecha más reciente
                            primero</option>
                        <option value="asc" <?php echo $orden_fecha == 'asc' ? 'selected' : ''; ?>>Fecha más antigua
                            primero</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dispositivo</th>
                                <th>Usuario</th>
                                <th>Fecha Programada</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_mantenimientos) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result_mantenimientos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['fecha_programada']))); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                        <td>
                                            <?php
                                            $estado = strtolower($row['estado']);
                                            $badgeClass = '';

                                            if ($estado == 'completado') {
                                                $badgeClass = 'bg-completado';
                                            } elseif ($estado == 'en_proceso' || $estado == 'en proceso') {
                                                $badgeClass = 'bg-en-proceso';
                                            } else {
                                                $badgeClass = 'bg-pendiente';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo formatearEstado($row['estado']); ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="editar_mantenimiento.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#eliminarModal" 
                                                    data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay mantenimientos programados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación para Eliminar -->
    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este mantenimiento? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="">
                        <input type="hidden" name="eliminar_id" id="eliminar_id" value="">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para pasar el ID al modal de eliminación
        document.addEventListener('DOMContentLoaded', function() {
            const eliminarModal = document.getElementById('eliminarModal');
            eliminarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                document.getElementById('eliminar_id').value = id;
            });
        });
    </script>
</body>

</html>