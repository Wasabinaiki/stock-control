<?php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador"){
    header("location: login.php");
    exit;
}

// Función para formatear el estado
function formatearEstado($estado) {
    // Primero convertir a minúsculas y reemplazar guiones bajos por espacios
    $estado = strtolower(str_replace('_', ' ', $estado));
    // Capitalizar la primera letra de cada palabra
    return ucwords($estado);
}

// Obtener los filtros de la URL
$asunto_filtro = isset($_GET['asunto']) ? $_GET['asunto'] : '';
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';
$orden_fecha = isset($_GET['orden_fecha']) ? $_GET['orden_fecha'] : '';

// Función para obtener todos los formularios de contacto con filtros
function getContactForms($link, $asunto_filtro, $estado_filtro, $orden_fecha) {
    $sql = "SELECT * FROM contactos WHERE 1=1";
    
    // Aplicar filtro de asunto
    if (!empty($asunto_filtro)) {
        $sql .= " AND asunto = '" . mysqli_real_escape_string($link, $asunto_filtro) . "'";
    }
    
    // Aplicar filtro de estado
    if (!empty($estado_filtro)) {
        $sql .= " AND estado = '" . mysqli_real_escape_string($link, $estado_filtro) . "'";
    }
    
    // Aplicar orden por fecha
    if (!empty($orden_fecha)) {
        $sql .= " ORDER BY fecha " . ($orden_fecha == 'asc' ? 'ASC' : 'DESC');
    } else {
        $sql .= " ORDER BY fecha DESC"; // Por defecto, más reciente primero
    }
    
    $result = mysqli_query($link, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Función para actualizar el estado y la información del formulario de contacto
function updateContactForm($link, $id, $status, $notes) {
    $sql = "UPDATE contactos SET estado = ?, notas = ? WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $status, $notes, $id);
    return mysqli_stmt_execute($stmt);
}

// Función para obtener asuntos únicos para el filtro
function getUniqueSubjects($link) {
    $sql = "SELECT DISTINCT asunto FROM contactos ORDER BY asunto";
    $result = mysqli_query($link, $sql);
    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row['asunto'];
    }
    return $subjects;
}

$message = '';

// Manejar el envío del formulario para actualizar el formulario de contacto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_form'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    
    if (updateContactForm($link, $id, $status, $notes)) {
        $message = "Formulario de contacto actualizado exitosamente.";
    } else {
        $message = "Error al actualizar el formulario de contacto.";
    }
}

// Obtener asuntos únicos para el filtro
$uniqueSubjects = getUniqueSubjects($link);

// Obtener todos los formularios de contacto con filtros aplicados
$contactForms = getContactForms($link, $asunto_filtro, $estado_filtro, $orden_fecha);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Formularios de Contacto</title>
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
        .content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        h1 {
            color: #764ba2;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .dashboard-link {
            color: white !important;
            border-radius: 5px;
            padding: 8px 15px !important;
            margin-right: 10px;
        }
        /* Estados estandarizados */
        .badge {
            padding: 6px 10px;
            border-radius: 12px;
            font-weight: 500;
        }
        .bg-pendiente {
            background-color: #ffc107 !important; /* Amarillo para pendiente */
            color: #000 !important;
        }
        .bg-en-proceso {
            background-color: #0d6efd !important; /* Azul para en proceso */
            color: #fff !important;
        }
        .bg-resuelto {
            background-color: #198754 !important; /* Verde para resuelto */
            color: #fff !important;
        }
        /* Estilos para los filtros */
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
            font-weight: bold;
        }
        .section-filters {
            background-color: #f0f2f5;
            padding: 15px;
            border-radius: 0 0 10px 10px;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-envelope me-2"></i>Gestión de Formularios de Contacto</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link dashboard-link" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Administrador
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Título de la sección -->
        <div class="section-header">
            <i class="fas fa-envelope me-2"></i>Formularios de Contacto
        </div>
        
        <!-- Filtros -->
        <div class="section-filters">
            <form action="" method="GET" class="filter-form">
                <select name="asunto" class="form-select">
                    <option value="">Todos los asuntos</option>
                    <?php foreach ($uniqueSubjects as $subject): ?>
                        <option value="<?php echo htmlspecialchars($subject); ?>" <?php echo $asunto_filtro == $subject ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente" <?php echo $estado_filtro == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En proceso" <?php echo $estado_filtro == 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
                    <option value="Resuelto" <?php echo $estado_filtro == 'Resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                </select>
                
                <select name="orden_fecha" class="form-select">
                    <option value="">Ordenar por fecha</option>
                    <option value="desc" <?php echo $orden_fecha == 'desc' ? 'selected' : ''; ?>>Más reciente primero</option>
                    <option value="asc" <?php echo $orden_fecha == 'asc' ? 'selected' : ''; ?>>Más antiguo primero</option>
                </select>
                
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if (count($contactForms) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Asunto</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contactForms as $form): ?>
                                    <tr>
                                        <td><?php echo $form['id']; ?></td>
                                        <td><?php echo htmlspecialchars($form['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($form['email']); ?></td>
                                        <td><?php echo htmlspecialchars($form['asunto']); ?></td>
                                        <td><?php echo $form['fecha']; ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = '';
                                            if ($form['estado'] == 'Resuelto') {
                                                $badgeClass = 'bg-resuelto';
                                            } elseif ($form['estado'] == 'En proceso') {
                                                $badgeClass = 'bg-en-proceso';
                                            } else {
                                                $badgeClass = 'bg-pendiente';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo formatearEstado($form['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $form['id']; ?>">
                                                Editar
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal de Edición -->
                                    <div class="modal fade" id="editModal<?php echo $form['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $form['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?php echo $form['id']; ?>">Editar Formulario de Contacto</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $form['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Estado</label>
                                                            <select class="form-select" id="status" name="status" required>
                                                                <option value="Pendiente" <?php echo $form['estado'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                                <option value="En proceso" <?php echo $form['estado'] == 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
                                                                <option value="Resuelto" <?php echo $form['estado'] == 'Resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="notes" class="form-label">Notas</label>
                                                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($form['notas']); ?></textarea>
                                                        </div>
                                                        <button type="submit" name="update_form" class="btn btn-primary">Guardar cambios</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay formularios de contacto disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>