<?php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador"){
    header("location: login.php");
    exit;
}

// Función para obtener todos los formularios de contacto
function getContactForms($link) {
    $sql = "SELECT * FROM contactos ORDER BY fecha DESC";
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

// Obtener todos los formularios de contacto
$contactForms = getContactForms($link);
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Panel de Administración</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="content">
            <h1 class="mb-4">Gestión de Formularios de Contacto</h1>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

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
                            <td><?php echo htmlspecialchars($form['estado']); ?></td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>