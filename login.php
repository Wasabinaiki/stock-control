<?php
session_start();
require_once "includes/config.php";

$username = $password = "";
$username_err = $password_err = "";
$login_attempts = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese su usuario.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese su contraseña.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id_usuario, username, password, rol FROM usuarios WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $rol);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["rol"] = $rol;
                            
                            unset($_SESSION['login_attempts']);
                            
                            if($rol === "administrador"){
                                header("location: admin_dashboard.php");
                            } else {
                                header("location: dashboard.php");
                            }
                            exit();
                        } else{
                            $password_err = "La contraseña que has ingresado no es válida.";
                            $login_attempts++;
                            $_SESSION['login_attempts'] = $login_attempts;
                        }
                    }
                } else{
                    $username_err = "No existe cuenta registrada con ese nombre de usuario.";
                    $login_attempts++;
                    $_SESSION['login_attempts'] = $login_attempts;
                }
            } else{
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($link);
    
    if($login_attempts >= 3){
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .wrapper {
            width: 380px;
            padding: 40px;
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .wrapper h2 {
            font-size: 2em;
            margin-bottom: 1em;
            color: #333;
            text-align: center;
        }
        .wrapper .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        .wrapper .form-group input {
            height: 50px;
            padding-left: 45px;
            font-size: 16px;
            border: none;
            border-bottom: 2px solid #ddd;
            background: transparent;
            transition: all 0.3s ease;
        }
        .wrapper .form-group input:focus {
            border-color: #764ba2;
            box-shadow: none;
        }
        .wrapper .form-group i {
            position: absolute;
            left: 15px;
            top: 17px;
            color: #764ba2;
            font-size: 18px;
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
        .alert {
            border-radius: 8px;
            font-size: 14px;
        }
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><i class="fas fa-lock me-2"></i>Iniciar Sesión</h2>
        <?php 
        if($login_attempts > 0){
            echo "<div class='alert alert-warning' role='alert'>
                    <i class='fas fa-exclamation-triangle me-2'></i>Intentos fallidos: $login_attempts
                  </div>";
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" placeholder="Usuario">
                <i class="fas fa-user"></i>
                <div class="invalid-feedback"><?php echo $username_err; ?></div>
            </div>    
            <div class="form-group">
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Contraseña">
                <i class="fas fa-lock"></i>
                <div class="invalid-feedback"><?php echo $password_err; ?></div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                </button>
            </div>
            <div class="links">
                <a href="register.php">¿No tienes una cuenta? Regístrate</a>
                <a href="reset_password.php">¿Olvidaste tu contraseña?</a>
            </div>
        </form>
    </div>    
</body>
</html>