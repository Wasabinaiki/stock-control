<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Determinar si el usuario es administrador
$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";

// Aquí puedes agregar lógica para cargar las preguntas frecuentes desde la base de datos si lo deseas
$faqs = [
    [
        "pregunta" => "¿Cómo puedo registrar un nuevo dispositivo?",
        "respuesta" => "Para registrar un nuevo dispositivo, ve a la sección 'Dispositivos' en el panel de usuario, haz clic en 'Agregar Dispositivo' y completa el formulario con la información requerida."
    ],
    [
        "pregunta" => "¿Cómo puedo solicitar mantenimiento para mi dispositivo?",
        "respuesta" => "Para solicitar mantenimiento, ve a la sección 'Mantenimiento' en tu panel de usuario, selecciona el dispositivo que necesita mantenimiento y completa el formulario de solicitud."
    ],
    [
        "pregunta" => "¿Cómo puedo ver el estado de mi solicitud de mantenimiento?",
        "respuesta" => "Puedes ver el estado de tu solicitud de mantenimiento en la sección 'Mis Solicitudes' de tu panel de usuario. Allí encontrarás una lista de todas tus solicitudes y su estado actual."
    ],
    [
        "pregunta" => "¿Cómo puedo actualizar la información de mi cuenta?",
        "respuesta" => "Para actualizar la información de tu cuenta, ve a la sección 'Mi Perfil' en tu panel de usuario. Allí podrás editar tu información personal y cambiar tu contraseña."
    ],
    [
        "pregunta" => "¿Qué debo hacer si olvidé mi contraseña?",
        "respuesta" => "Si olvidaste tu contraseña, haz clic en el enlace 'Olvidé mi contraseña' en la página de inicio de sesión. Sigue las instrucciones para restablecer tu contraseña a través de tu correo electrónico registrado."
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes - Control de Stock</title>
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
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-question-circle me-2"></i>Preguntas Frecuentes</a>
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
        <h2 class="mb-4">Preguntas Frecuentes</h2>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Guía de uso del sitio</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordionFAQ">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($faq['pregunta']); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body">
                                    <?php echo htmlspecialchars($faq['respuesta']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver al Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>