<?php
session_start();
require_once "includes/config.php";
header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'ID de usuario no proporcionado']);
    exit;
}

$id_usuario = $_GET['user_id'];

if ($_SESSION["id"] != $id_usuario && $_SESSION["rol"] !== "administrador") {
    echo json_encode(['error' => 'No autorizado para acceder a estos datos']);
    exit;
}

$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";

try {
    $sql = "SELECT m.*, d.tipo as tipo_dispositivo, d.marca, d.modelo 
            FROM mantenimientos m 
            JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo";

    if (!$is_admin) {
        $sql .= " WHERE d.id_usuario = " . $id_usuario;
    }

    $sql .= " ORDER BY m.fecha_programada DESC";

    $result_mantenimientos = mysqli_query($link, $sql);

    $mantenimientos = [];
    while ($row = mysqli_fetch_assoc($result_mantenimientos)) {
        $mantenimientos[] = $row;
    }

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

    echo json_encode([
        'mantenimientos' => $mantenimientos,
        'stats' => $stats
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($link);
?>