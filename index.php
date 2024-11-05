<?php
require_once "includes/config.php";
redirectIfNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Control de Stock</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dispositivos.php">Dispositivos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="licencias.php">Licencias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pqrs.php">PQRS</a>
                </li>
                <?php if  (hasRole('admin')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="reportes.php">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mantenimiento.php">Mantenimiento</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION["email"]); ?></h1>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Dispositivos</h5>
                        <p class="card-text">Gestiona tus dispositivos registrados.</p>
                        <a href="dispositivos.php" class="btn btn-primary">Ver Dispositivos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Licencias</h5>
                        <p class="card-text">Administra las licencias de tus dispositivos.</p>
                        <a href="licencias.php" class="btn btn-primary">Ver Licencias</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">PQRS</h5>
                        <p class="card-text">Gestiona tus peticiones, quejas, reclamos y sugerencias.</p>
                        <a href="pqrs.php" class="btn btn-primary">Ver PQRS</a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (hasRole('admin')): ?>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reportes</h5>
                        <p class="card-text">Genera y visualiza reportes del sistema.</p>
                        <a href="reportes.php" class="btn btn-primary">Ver Reportes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mantenimiento</h5>
                        <p class="card-text">Gestiona el mantenimiento de los dispositivos.</p>
                        <a href="mantenimiento.php" class="btn btn-primary">Ver Mantenimiento</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>