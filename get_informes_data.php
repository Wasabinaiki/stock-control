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

try {
    // Obtener dispositivos del usuario
    $sql_dispositivos = "SELECT * FROM dispositivos WHERE id_usuario = ?";
    $stmt_dispositivos = mysqli_prepare($link, $sql_dispositivos);
    mysqli_stmt_bind_param($stmt_dispositivos, "i", $id_usuario);
    mysqli_stmt_execute($stmt_dispositivos);
    $result_dispositivos = mysqli_stmt_get_result($stmt_dispositivos);
    
    $dispositivos = [];
    while ($row = mysqli_fetch_assoc($result_dispositivos)) {
        $dispositivos[] = $row;
    }

    // Obtener mantenimientos del usuario
    $sql_mantenimientos = "SELECT m.*, d.tipo, d.marca, d.modelo 
                           FROM mantenimientos m 
                           JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo 
                           WHERE d.id_usuario = ? 
                           ORDER BY m.fecha_programada DESC";
    $stmt_mantenimientos = mysqli_prepare($link, $sql_mantenimientos);
    mysqli_stmt_bind_param($stmt_mantenimientos, "i", $id_usuario);
    mysqli_stmt_execute($stmt_mantenimientos);
    $result_mantenimientos = mysqli_stmt_get_result($stmt_mantenimientos);
    
    $mantenimientos = [];
    while ($row = mysqli_fetch_assoc($result_mantenimientos)) {
        $mantenimientos[] = $row;
    }

    // Contar dispositivos por estado
    $sql_estados = "SELECT estado, COUNT(*) as total FROM dispositivos WHERE id_usuario = ? GROUP BY estado";
    $stmt_estados = mysqli_prepare($link, $sql_estados);
    mysqli_stmt_bind_param($stmt_estados, "i", $id_usuario);
    mysqli_stmt_execute($stmt_estados);
    $result_estados = mysqli_stmt_get_result($stmt_estados);
    
    $estados = [];
    while ($row = mysqli_fetch_assoc($result_estados)) {
        $estados[] = $row;
    }

    // Devolver los datos como JSON
    echo json_encode([
        'dispositivos' => $dispositivos,
        'mantenimientos' => $mantenimientos,
        'estados' => $estados
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($link);
?>