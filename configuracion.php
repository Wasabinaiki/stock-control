<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$usuario_id = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

if ($usuario_id === null) {
    session_destroy();
    header("location: login.php");
    exit;
}

$email = '';
$tamanio_texto = 'normal';
$tema = 'claro';
$idioma = 'es';

$sql = "SELECT email, tamanio_texto, tema, idioma FROM usuarios WHERE id_usuario = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $email, $tamanio_texto, $tema, $idioma);
            mysqli_stmt_fetch($stmt);
        }
    }
    mysqli_stmt_close($stmt);
}

// Verificar si hay un mensaje de éxito o error
$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
            font-size: <?php echo $tamanio_texto === 'pequeno' ? '14px' : ($tamanio_texto === 'grande' ? '18px' : '16px'); ?>;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: background 0.3s ease;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .container {
            max-width: 800px;
            margin-top: 2rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .dark-mode {
            background-color: #333;
            color: #fff;
        }
        .dark-mode .card {
            background-color: #444;
            color: #fff;
        }
        .dark-mode .navbar {
            background: linear-gradient(135deg, #333 0%, #222 100%);
        }
    </style>
</head>
<body class="<?php echo $tema === 'oscuro' ? 'dark-mode' : ''; ?>">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-cog me-2"></i>Control de Stock</a>
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

    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4"><i class="fas fa-cogs me-2"></i>Configuración de la App</h2>
                <form action="guardar_configuracion.php" method="post">
                    <div class="mb-3">
                        <label for="email_notificaciones" class="form-label">Email para Notificaciones</label>
                        <input type="email" class="form-control" id="email_notificaciones" name="email_notificaciones" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tamanio_texto" class="form-label">Tamaño del Texto</label>
                        <select class="form-select" id="tamanio_texto" name="tamanio_texto">
                            <option value="pequeno" <?php echo $tamanio_texto === 'pequeno' ? 'selected' : ''; ?>>Pequeño</option>
                            <option value="normal" <?php echo $tamanio_texto === 'normal' ? 'selected' : ''; ?>>Normal</option>
                            <option value="grande" <?php echo $tamanio_texto === 'grande' ? 'selected' : ''; ?>>Grande</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tema" class="form-label">Tema de la Interfaz</label>
                        <select class="form-select" id="tema" name="tema">
                            <option value="claro" <?php echo $tema === 'claro' ? 'selected' : ''; ?>>Claro</option>
                            <option value="oscuro" <?php echo $tema === 'oscuro' ? 'selected' : ''; ?>>Oscuro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="idioma" class="form-label">Idioma</label>
                        <select class="form-select" id="idioma" name="idioma">
                            <option value="es" <?php echo $idioma === 'es' ? 'selected' : ''; ?>>Español</option>
                            <option value="en" <?php echo $idioma === 'en' ? 'selected' : ''; ?>>English</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Configuración</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tamanioTexto = document.getElementById('tamanio_texto');
            const tema = document.getElementById('tema');
            const idioma = document.getElementById('idioma');

            tamanioTexto.addEventListener('change', function() {
                document.body.style.fontSize = this.value === 'pequeno' ? '14px' : this.value === 'grande' ? '18px' : '16px';
            });

            tema.addEventListener('change', function() {
                document.body.classList.toggle('dark-mode', this.value === 'oscuro');
            });

            idioma.addEventListener('change', function() {
                document.documentElement.lang = this.value;
                // Aquí podrías agregar lógica para cambiar los textos de la página
            });
        });
    </script>
</body>
</html>

<?php
mysqli_close($link);
?>