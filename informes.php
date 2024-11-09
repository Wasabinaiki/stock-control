<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Usar $_SESSION["id_usuario"] en lugar de $_GET["id_usuario"]
$id_usuario = $_SESSION["id_usuario"];

// Crear la tabla si no existe
$sql_create_table = "CREATE TABLE IF NOT EXISTS informes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    titulo VARCHAR(255),
    contenido TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($link, $sql_create_table);

// Consulta para seleccionar los informes del usuario
$sql = "SELECT * FROM informes WHERE id_usuario = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Mis Informes</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_creacion']); ?></td>
                        <td>
                            <a href="ver_informe.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Ver</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>