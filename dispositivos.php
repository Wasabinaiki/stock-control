<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$tipo = $_GET['tipo'] ?? '';

if (empty($tipo)) {
    header("location: dashboard.php");
    exit;
}

// Mensajes de éxito y error
$success_message = '';
$error_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$sql = "SELECT id_dispositivo, marca, modelo, fecha_entrega, licencias, procesador, almacenamiento, ram, serial FROM dispositivos WHERE tipo = ? AND id_usuario = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "si", $tipo, $_SESSION["id"]);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
    }
} else {
    echo "Error en la preparación de la consulta.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver <?php echo ucfirst($tipo); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 1.5rem;
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

        .device-info {
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .device-info strong {
            color: #764ba2;
        }

        .btn-success {
            background: linear-gradient(135deg, #20bf6b 0%, #0b8a45 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #0b8a45 0%, #20bf6b 100%);
        }

        /* Estilos para el modal de confirmación */
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-footer {
            border-top: none;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-laptop me-2"></i>Dispositivos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
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
        <?php
        // Mostrar mensaje de éxito
        if (!empty($success_message)) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($success_message);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }

        // Mostrar mensaje de error
        if (!empty($error_message)) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($error_message);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        ?>

        <h2 class="mb-4"><i class="fas fa-list me-2"></i>Lista de <?php echo ucfirst($tipo); ?>s</h2>
        <div class="row">
            <?php
            if (isset($result) && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    echo "<div class='col-md-4'>";
                    echo "<div class='card'>";
                    echo "<div class='card-body'>";
                    echo "<h4 class='card-title mb-3'>" . htmlspecialchars($row['marca']) . " " . htmlspecialchars($row['modelo']) . "</h4>";
                    echo "<div class='device-info'><strong><i class='fas fa-calendar-alt me-2'></i>Fecha de entrega:</strong> " . date('d/m/Y', strtotime($row['fecha_entrega'])) . "</div>";
                    if (!empty($row['licencias'])) {
                        echo "<div class='device-info'><strong><i class='fas fa-key me-2'></i>Licencias:</strong> " . htmlspecialchars($row['licencias']) . "</div>";
                    }
                    echo "<div class='device-info'><strong><i class='fas fa-microchip me-2'></i>Procesador:</strong> " . htmlspecialchars($row['procesador']) . "</div>";
                    echo "<div class='device-info'><strong><i class='fas fa-hdd me-2'></i>Almacenamiento:</strong> " . htmlspecialchars($row['almacenamiento']) . "</div>";
                    echo "<div class='device-info'><strong><i class='fas fa-memory me-2'></i>RAM:</strong> " . htmlspecialchars($row['ram']) . "</div>";
                    echo "<div class='device-info'><strong><i class='fas fa-barcode me-2'></i>Serial:</strong> " . htmlspecialchars($row['serial']) . "</div>";
                    echo "<div class='d-flex justify-content-between mt-4'>";
                    echo "<a href='editar_dispositivo.php?id=" . $row['id_dispositivo'] . "&tipo=" . $tipo . "' class='btn btn-primary'><i class='fas fa-edit me-2'></i>Editar</a>";
                    echo "<button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal" . $row['id_dispositivo'] . "'>";
                    echo "<i class='fas fa-trash-alt me-2'></i>Eliminar</button>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";

                    // Modal de confirmación para eliminar
                    echo "<div class='modal fade' id='deleteModal" . $row['id_dispositivo'] . "' tabindex='-1' aria-labelledby='deleteModalLabel" . $row['id_dispositivo'] . "' aria-hidden='true'>";
                    echo "<div class='modal-dialog'>";
                    echo "<div class='modal-content'>";
                    echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='deleteModalLabel" . $row['id_dispositivo'] . "'><i class='fas fa-exclamation-triangle me-2'></i>Confirmar eliminación</h5>";
                    echo "<button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal' aria-label='Close'></button>";
                    echo "</div>";
                    echo "<div class='modal-body'>";
                    echo "<p>¿Estás seguro de que deseas eliminar este dispositivo? Esta acción no se puede deshacer.</p>";
                    echo "<p><strong>Dispositivo:</strong> " . htmlspecialchars($row['marca']) . " " . htmlspecialchars($row['modelo']) . "</p>";
                    echo "</div>";
                    echo "<div class='modal-footer'>";
                    echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'><i class='fas fa-times me-2'></i>Cancelar</button>";
                    echo "<a href='eliminar_dispositivo.php?id=" . $row['id_dispositivo'] . "&tipo=" . $tipo . "' class='btn btn-danger'><i class='fas fa-trash-alt me-2'></i>Eliminar</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";

                    echo "</div>";
                }
            } else {
                echo "<div class='col'>";
                echo "<div class='alert alert-info'>";
                echo "<i class='fas fa-info-circle me-2'></i>No se encontraron dispositivos.";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <div class="text-center my-4">
        <a href="agregar_dispositivo.php?tipo=<?php echo $tipo; ?>" class="btn btn-success btn-lg">
            <i class="fas fa-plus me-2"></i>Agregar nuevo dispositivo
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($link);
?>