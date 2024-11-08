<?php
// terminos.php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            position: relative;
        }
        .home-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
        .back-icon {
            position: absolute;
            left: 15px;
            bottom: 15px;
        }
        .content {
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: calc(100vh - 60px);
        }
        .icon {
            width: 30px;
            height: 30px;
        }
        a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="dashboard.php" class="home-icon">🏠</a>
        <h1 class="text-center mb-0">TÉRMINOS Y CONDICIONES</h1>
        <a href="#" class="float-end">⚙️</a>
    </div>
    
    <div class="content">
        <div class="container">
            <p class="text-justify">
                Al utilizar nuestra aplicación, aceptas nuestros términos y condiciones en relación con la compra y el uso de nuestros productos de ropa para mujer. Todos los precios, promociones y disponibilidad de productos están sujetos a cambios sin previo aviso. Se aceptan devoluciones dentro de los 30 días posteriores a la compra, siempre que los artículos se encuentren en su estado original. Al utilizar esta aplicación, también aceptas la recopilación de datos personales para el procesamiento de pedidos y fines de marketing de acuerdo con nuestra Política de privacidad.
            </p>
        </div>
    </div>
    
    <a href="javascript:history.back()" class="back-icon">⬅️</a>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>