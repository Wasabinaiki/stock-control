<?php
session_start();
require_once "includes/config.php";


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}


$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";


$faqs = [
    [
        "pregunta" => "¿Cómo puedo registrar un nuevo dispositivo?",
        "respuesta" => "Para registrar un nuevo dispositivo, ve a la sección 'Dispositivos' en el panel de usuario, haz clic en 'Agregar Dispositivo' y completa el formulario con la información requerida como tipo, marca, modelo, especificaciones técnicas y número de serie."
    ],
    [
        "pregunta" => "¿Cómo puedo solicitar mantenimiento para mi dispositivo?",
        "respuesta" => "Para solicitar mantenimiento, ve a la sección 'Mantenimiento' en tu panel de usuario, selecciona el dispositivo que necesita mantenimiento y completa el formulario de solicitud detallando el problema o servicio requerido."
    ],
    [
        "pregunta" => "¿Cómo puedo ver el estado de mi solicitud de mantenimiento?",
        "respuesta" => "Puedes ver el estado de tu solicitud de mantenimiento en la sección 'Mantenimientos' de tu panel de usuario. Allí encontrarás una lista de todas tus solicitudes y su estado actual (programado, en proceso o completado)."
    ],
    [
        "pregunta" => "¿Cómo puedo actualizar la información de mi cuenta?",
        "respuesta" => "Para actualizar la información de tu cuenta, ve a la sección 'Mi Perfil' en tu panel de usuario. Allí podrás editar tu información personal."
    ],
    [
        "pregunta" => "¿Qué debo hacer si olvidé mi contraseña?",
        "respuesta" => "Si olvidaste tu contraseña, haz clic en el enlace 'Recupera tu contraseña' en la página de inicio de sesión. Sigue las instrucciones para restablecer tu contraseña a través de tu correo electrónico registrado."
    ],
    [
        "pregunta" => "¿Puedo cancelar un mantenimiento programado?",
        "respuesta" => "Sí, puedes cancelar un mantenimiento programado hasta 24 horas antes de la fecha establecida. Para hacerlo, debes de rellenar el formulario de 'Contáctenos', ubicado en tu panel de usuario, detallar el dispositivo en cuestión y un asesor se comunicará contigo para resolver el inconveniente."
    ],
    [
        "pregunta" => "¿Cómo puedo reportar un problema con la plataforma?",
        "respuesta" => "Para reportar un problema con la plataforma, ve a la sección 'Soporte' y selecciona 'Queja'. Completa el formulario describiendo detalladamente el inconveniente que estás experimentando. Nuestro equipo de soporte te responderá en un plazo máximo de 48 horas."
    ],
    [
        "pregunta" => "¿Cómo puedo ver un informe de mis dispositivos?",
        "respuesta" => "Para visualizar un informe de tus dispositivos, ve a la sección 'Informes' en tu panel de usuario. Allí encontrarás toda la información de tus dispositivos y mantenimientos."
    ],
    [
        "pregunta" => "¿Qué información necesito para registrar un dispositivo?",
        "respuesta" => "Para registrar un dispositivo necesitas: tipo de dispositivo (computadora, tablet, celular), marca, modelo, número de serie, fecha de adquisición, especificaciones técnicas (procesador, RAM, almacenamiento) y, opcionalmente, información sobre licencias de software."
    ],
    [
        "pregunta" => "¿Cómo puedo eliminar un dispositivo?",
        "respuesta" => "Para eliminar un dispositivo, ve a la sección 'Dispositivos', selecciona el dispositivo que deseas eliminar, haz clic en 'Eliminar' y confirma la acción."
    ],
    [
        "pregunta" => "¿Puedo programar mantenimientos preventivos para mis dispositivos?",
        "respuesta" => "Sí, puedes programar mantenimientos preventivos para tus dispositivos. Ve a la sección 'Mantenimiento', selecciona 'Programar Mantenimiento Preventivo', elige el dispositivo, la fecha deseada y el tipo de mantenimiento preventivo (limpieza, actualización de software, revisión general, etc.). El sistema te notificará cuando se acerque la fecha programada."
    ],
    [
        "pregunta" => "¿Cómo puedo enviar una PQRS (Petición, Queja, Reclamo o Sugerencia)?",
        "respuesta" => "Para enviar una PQRS, ve a la sección 'PQRS' en el menú principal. Selecciona el tipo de solicitud (petición, queja, reclamo o sugerencia), completa el formulario con los detalles necesarios y haz clic en 'Enviar'. En tu menú de 'Reportes' verás todos los pqrs registrados con su estado y demás peticiones."
    ],
    [
        "pregunta" => "¿Cómo puedo ver todos los dispositivos asignados a mi usuario?",
        "respuesta" => "Para ver todos los dispositivos asignados a tu usuario, ve a tu 'Perfil de Usuario' y encontrarás una sección llamada 'Dispositivos Asignados' que muestra un resumen por tipo. Para ver el detalle completo, puedes hacer clic en 'Ver Todos' o ir directamente a la sección 'Mis Dispositivos' en el menú principal."
    ],
    [
        "pregunta" => "¿Es posible cambiar las especificaciones de un dispositivo después de registrarlo?",
        "respuesta" => "Sí, puedes actualizar las especificaciones de un dispositivo en cualquier momento. Ve a la sección 'Dispositivos', selecciona el dispositivo que deseas modificar y haz clic en 'Editar'. Actualiza los campos necesarios (procesador, RAM, almacenamiento, etc.) y guarda los cambios."
    ],
    [
        "pregunta" => "¿Qué debo hacer si necesito asistencia técnica inmediata para un dispositivo?",
        "respuesta" => "Si necesitas asistencia técnica inmediata, ve a la sección 'Soporte' y selecciona 'Soporte técnico'. Completa el formulario indicando el dispositivo afectado y la naturaleza de la emergencia."
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

        .navbar-brand,
        .nav-link {
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

        .accordion-item {
            border: 1px solid rgba(0, 0, 0, .125);
            margin-bottom: 5px;
        }

        .accordion-button {
            font-weight: 500;
        }

        .accordion-body {
            background-color: #f8f9fa;
            padding: 1.25rem;
        }

        .faq-category {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #764ba2;
            font-weight: 600;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            border-radius: 20px;
            padding-left: 40px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>');
            background-repeat: no-repeat;
            background-position: 15px center;
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
        <h2 class="mb-4">Preguntas Frecuentes</h2>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Guía de uso del sitio</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p>Bienvenido a nuestra sección de Preguntas Frecuentes. Aquí encontrarás respuestas a las dudas más
                        comunes sobre el uso de nuestra plataforma de Control de Stock. Si no encuentras la información
                        que buscas, no dudes en contactar a nuestro equipo de soporte.</p>
                </div>

                <div class="search-container">
                    <input type="text" id="searchFAQ" class="form-control search-input"
                        placeholder="Buscar preguntas...">
                </div>

                <div class="accordion" id="accordionFAQ">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>"
                                    aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    aria-controls="collapse<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($faq['pregunta']); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>"
                                class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>"
                                aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionFAQ">
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
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2    "></i>Volver al
                Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchFAQ');
            const accordionItems = document.querySelectorAll('.accordion-item');

            searchInput.addEventListener('keyup', function () {
                const searchTerm = this.value.toLowerCase();

                accordionItems.forEach(function (item) {
                    const question = item.querySelector('.accordion-button').textContent.toLowerCase();
                    const answer = item.querySelector('.accordion-body').textContent.toLowerCase();

                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>