<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Definir el mensaje de confirmación
$mensaje = "";
$tipo_mensaje = "success";

// Validar si se recibió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $email_notificaciones = $_POST['email_notificaciones'] ?? '';
    $limite_dispositivos = $_POST['limite_dispositivos'] ?? '';
    $recibir_notificaciones = isset($_POST['recibir_notificaciones']) ? 1 : 0;

    // Obtener el ID del usuario
    $usuario_id = $_SESSION['id']; // Usando la clave correcta de la sesión

    // Actualizar el email del usuario
    $sql = "UPDATE usuarios SET email = ? WHERE id_usuario = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $email_notificaciones, $usuario_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "¡Cambios guardados exitosamente!";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Hubo un error al guardar los cambios: " . mysqli_error($link);
            $tipo_mensaje = "danger";
        }
        mysqli_stmt_close($stmt);
    }
}

// Cerrar la conexión
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4"><i class="fas fa-check-circle me-2"></i>Resultado de la Configuración</h2>
                <!-- Mensaje de confirmación -->
                <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                    <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check' : 'exclamation'; ?>-circle me-2"></i>
                    <?php echo $mensaje; ?>
                </div>
                <!-- Botón para redirigir a la página de configuración -->
                <a href="configuracion.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Configuración
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>