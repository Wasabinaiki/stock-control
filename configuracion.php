<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Si se hace click en el botón de eliminar cuenta, se eliminaría la cuenta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_cuenta']) && $_POST['eliminar_cuenta'] == 'yes') {
    // Aquí va la lógica para eliminar la cuenta de la base de datos
    // Ejemplo de código de eliminación, deberías personalizarlo según tu base de datos
    // Eliminar el usuario de la base de datos (asegúrate de hacer esto de forma segura)
    // Ejemplo: Eliminar usuario de la tabla 'usuarios' (asegúrate de usar sentencias preparadas para evitar inyecciones SQL)

    $usuario_id = $_SESSION['user_id'];  // Asumiendo que tienes el ID de usuario en la sesión

    // Aquí debería ir tu código para eliminar el usuario de la base de datos
    // Esto es solo un ejemplo de cómo podrías hacerlo (ajústalo a tu configuración de base de datos)
    $conexion = new mysqli("localhost", "usuario", "contraseña", "base_de_datos");
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();

    // Cerrar la sesión y redirigir al login
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Configuración de la App</h2>
    <form action="guardar_configuracion.php" method="post">
        <div class="mb-3">
            <label for="email_notificaciones" class="form-label">Email para Notificaciones</label>
            <input type="email" class="form-control" id="email_notificaciones" name="email_notificaciones" required>
        </div>
        <div class="mb-3">
            <label for="limite_dispositivos" class="form-label">Límite de Dispositivos por Usuario</label>
            <input type="number" class="form-control" id="limite_dispositivos" name="limite_dispositivos" required>
        </div>

        <!-- Opción de recibir notificaciones -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="recibir_notificaciones" name="recibir_notificaciones">
            <label class="form-check-label" for="recibir_notificaciones">Recibir notificaciones</label>
        </div>

        <!-- Opción de eliminar cuenta -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="eliminar_cuenta" name="eliminar_cuenta" value="yes">
            <label class="form-check-label" for="eliminar_cuenta">Eliminar mi cuenta</label>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
    </form>

    <!-- Enviar el formulario de eliminación si el checkbox de eliminar cuenta está marcado -->
    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_cuenta']) && $_POST['eliminar_cuenta'] == 'yes') : ?>
        <div class="alert alert-danger mt-4" role="alert">
            ¡Tu cuenta ha sido eliminada! Serás redirigido a la página de inicio de sesión.
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

