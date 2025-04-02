<?php
session_start();
require_once "includes/config.php";
header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar si se proporcionó un ID de usuario
if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'ID de usuario no proporcionado']);
    exit;
}

$id_usuario = $_GET['user_id'];

// Verificar que el usuario solo pueda acceder a sus propios datos
if ($_SESSION["id"] != $id_usuario && $_SESSION["rol"] !== "administrador") {
    echo json_encode(['error' => 'No autorizado para acceder a estos datos']);
    exit;
}

// Determinar si el usuario es administrador
$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";

try {
    // Consulta SQL base
    $sql = "SELECT m.*, d.tipo as tipo_dispositivo, d.marca, d.modelo 
            FROM mantenimientos m 
            JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo";

    // Si no es administrador, filtrar solo los mantenimientos del usuario actual
    if (!$is_admin) {
        $sql .= " WHERE d.id_usuario = " . $id_usuario;
    }

    $sql .= " ORDER BY m.fecha_programada DESC";

    $result_mantenimientos = mysqli_query($link, $sql);
    
    $mantenimientos = [];
    while ($row = mysqli_fetch_assoc($result_mantenimientos)) {
        $mantenimientos[] = $row;
    }

    // Obtener estadísticas de mantenimientos
    $sql_stats = "SELECT estado, COUNT(*) as total FROM mantenimientos m 
                 JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo";

    if (!$is_admin) {
        $sql_stats .= " WHERE d.id_usuario = " . $id_usuario;
    }

    $sql_stats .= " GROUP BY estado";
    $result_stats = mysqli_query($link, $sql_stats);

    $stats = [
        'Programado' => 0,
        'En proceso' => 0,
        'Completado' => 0
    ];

    while ($row = mysqli_fetch_assoc($result_stats)) {
        $stats[$row['estado']] = $row['total'];
    }

    // Devolver los datos como JSON
    echo json_encode([
        'mantenimientos' => $mantenimientos,
        'stats' => $stats
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($link);
?>