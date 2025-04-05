<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['name'];
    $email = $_POST['email'];
    $telefono = $_POST['phone'];
    $asunto = $_POST['subject'];
    $mensaje = $_POST['message'];

    $archivo = '';
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["attachment"]["name"]);
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $archivo = $target_file;
        }
    }

    $sql = "INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, archivo) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssss", $param_nombre, $param_email, $param_telefono, $param_asunto, $param_mensaje, $param_archivo);

        $param_nombre = $nombre;
        $param_email = $email;
        $param_telefono = $telefono;
        $param_asunto = $asunto;
        $param_mensaje = $mensaje;
        $param_archivo = $archivo;

        if (mysqli_stmt_execute($stmt)) {
            $message = "Gracias por contactarnos. Responderemos pronto.";
        } else {
            $message = "Oops! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
        }
    }

    mysqli_stmt_close($stmt);

    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
}

if (isset($_GET['success'])) {
    $message = "Gracias por contactarnos. Responderemos pronto.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctenos - Control de Stock</title>
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

        .content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        h1,
        h2 {
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
            <a class="navbar-brand" href="#"><i class="fas fa-envelope me-2"></i>Contacto</a>
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

    <div class="container mt-4">
        <div class="content">
            <h1 class="mb-4">Contáctenos</h1>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
                enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre Completo:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Teléfono (opcional):</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Asunto:</label>
                    <select class="form-select" id="subject" name="subject">
                        <option value="soporte">Soporte Técnico</option>
                        <option value="sugerencias">Sugerencias</option>
                        <option value="quejas">Quejas</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Mensaje:</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="attachment" class="form-label">Adjuntar archivo (opcional):</label>
                    <input type="file" class="form-control" id="attachment" name="attachment"
                        accept="image/*,application/pdf">
                </div>

                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>

        <div class="mt-4 mb-4">
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver al
                Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>