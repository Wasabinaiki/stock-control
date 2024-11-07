<?php
session_start();

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
    $eliminar_cuenta = $_POST['eliminar_cuenta'] ?? '';

    // Conectar a la base de datos (asegúrate de cambiar estos datos)
    $conexion = new mysqli("localhost", "usuario", "contraseña", "base_de_datos");

    // Verificar conexión
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Obtener el ID del usuario
    $usuario_id = $_SESSION['user_id']; // Asumiendo que tienes el ID de usuario en la sesión

    // Si se seleccionó eliminar cuenta
    if ($eliminar_cuenta == 'yes') {
        // Eliminar la cuenta del usuario
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        if ($stmt->execute()) {
            // Cerrar sesión y redirigir a login
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit;
        } else {
            $mensaje = "Hubo un error al eliminar la cuenta.";
            $tipo_mensaje = "danger";
        }
    } else {
        // Guardar la configuración (por ejemplo, notificaciones y límite de dispositivos)
        $sql = "UPDATE usuarios SET email_notificaciones = ?, limite_dispositivos = ?, recibir_notificaciones = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssii", $email_notificaciones, $limite_dispositivos, $recibir_notificaciones, $usuario_id);
        
        if ($stmt->execute()) {
            $mensaje = "¡Cambios guardados exitosamente!";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Hubo un error al guardar los cambios.";
            $tipo_mensaje = "danger";
        }
    }

    // Cerrar la conexión
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Resultado de la Configuración</h2>
    <!-- Mensaje de confirmación -->
    <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
        <?php echo $mensaje; ?>
    </div>
    <!-- Botón para redirigir a la página de configuración o login -->
    <a href="configuracion.php" class="btn btn-primary">Volver a Configuración</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
