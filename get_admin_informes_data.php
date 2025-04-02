<?php
session_start();
require_once "includes/config.php";
header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Obtener parámetros
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'MONTH';
$periodos_validos = ['DAY', 'WEEK', 'MONTH', 'YEAR', 'ALL'];
if (!in_array($periodo, $periodos_validos)) {
    $periodo = 'MONTH';
}

$tipo_pqrs = isset($_GET['tipo_pqrs']) ? $_GET['tipo_pqrs'] : '';

try {
    // Obtener el número de usuarios registrados
    $sql_usuarios = "SELECT COUNT(*) as total_usuarios FROM usuarios";
    $result_usuarios = mysqli_query($link, $sql_usuarios);
    $row_usuarios = mysqli_fetch_assoc($result_usuarios);
    $total_usuarios = $row_usuarios['total_usuarios'];

    // Obtener estadísticas de dispositivos
    if ($periodo === 'ALL') {
        $sql_dispositivos_total = "SELECT COUNT(*) as total FROM dispositivos";
        $sql_dispositivos_periodo = $sql_dispositivos_total;
    } else {
        $sql_dispositivos_total = "SELECT COUNT(*) as total FROM dispositivos";
        $sql_dispositivos_periodo = "SELECT COUNT(*) as total FROM dispositivos WHERE fecha_entrega >= DATE_SUB(CURDATE(), INTERVAL 1 $periodo)";
    }
    
    $result_dispositivos_total = mysqli_query($link, $sql_dispositivos_total);
    $row_dispositivos_total = mysqli_fetch_assoc($result_dispositivos_total);
    $dispositivos_total = $row_dispositivos_total['total'];
    
    $result_dispositivos_periodo = mysqli_query($link, $sql_dispositivos_periodo);
    $row_dispositivos_periodo = mysqli_fetch_assoc($result_dispositivos_periodo);
    $dispositivos_periodo = $row_dispositivos_periodo['total'];

    // Obtener estadísticas de mantenimientos
    $mantenimientos_data = [];
    $estados = ['Programado', 'En proceso', 'Completado'];
    
    foreach ($estados as $estado) {
        if ($periodo === 'ALL') {
            $sql = "SELECT COUNT(*) as total FROM mantenimientos WHERE estado = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "s", $estado);
        } else {
            $sql = "SELECT COUNT(*) as total FROM mantenimientos WHERE estado = ? AND fecha_programada >= DATE_SUB(CURDATE(), INTERVAL 1 $periodo)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "s", $estado);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $mantenimientos_data[$estado] = $row['total'] ?? 0;
    }

    // Obtener tipos de dispositivos más utilizados
    $sql_tipos = "SELECT tipo, COUNT(*) as total FROM dispositivos GROUP BY tipo ORDER BY total DESC LIMIT 5";
    $result_tipos = mysqli_query($link, $sql_tipos);
    $tipos_data = [];
    while ($row = mysqli_fetch_assoc($result_tipos)) {
        $tipos_data[] = [
            'tipo' => $row['tipo'],
            'total' => $row['total']
        ];
    }

    // Obtener usuarios con más dispositivos
    $sql_usuarios_dispositivos = "SELECT u.username, COUNT(d.id_dispositivo) as total 
                                 FROM usuarios u 
                                 LEFT JOIN dispositivos d ON u.id_usuario = d.id_usuario 
                                 GROUP BY u.id_usuario 
                                 ORDER BY total DESC 
                                 LIMIT 5";
    $result_usuarios_dispositivos = mysqli_query($link, $sql_usuarios_dispositivos);
    $usuarios_dispositivos = [];
    while ($row = mysqli_fetch_assoc($result_usuarios_dispositivos)) {
        $usuarios_dispositivos[] = $row;
    }

    // Obtener usuarios con más mantenimientos
    $sql_usuarios_mantenimientos = "SELECT u.username, COUNT(m.id) as total 
                                   FROM usuarios u 
                                   JOIN dispositivos d ON u.id_usuario = d.id_usuario 
                                   JOIN mantenimientos m ON d.id_dispositivo = m.id_dispositivo 
                                   GROUP BY u.id_usuario 
                                   ORDER BY total DESC 
                                   LIMIT 5";
    $result_usuarios_mantenimientos = mysqli_query($link, $sql_usuarios_mantenimientos);
    $usuarios_mantenimientos = [];
    while ($row = mysqli_fetch_assoc($result_usuarios_mantenimientos)) {
        $usuarios_mantenimientos[] = $row;
    }

    // Obtener estadísticas de PQRS
    $sql_pqrs_base = "SELECT tipo, estado, COUNT(*) as total FROM pqrs";
    if (!empty($tipo_pqrs)) {
        $sql_pqrs_base .= " WHERE tipo = '$tipo_pqrs'";
    }
    $sql_pqrs_base .= " GROUP BY tipo, estado";
    $result_pqrs = mysqli_query($link, $sql_pqrs_base);
    
    $pqrs_data = [
        'peticion' => ['Pendiente' => 0, 'En proceso' => 0, 'Resuelto' => 0],
        'queja' => ['Pendiente' => 0, 'En proceso' => 0, 'Resuelto' => 0],
        'reclamo' => ['Pendiente' => 0, 'en  => ['Pendiente' => 0, 'En proceso' => 0, 'Resuelto' => 0],
        'reclamo' => ['Pendiente' => 0, 'En proceso' => 0, 'Resuelto' => 0],
        'sugerencia' => ['Pendiente' => 0, 'En proceso' => 0, 'Resuelto' => 0]
    ];
    
    while ($row = mysqli_fetch_assoc($result_pqrs)) {
        $pqrs_data[$row['tipo']][$row['estado']] = $row['total'];
    }

    // Calcular tiempo promedio de respuesta para PQRS resueltas
    $sql_tiempo_respuesta = "SELECT AVG(DATEDIFF(fecha_respuesta, fecha_creacion)) as promedio 
                            FROM pqrs 
                            WHERE estado = 'resuelto' AND fecha_respuesta IS NOT NULL";
    $result_tiempo = mysqli_query($link, $sql_tiempo_respuesta);
    $row_tiempo = mysqli_fetch_assoc($result_tiempo);
    $tiempo_promedio = round($row_tiempo['promedio'] ?? 0);

    // Devolver los datos como JSON
    echo json_encode([
        'total_usuarios' => $total_usuarios,
        'dispositivos_total' => $dispositivos_total,
        'dispositivos_periodo' => $dispositivos_periodo,
        'mantenimientos' => $mantenimientos_data,
        'tipos_dispositivos' => $tipos_data,
        'usuarios_dispositivos' => $usuarios_dispositivos,
        'usuarios_mantenimientos' => $usuarios_mantenimientos,
        'pqrs_data' => $pqrs_data,
        'tiempo_promedio' => $tiempo_promedio
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($link);
?>