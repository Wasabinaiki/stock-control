<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$nombre = $apellido = $username = $email = $telefono = $area = "";
$nombre_err = $apellido_err = $username_err = $email_err = $telefono_err = $area_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["nombre"]))) {
        $nombre_err = "Por favor ingrese su nombre.";
    } else {
        $nombre = trim($_POST["nombre"]);
    }

    if (empty(trim($_POST["apellido"]))) {
        $apellido_err = "Por favor ingrese su apellido.";
    } else {
        $apellido = trim($_POST["apellido"]);
    }

    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor ingrese un nombre de usuario.";
    } else {
        $sql = "SELECT id_usuario FROM usuarios WHERE username = ? AND id_usuario != ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $param_username, $param_id);
            $param_username = trim($_POST["username"]);
            $param_id = $user_id;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Este nombre de usuario ya está en uso.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor ingrese su correo electrónico.";
    } else {
        $email = trim($_POST["email"]);
    }

    $telefono = trim($_POST["telefono"]);
    $area = trim($_POST["area"]);

    if (empty($nombre_err) && empty($apellido_err) && empty($username_err) && empty($email_err)) {
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, username = ?, email = ?, telefono = ?, area = ? WHERE id_usuario = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $param_nombre, $param_apellido, $param_username, $param_email, $param_telefono, $param_area, $param_id);

            $param_nombre = $nombre;
            $param_apellido = $apellido;
            $param_username = $username;
            $param_email = $email;
            $param_telefono = $telefono;
            $param_area = $area;
            $param_id = $user_id;

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["username"] = $username;
                $_SESSION['success_message'] = "Perfil actualizado exitosamente.";
                header("location: perfil.php");
                exit();
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
} else {
    $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $nombre = $row["nombre"];
                $apellido = $row["apellido"];
                $username = $row["username"];
                $email = $row["email"];
                $telefono = $row["telefono"];
                $area = $row["area"];
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .section-title {
            color: #764ba2;
            margin-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
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
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="wrapper">
            <h2 class="mb-4"><i class="fas fa-user-edit me-2"></i>Editar Perfil</h2>

            <h3 class="section-title"><i class="fas fa-id-card me-2"></i>Información Personal</h3>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre"
                                class="form-control <?php echo (!empty($nombre_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $nombre; ?>">
                            <span class="invalid-feedback"><?php echo $nombre_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" name="apellido" id="apellido"
                                class="form-control <?php echo (!empty($apellido_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $apellido; ?>">
                            <span class="invalid-feedback"><?php echo $apellido_err; ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text">@</span>
                        <input type="text" name="username" id="username"
                            class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $username; ?>">
                    </div>
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    <small class="text-muted">El nombre de usuario debe ser único.</small>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email"
                        class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $email; ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" name="telefono" id="telefono"
                        class="form-control <?php echo (!empty($telefono_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $telefono; ?>">
                    <span class="invalid-feedback"><?php echo $telefono_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="area">Área</label>
                    <input type="text" name="area" id="area"
                        class="form-control <?php echo (!empty($area_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $area; ?>">
                    <span class="invalid-feedback"><?php echo $area_err; ?></span>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar
                        Cambios</button>
                    <a class="btn btn-secondary" href="perfil.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>