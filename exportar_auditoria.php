<?php
session_start();
require_once "includes/config.php";
require_once "includes/audit_functions.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

registrar_generacion_reporte($_SESSION["id"], "Auditoría", "Formato: " . ($_GET['formato'] ?? 'desconocido'));

$filtro_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$filtro_accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$filtro_tabla = isset($_GET['tabla']) ? $_GET['tabla'] : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'csv';

$sql_where = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($filtro_usuario)) {
    $sql_where .= " AND (u.username LIKE ? OR u.id_usuario = ?)";
    $params[] = "%$filtro_usuario%";
    $params[] = $filtro_usuario;
    $types .= "si";
}

if (!empty($filtro_accion)) {
    $sql_where .= " AND a.accion LIKE ?";
    $params[] = "%$filtro_accion%";
    $types .= "s";
}

if (!empty($filtro_tabla)) {
    $sql_where .= " AND a.tabla = ?";
    $params[] = $filtro_tabla;
    $types .= "s";
}

if (!empty($filtro_fecha_desde)) {
    $sql_where .= " AND DATE(a.fecha_hora) >= ?";
    $params[] = $filtro_fecha_desde;
    $types .= "s";
}

if (!empty($filtro_fecha_hasta)) {
    $sql_where .= " AND DATE(a.fecha_hora) <= ?";
    $params[] = $filtro_fecha_hasta;
    $types .= "s";
}

$sql = "SELECT a.id_auditoria, u.username, a.accion, a.tabla, a.id_registro, a.ip_usuario, a.fecha_hora, a.detalles 
        FROM auditoria a 
        JOIN usuarios u ON a.id_usuario = u.id_usuario 
        $sql_where 
        ORDER BY a.fecha_hora DESC";

$stmt = mysqli_prepare($link, $sql);
if (!empty($types)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=auditoria_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');

    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, ['ID', 'Usuario', 'Acción', 'Tabla', 'ID Registro', 'IP', 'Fecha y Hora', 'Detalles']);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id_auditoria'],
            $row['username'],
            $row['accion'],
            $row['tabla'] ?? 'N/A',
            $row['id_registro'] ?? 'N/A',
            $row['ip_usuario'],
            $row['fecha_hora'],
            $row['detalles'] ?? 'N/A'
        ]);
    }

    fclose($output);
    exit;
} elseif ($formato === 'pdf') {
    header('Content-Type: text/html; charset=utf-8');

    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Reporte de Auditoría - ' . date('Y-m-d') . '</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                font-size: 14px;
            }
            @media print {
                body { margin: 0; }
                .no-print { display: none; }
            }
            table { 
                border-collapse: collapse; 
                width: 100%; 
                margin-top: 20px;
            }
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: left; 
                font-size: 12px;
            }
            th { 
                background-color: #f2f2f2; 
                color: #333;
                font-weight: bold;
            }
            h1, h2 { 
                text-align: center; 
                color: #333;
            }
            .print-button { 
                background-color: #667eea; 
                color: white; 
                padding: 10px 20px; 
                border: none; 
                border-radius: 5px;
                cursor: pointer; 
                margin: 20px 0;
                font-weight: bold;
            }
            .print-button:hover {
                background-color: #764ba2;
            }
            .instructions {
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .filters {
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .header-info {
                margin-bottom: 30px;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .page-footer {
                text-align: center;
                font-size: 12px;
                margin-top: 30px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="no-print">
            <button class="print-button" onclick="window.print()">Imprimir / Guardar como PDF</button>
            <div class="instructions">
                <p><strong>Instrucciones:</strong> Para guardar como PDF, haz clic en el botón "Imprimir / Guardar como PDF" y selecciona "Guardar como PDF" en las opciones de impresión de tu navegador.</p>
            </div>
        </div>
        
        <div class="header-info">
            <div class="logo">
                <h1>Sistema de Control de Stock</h1>
            </div>
            <h1>Reporte de Auditoría</h1>
            <h2>Fecha de generación: ' . date('d/m/Y H:i:s') . '</h2>
        </div>';

    echo '<div class="filters">';
    echo '<h3>Filtros aplicados:</h3>';
    $filtros_texto = [];
    if (!empty($filtro_usuario))
        $filtros_texto[] = "Usuario: $filtro_usuario";
    if (!empty($filtro_accion))
        $filtros_texto[] = "Acción: $filtro_accion";
    if (!empty($filtro_tabla))
        $filtros_texto[] = "Tabla: $filtro_tabla";
    if (!empty($filtro_fecha_desde))
        $filtros_texto[] = "Fecha desde: $filtro_fecha_desde";
    if (!empty($filtro_fecha_hasta))
        $filtros_texto[] = "Fecha hasta: $filtro_fecha_hasta";

    if (empty($filtros_texto)) {
        echo '<p>Sin filtros</p>';
    } else {
        echo '<p>' . implode(' | ', $filtros_texto) . '</p>';
    }
    echo '</div>';

    echo '<table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Tabla</th>
                <th>ID Registro</th>
                <th>IP</th>
                <th>Fecha y Hora</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>';

    mysqli_data_seek($result, 0);
    $row_count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $row_count++;
        $row_class = $row_count % 2 == 0 ? 'even-row' : 'odd-row';
        echo '<tr class="' . $row_class . '">
            <td>' . $row['id_auditoria'] . '</td>
            <td>' . htmlspecialchars($row['username']) . '</td>
            <td>' . htmlspecialchars($row['accion']) . '</td>
            <td>' . htmlspecialchars($row['tabla'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($row['id_registro'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($row['ip_usuario']) . '</td>
            <td>' . date('d/m/Y H:i:s', strtotime($row['fecha_hora'])) . '</td>
            <td>' . htmlspecialchars(strlen($row['detalles'] ?? '') > 30 ? substr($row['detalles'], 0, 27) . '...' : ($row['detalles'] ?? 'N/A')) . '</td>
        </tr>';
    }

    echo '</tbody>
    </table>';

    echo '<div class="summary">
        <p>Total de registros: ' . $row_count . '</p>
    </div>';

    echo '<div class="page-footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Control de Stock.</p>
        <p>© ' . date('Y') . ' - Todos los derechos reservados</p>
    </div>';

    echo '</body>
    </html>';
    exit;
} else {
    header("location: admin_auditoria.php");
    exit;
}
?>