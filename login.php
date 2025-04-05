<?php
session_start();
require_once "includes/config.php";

$username = $password = "";
$username_err = $password_err = "";
$login_attempts = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor ingrese su usuario o email.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor ingrese su contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id_usuario, username, password, rol FROM usuarios WHERE username = ? OR email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_email);
            $param_username = $username;
            $param_email = $username;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $rol);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["rol"] = $rol;

                            unset($_SESSION['login_attempts']);

                            if ($rol === "administrador") {
                                header("location: admin_dashboard.php");
                            } else {
                                header("location: dashboard.php");
                            }
                            exit();
                        } else {
                            $password_err = "La contraseña que has ingresado no es válida.";
                            $login_attempts++;
                            $_SESSION['login_attempts'] = $login_attempts;
                        }
                    }
                } else {
                    $username_err = "No existe cuenta registrada con ese nombre de usuario o correo electrónico.";
                    $login_attempts++;
                    $_SESSION['login_attempts'] = $login_attempts;
                }
            } else {
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);

    if ($login_attempts >= 3) {
        header("location: reset_password.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ff9f43, #1e90ff, #667eea, #764ba2);
            background-size: 400% 400%;
            animation: gradientAnimation 10s ease infinite;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            height: 50%;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            flex: 1;
            padding: 40px;
            background-color: #ffffff;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            text-align: center;
        }

        .info-container {
            flex: 1;
            padding: 40px;
            background-color: rgb(55, 19, 85);
            color: white;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            text-align: center;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo img {
            width: 150px;
            height: 150px;
            filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5));
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            position: relative;
            margin-bottom: 30px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .form-control {
            padding-left: 40px;
            padding-right: 40px;
            height: 50px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
        }

        .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: rgb(87, 27, 227);
        }

        .eye-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #764ba2;

        }

        .eye-icon i {
            font-size: 18px;
            margin-left: 210%;
        }

        .invalid-feedback {
            position: absolute;
            bottom: -20px;
            left: 0;
            font-size: 14px;
            color: red;
            min-height: 20px;
        }

        .form-container form {
            max-width: 400px;
            margin: 0 auto;
        }

        .form-group+.form-group {
            margin-top: 20px;
        }

        .links {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2><i class="fas fa-sign-in-alt me-2" style="margin-bottom: 5%;"></i>Iniciar Sesión</h2>
            <?php
            if ($login_attempts > 0) {
                echo "<div class='alert alert-warning' role='alert'>
                        <i class='fas fa-exclamation-triangle me-2'></i>Intentos fallidos: $login_attempts
                      </div>";
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <input type="text" name="username"
                        class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $username; ?>" placeholder="Usuario o Correo Electrónico">
                    <i class="fas fa-user icon"></i>
                    <div class="invalid-feedback"><?php echo $username_err; ?></div>
                </div>
                <div class="form-group position-relative">
                    <input type="password" name="password"
                        class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                        placeholder="Contraseña">
                    <i class="fas fa-lock icon"></i>
                    <span class="eye-icon show-password" onclick="togglePasswordVisibility(this)">
                        <i class="fas fa-eye-slash"></i>
                    </span>
                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                    </button>
                </div>
                <div class="links">
                    <a href="register.php" style="margin-right: 14%;">Regístrate</a>
                    <a href="reset_password.php">Recupera tu contraseña</a>
                </div>
            </form>
        </div>

        <div class="info-container">
            <div class="logo">
                <img src="Stock Control Logo Curved.png" alt="Logo StockControl">
            </div>
            <h1 style="margin-bottom: 5%;">Bienvenido a Stock Control</h1>
            <p style="margin-bottom: 6%;">Gestiona fácilmente tus equipos de manera rápida y eficaz con la disposición
                de métricas y analíticas a tu disposición.</p>
            <p style="font-size: 140%;">¡Optimiza tu control de dispositivos hoy mismo!</p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(icon) {
            const passwordInput = document.querySelector('input[name="password"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.innerHTML = '<i class="fas fa-eye"></i>';
            } else {
                passwordInput.type = 'password';
                icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
            }
        }
    </script>
</body>

</html>