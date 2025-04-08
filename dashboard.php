<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";

require_once "includes/config.php";
$usuario_id = $_SESSION["id"];

$sql_actividades = "
    (SELECT 'PQRS' as tipo, fecha_creacion as fecha, CONCAT('Registraste un ', tipo) as descripcion FROM pqrs WHERE id_usuario = ? ORDER BY fecha_creacion DESC LIMIT 3)
    UNION ALL
    (SELECT 'Dispositivo' as tipo, fecha_entrega as fecha, CONCAT('Registraste un dispositivo ', tipo, ' ', marca) as descripcion FROM dispositivos WHERE id_usuario = ? ORDER BY fecha_entrega DESC LIMIT 3)
    UNION ALL
    (SELECT 'Mantenimiento' as tipo, fecha_programada as fecha, CONCAT('Programaste un mantenimiento para el ', fecha_programada) as descripcion FROM mantenimientos m JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo WHERE d.id_usuario = ? ORDER BY fecha_programada DESC LIMIT 3)
    UNION ALL
    (SELECT 'Contacto' as tipo, fecha as fecha, CONCAT('Enviaste un formulario de contacto: ', asunto) as descripcion FROM contactos WHERE email = (SELECT email FROM usuarios WHERE id_usuario = ?) ORDER BY fecha DESC LIMIT 3)
    ORDER BY fecha DESC
    LIMIT 5
";

$stmt_actividades = mysqli_prepare($link, $sql_actividades);
mysqli_stmt_bind_param($stmt_actividades, "iiis", $usuario_id, $usuario_id, $usuario_id, $usuario_id);
mysqli_stmt_execute($stmt_actividades);
$result_actividades = mysqli_stmt_get_result($stmt_actividades);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
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

        .feature-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            color: #764ba2;
            margin-bottom: 1rem;
        }

        .activity-item {
            padding: 10px 15px;
            border-left: 3px solid #764ba2;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-radius: 0 5px 5px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
        }

        .activity-date {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .activity-icon {
            margin-right: 10px;
            color: #764ba2;
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
            <a class="navbar-brand" href="#"><i class="fas fa-laptop me-2"></i>Control de Stock</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($is_admin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-user-shield me-2"></i>Dashboard
                                Administrador</a>
                        </li>
                    <?php endif; ?>
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
        <div class="sidebar-heading">Dispositivos</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dispositivos.php?tipo=computadora">
                    <i class="fas fa-desktop"></i> Computadoras
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dispositivos.php?tipo=tablet">
                    <i class="fas fa-tablet-alt"></i> Tablets
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dispositivos.php?tipo=celular">
                    <i class="fas fa-mobile-alt"></i> Celulares
                </a>
            </li>
        </ul>
        <div class="sidebar-heading">Gestión</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="envios.php">
                    <i class="fas fa-truck"></i> Envíos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="mantenimientos.php">
                    <i class="fas fa-tools"></i> Mantenimientos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="informes.php">
                    <i class="fas fa-chart-bar"></i> Informes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reportes.php">
                    <i class="fas fa-file-alt"></i> Reportes
                </a>
            </li>
        </ul>
        <div class="sidebar-heading">Soporte</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="pqrs.php">
                    <i class="fas fa-question-circle"></i> PQRS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="faq.php">
                    <i class="fas fa-question-circle"></i> Preguntas Frecuentes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="ayuda.php">
                    <i class="fas fa-question"></i> Ayuda
                </a>
            </li>
        </ul>
        <div class="sidebar-heading">Información</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="terminos.php">
                    <i class="fas fa-file-alt"></i> Términos y Condiciones
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="privacidad.php">
                    <i class="fas fa-shield-alt"></i> Políticas de Privacidad
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="quienes_somos.php">
                    <i class="fas fa-users"></i> Quiénes Somos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contacto.php">
                    <i class="fas fa-envelope"></i> Contáctenos
                </a>
            </li>
        </ul>
        <?php if ($is_admin): ?>
            <div class="sidebar-heading">Administración</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php">
                        <i class="fas fa-user-shield"></i> Panel de Administración
                    </a>
                </li>
            </ul>
        <?php endif; ?>
        <div style="height: 20px;"></div>
    </div>
    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-chevron-left" id="toggleIcon"></i>
    </button>
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <div class="card welcome-card mb-5">
                <div class="card-body">
                    <h2 class="card-title mb-3">Bienvenido, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
                    <h5 class="card-subtitle mb-3">Panel de Control de Dispositivos</h5>
                    <p class="card-text">
                        Bienvenido a tu panel de control personalizado. Desde aquí podrás gestionar todos tus
                        dispositivos,
                        incluyendo computadoras, tablets y celulares. Además, tendrás acceso a informes, reportes,
                        facturas y
                        mucho más. Utiliza el menú lateral para navegar por las diferentes funcionalidades del sistema.
                    </p>
                </div>
            </div>
            <h3 class="mb-4">Características Destacadas</h3>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-sync-alt feature-icon"></i>
                            <h5 class="card-title">Gestión Simplificada</h5>
                            <p class="card-text">Administra todos tus dispositivos desde una única plataforma intuitiva
                                y fácil de usar.</p>
                            <a href="dispositivos.php?tipo=computadora" class="btn btn-primary mt-3">Ver
                                Dispositivos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-tools feature-icon"></i>
                            <h5 class="card-title">Mantenimientos</h5>
                            <p class="card-text">Programa y gestiona mantenimientos para tus dispositivos de forma
                                sencilla.</p>
                            <a href="mantenimientos.php" class="btn btn-primary mt-3">Ver Mantenimientos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <h5 class="card-title">Informes Detallados</h5>
                            <p class="card-text">Accede a informes y estadísticas detalladas sobre el estado y
                                rendimiento de tus dispositivos.</p>
                            <a href="informes.php" class="btn btn-primary mt-3">Ver Informes</a>
                        </div>
                    </div>
                </div>
            </div>
            <h3 class="mb-4 mt-4">Movimientos Recientes</h3>
            <div class="card">
                <div class="card-body">
                    <?php if (mysqli_num_rows($result_actividades) > 0): ?>
                        <?php while ($actividad = mysqli_fetch_assoc($result_actividades)): ?>
                            <div class="activity-item">
                                <?php
                                $icon = '';
                                switch ($actividad['tipo']) {
                                    case 'PQRS':
                                        $icon = 'fas fa-clipboard-list';
                                        break;
                                    case 'Dispositivo':
                                        $icon = 'fas fa-laptop';
                                        break;
                                    case 'Mantenimiento':
                                        $icon = 'fas fa-tools';
                                        break;
                                    case 'Contacto':
                                        $icon = 'fas fa-envelope';
                                        break;
                                    default:
                                        $icon = 'fas fa-bell';
                                }
                                ?>
                                <i class="<?php echo $icon; ?> activity-icon"></i>
                                <?php echo htmlspecialchars($actividad['descripcion']); ?>
                                <div class="activity-date">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($actividad['fecha'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">No hay actividades recientes para mostrar.</p>
                    <?php endif; ?>
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
            const sidebarState = localStorage.getItem('sidebarState');
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
                    localStorage.setItem('sidebarState', 'collapsed');
                } else {
                    toggleIcon.classList.remove('fa-chevron-right');
                    toggleIcon.classList.add('fa-chevron-left');
                    localStorage.setItem('sidebarState', 'expanded');
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