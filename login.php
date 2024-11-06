<?php
// login.php
session_start();
require_once "includes/config.php";

// Inicializar variables
$username = $password = "";
$username_err = $password_err = $login_err = "";
$attempts = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;

// Procesar datos del formulario cuando se envía
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Verificar si se han excedido los intentos
    if($attempts >= 3) {
        $login_err = "Has excedido el número máximo de intentos. Por favor, restablece tu contraseña.";
    } else {
        // Validar username
        if(empty(trim($_POST["username"]))){
            $username_err = "Por favor ingrese su nombre de usuario.";
        } else{
            $username = trim($_POST["username"]);
        }
        
        // Validar password
        if(empty(trim($_POST["password"]))){
            $password_err = "Por favor ingrese su contraseña.";
        } else{
            $password = trim($_POST["password"]);
        }
        
        // Validar credenciales
        if(empty($username_err) && empty($password_err)){
            $sql = "SELECT id, username, password FROM users WHERE username = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;
                
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                // Contraseña correcta, iniciar sesión
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                unset($_SESSION['login_attempts']); // Resetear intentos
                                
                                header("location: dashboard.php");
                            } else{
                                // Contraseña incorrecta
                                $attempts++;
                                $_SESSION['login_attempts'] = $attempts;
                                $login_err = "Usuario o contraseña incorrectos. Intentos restantes: " . (3 - $attempts);
                            }
                        }
                    } else{
                        $login_err = "Usuario o contraseña incorrectos.";
                    }
                } else{
                    $login_err = "Algo salió mal. Por favor, inténtalo de nuevo.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Control Stock</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e1e1e 0%, #3d0000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .btn-primary {
            background: #dc3545;
            border-color: #dc3545;
        }
        .btn-primary:hover {
            background: #c82333;
            border-color: #bd2130;
        }
        .text-white {
            color: white !important;
        }
        .alert {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: white;
        }
        .forgot-password {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }
        .forgot-password:hover {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center text-white mb-4">GESTIÓN DE EQUIPOS CONTROL STOCK</h2>
        
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        if($attempts >= 3){
            echo '<div class="alert alert-danger">Has excedido el número máximo de intentos. 
                  <a href="reset-password.php" class="text-white">Haz clic aquí para restablecer tu contraseña</a></div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Usuario" 
                       class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                       value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" 
                       class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary btn-block" value="Iniciar Sesión">
            </div>
            <p class="text-center">
                <a href="reset-password.php" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </p>
            <p class="text-center text-white">
                ¿No tienes una cuenta? <a href="register.php" class="forgot-password">Regístrate ahora</a>
            </p>
        </form>
    </div>
</body>
</html>