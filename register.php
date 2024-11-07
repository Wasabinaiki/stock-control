<?php
require_once "includes/config.php";
 
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validar username
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese un nombre de usuario.";
    } else{
        $sql = "SELECT id_usuario FROM usuarios WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Este nombre de usuario ya está en uso.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Validar email
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor ingrese un email.";
    } else{
        $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Este email ya está registrado.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Validar password
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese una contraseña.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validar confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Por favor confirme la contraseña.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }
    
    // Verificar los errores de entrada antes de insertar en la base de datos
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        
        $sql = "INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);
            
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: login.php");
            } else{
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }

            mysqli_stmt_close($stmt);
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
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .wrapper {
            width: 100%;
            max-width: 450px;
            padding: 35px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
        }
        .wrapper h2 {
            font-size: 2em;
            margin-bottom: 1em;
            color: #333;
            text-align: center;
        }
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        .form-control {
            height: 50px;
            padding-left: 45px;
            font-size: 16px;
            border: none;
            border-bottom: 2px solid #ddd;
            background: transparent;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: none;
        }
        .form-group i {
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
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .invalid-feedback {
            display: block;
            margin-top: 5px;
            font-size: 14px;
        }
        .form-text {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><i class="fas fa-user-plus me-2"></i>Registro</h2>
        <p class="text-center mb-4">Por favor, complete este formulario para crear una cuenta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" placeholder="Usuario">
                <div class="invalid-feedback"><?php echo $username_err; ?></div>
            </div>    
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" placeholder="Email">
                <div class="invalid-feedback"><?php echo $email_err; ?></div>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Contraseña">
                <div class="invalid-feedback"><?php echo $password_err; ?></div>
            </div>
            <div class="form-group">
                <i class="fas fa-check-circle"></i>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" placeholder="Confirmar Contraseña">
                <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                </button>
            </div>
            <p class="text-center mt-3">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
        </form>
    </div>    
</body>
</html>