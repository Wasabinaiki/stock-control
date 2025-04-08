<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

$admin_id = $_SESSION["id"];
$admin_username = $_SESSION["username"];

$sql_stats = "SELECT 
    (SELECT COUNT(*) FROM usuarios) as total_usuarios,
    (SELECT COUNT(*) FROM dispositivos) as total_dispositivos,
    (SELECT COUNT(*) FROM mantenimientos) as total_mantenimientos,
    (SELECT COUNT(*) FROM pqrs) as total_pqrs,
    (SELECT COUNT(*) FROM contactos) as total_contactos";
$result_stats = mysqli_query($link, $sql_stats);
$stats = mysqli_fetch_assoc($result_stats);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 1030;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .welcome-card .card-title,
        .welcome-card .card-text {
            color: white;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding-top: 56px;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #f8f9fa;
            transition: all 0.3s;
            width: 250px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #764ba2 #f8f9fa;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: #764ba2;
            border-radius: 6px;
        }

        .sidebar-collapsed {
            margin-left: -250px;
        }

        .sidebar .nav-link {
            color: #000 !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(118, 75, 162, 0.1);
            color: #000;
        }

        .sidebar .nav-link.active {
            background-color: rgba(118, 75, 162, 0.2);
            color: #764ba2;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            color: #764ba2;
        }

        .sidebar-heading {
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 1rem 1rem 0.5rem;
            color: #764ba2;
            font-weight: bold;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }

        .main-content-expanded {
            margin-left: 0;
        }

        .toggle-sidebar {
            position: fixed;
            left: 250px;
            top: 70px;
            background: #764ba2;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            padding: 10px;
            z-index: 99;
            transition: all 0.3s;
        }

        .toggle-sidebar.collapsed {
            left: 0;
        }

        .stats-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            font-size: 2.5rem;
            color: #764ba2;
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #764ba2;
        }

        .quick-access-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .quick-access-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar-collapsed {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content-expanded {
                margin-left: 250px;
            }

            .toggle-sidebar {
                left: 0;
            }

            .toggle-sidebar.collapsed {
                left: 250px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-user-shield me-2"></i>Panel de Administración</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard Usuario</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php"><i class="fas fa-user me-2"></i>Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-heading">Administración</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="bodega.php">
                    <i class="fas fa-warehouse"></i> Bodega
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_roles.php">
                    <i class="fas fa-users"></i> Gestión de Roles
                </a>
            </li>
        </ul>
        <div class="sidebar-heading">Gestión</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_pqrs.php">
                    <i class="fas fa-clipboard-list"></i> PQRs Registrados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_mantenimientos.php">
                    <i class="fas fa-tools"></i> Mantenimientos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_envios.php">
                    <i class="fas fa-truck"></i> Envíos
                </a>
            </li>
        </ul>
        <div class="sidebar-heading">Analíticas </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_informes.php">
                    <i class="fas fa-file-alt"></i> Informes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_reportes.php">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
        </ul>
        <div class="sidebar-heading">Comunicación</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_contactos.php">
                    <i class="fas fa-envelope"></i> Formularios de Contacto
                </a>
            </li>
        </ul>
        <div class="sidebar-heading">Sistema</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_auditoria.php">
                    <i class="fas fa-history"></i> Registros de Auditoría
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_backup.php">
                    <i class="fas fa-database"></i> Sistema de Backup
                </a>
            </li>
        </ul>
        <div style="height: 20px;"></div>
    </div>

    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-chevron-left" id="toggleIcon"></i>
    </button>

    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <div class="card welcome-card mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-3">Bienvenido, <?php echo htmlspecialchars($admin_username); ?></h2>
                    <h5 class="card-subtitle mb-3">Panel de Administración del Sistema</h5>
                    <p class="card-text">
                        Desde este panel podrás gestionar todos los aspectos del sistema, incluyendo usuarios, roles,
                        mantenimientos,
                        envíos, reportes y más. Utiliza el menú lateral para navegar por las diferentes secciones
                        administrativas.
                    </p>
                </div>
            </div>

            <h3 class="mb-4"><i class="fas fa-chart-line me-2"></i>Estadísticas del Sistema</h3>
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users stats-icon"></i>
                            <div class="stats-number"><?php echo $stats['total_usuarios'] ?? 0; ?></div>
                            <h5 class="card-title">Usuarios</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-laptop stats-icon"></i>
                            <div class="stats-number"><?php echo $stats['total_dispositivos'] ?? 0; ?></div>
                            <h5 class="card-title">Dispositivos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-tools stats-icon"></i>
                            <div class="stats-number"><?php echo $stats['total_mantenimientos'] ?? 0; ?></div>
                            <h5 class="card-title">Mantenimientos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-list stats-icon"></i>
                            <div class="stats-number"><?php echo $stats['total_pqrs'] ?? 0; ?></div>
                            <h5 class="card-title">PQRs</h5>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mb-4"><i class="fas fa-bolt me-2"></i>Acceso Rápido</h3>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card quick-access-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>Bodega</h5>
                        </div>
                        <div class="card-body">
                            <p>Gestiona los dispositivos en bodega y su estado actual.</p>
                            <a href="bodega.php" class="btn btn-primary">Ir a Bodega</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card quick-access-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>PQRs Pendientes</h5>
                        </div>
                        <div class="card-body">
                            <p>Revisa y gestiona las PQRs pendientes de atención.</p>
                            <a href="admin_pqrs.php" class="btn btn-primary">Ir a PQRs</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card quick-access-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Contactos Recientes</h5>
                        </div>
                        <div class="card-body">
                            <p>Revisa los formularios de contacto recientes.</p>
                            <a href="admin_contactos.php" class="btn btn-primary">Ir a Contactos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card quick-access-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Auditoría</h5>
                        </div>
                        <div class="card-body">
                            <p>Revisa los registros de actividad del sistema.</p>
                            <a href="admin_auditoria.php" class="btn btn-primary">Ver Registros</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card quick-access-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-database me-2"></i>Backup</h5>
                        </div>
                        <div class="card-body">
                            <p>Gestiona las copias de seguridad del sistema.</p>
                            <a href="admin_backup.php" class="btn btn-primary">Ir a Backup</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebar');
            const toggleIcon = document.getElementById('toggleIcon');

            const sidebarState = localStorage.getItem('adminSidebarState');
            if (sidebarState === 'collapsed') {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.add('main-content-expanded');
                toggleBtn.classList.add('collapsed');
                toggleIcon.classList.remove('fa-chevron-left');
                toggleIcon.classList.add('fa-chevron-right');
            }

            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('sidebar-collapsed');
                mainContent.classList.toggle('main-content-expanded');
                toggleBtn.classList.toggle('collapsed');

                if (sidebar.classList.contains('sidebar-collapsed')) {
                    toggleIcon.classList.remove('fa-chevron-left');
                    toggleIcon.classList.add('fa-chevron-right');
                    localStorage.setItem('adminSidebarState', 'collapsed');
                } else {
                    toggleIcon.classList.remove('fa-chevron-right');
                    toggleIcon.classList.add('fa-chevron-left');
                    localStorage.setItem('adminSidebarState', 'expanded');
                }
            });

            function checkWidth() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('sidebar-collapsed');
                    mainContent.classList.add('main-content-expanded');
                    toggleBtn.classList.add('collapsed');
                    toggleIcon.classList.remove('fa-chevron-left');
                    toggleIcon.classList.add('fa-chevron-right');
                } else if (sidebarState !== 'collapsed') {
                    sidebar.classList.remove('sidebar-collapsed');
                    mainContent.classList.remove('main-content-expanded');
                    toggleBtn.classList.remove('collapsed');
                    toggleIcon.classList.remove('fa-chevron-right');
                    toggleIcon.classList.add('fa-chevron-left');
                }
            }

            checkWidth();

            window.addEventListener('resize', checkWidth);
        });
    </script>
    <!-- Incluir jQuery si no está ya incluido -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluir el chatbot -->
    <?php
    define('INCLUDE_CHATBOT', true);
    include 'includes/chatbot_widget.php';
    ?>
</body>

</html>