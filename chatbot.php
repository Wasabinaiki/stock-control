<?php
session_start();
require_once "includes/config.php";
require_once "includes/chatbot_data.php";

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $response = [];

    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($accion === 'obtener_categorias') {
        $response['categorias'] = obtener_categorias_chatbot();
        $response['exito'] = true;
    } elseif ($accion === 'obtener_categoria') {
        $id_categoria = isset($_POST['id_categoria']) ? $_POST['id_categoria'] : '';
        $categoria = obtener_categoria_chatbot($id_categoria);

        if ($categoria) {
            $response['categoria'] = $categoria;
            $response['exito'] = true;
        } else {
            $response['exito'] = false;
            $response['mensaje'] = 'Categoría no encontrada';
        }
    } elseif ($accion === 'obtener_pregunta') {
        $id_categoria = isset($_POST['id_categoria']) ? $_POST['id_categoria'] : '';
        $id_pregunta = isset($_POST['id_pregunta']) ? $_POST['id_pregunta'] : '';
        $pregunta = obtener_pregunta_chatbot($id_categoria, $id_pregunta);

        if ($pregunta) {
            $response['pregunta'] = $pregunta;
            $response['exito'] = true;
        } else {
            $response['exito'] = false;
            $response['mensaje'] = 'Pregunta no encontrada';
        }
    } elseif ($accion === 'buscar') {
        $termino = isset($_POST['termino']) ? $_POST['termino'] : '';
        $resultados = buscar_preguntas_chatbot($termino);

        $response['resultados'] = $resultados;
        $response['exito'] = true;
    } else {
        $response['exito'] = false;
        $response['mensaje'] = 'Acción no válida';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .chatbot-container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .chatbot-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }

        .chatbot-messages {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message-content {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
        }

        .bot .message-content {
            background-color: #f1f0f0;
            color: #333;
        }

        .user .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .chatbot-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #e9e9e9;
        }

        .chatbot-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 10px;
        }

        .chatbot-input button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 15px;
            cursor: pointer;
        }

        .options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .option-button {
            background-color: #f1f0f0;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .option-button:hover {
            background-color: #e9e9e9;
        }

        .category-button {
            display: flex;
            align-items: center;
            background-color: #f1f0f0;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            text-align: left;
        }

        .category-button:hover {
            background-color: #e9e9e9;
        }

        .category-button i {
            margin-right: 10px;
            color: #764ba2;
        }

        .back-button {
            background-color: transparent;
            border: none;
            color: #764ba2;
            cursor: pointer;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .back-button i {
            margin-right: 5px;
        }

        .search-container {
            margin-bottom: 15px;
        }

        .search-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
        }

        .search-results {
            margin-top: 10px;
        }

        .search-result {
            background-color: #f1f0f0;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .search-result:hover {
            background-color: #e9e9e9;
        }

        .search-result-category {
            font-size: 12px;
            color: #764ba2;
            margin-bottom: 5px;
        }

        .chatbot-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .chatbot-toggle i {
            font-size: 24px;
        }

        .chatbot-widget {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            display: none;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background-color: #764ba2;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            animation: typing 1s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <button id="chatbotToggle" class="chatbot-toggle">
        <i class="fas fa-question"></i>
    </button>

    <div id="chatbotWidget" class="chatbot-widget">
        <div class="chatbot-container">
            <div class="chatbot-header">
                <i class="fas fa-robot me-2"></i> Asistente Virtual
            </div>
            <div id="chatbotMessages" class="chatbot-messages">
            </div>
            <div class="chatbot-input">
                <input type="text" id="userInput" placeholder="Escribe tu pregunta...">
                <button id="sendButton"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            let currentState = 'inicio';
            let currentCategory = null;
            let currentQuestion = null;
            let messageHistory = [];

            $('#chatbotToggle').click(function () {
                $('#chatbotWidget').toggle();
                if ($('#chatbotWidget').is(':visible') && messageHistory.length === 0) {
                    addBotMessage('¡Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?');
                    showMainMenu();
                }
            });

            $('#userInput').keypress(function (e) {
                if (e.which === 13) {
                    $('#sendButton').click();
                }
            });

            $('#sendButton').click(function () {
                const userInput = $('#userInput').val().trim();
                if (userInput) {
                    addUserMessage(userInput);
                    $('#userInput').val('');

                    if (currentState === 'busqueda') {
                        buscarPreguntas(userInput);
                    } else {
                        buscarPreguntas(userInput);
                    }
                }
            });

            function showMainMenu() {
                currentState = 'menu_principal';

                showTypingIndicator();

                setTimeout(function () {
                    hideTypingIndicator();

                    const menuMessage = `
                        <div>Puedes seleccionar una categoría para ver las preguntas frecuentes:</div>
                        <div class="options" id="categoryOptions"></div>
                        <div class="search-container mt-3">
                            <input type="text" id="searchInput" placeholder="O busca directamente tu pregunta...">
                        </div>
                    `;

                    addBotMessage(menuMessage);

                    $.ajax({
                        url: 'chatbot.php',
                        type: 'POST',
                        data: {
                            accion: 'obtener_categorias'
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.exito) {
                                const categorias = response.categorias;
                                let categoryButtons = '';

                                categorias.forEach(function (categoria) {
                                    categoryButtons += `
                                        <button class="category-button" data-category="${categoria.id}">
                                            <i class="${categoria.icono}"></i> ${categoria.nombre}
                                        </button>
                                    `;
                                });

                                $('#categoryOptions').html(categoryButtons);

                                $('.category-button').click(function () {
                                    const categoryId = $(this).data('category');
                                    showCategory(categoryId);
                                });

                                $('#searchInput').keypress(function (e) {
                                    if (e.which === 13) {
                                        const searchTerm = $(this).val().trim();
                                        if (searchTerm) {
                                            addUserMessage(searchTerm);
                                            buscarPreguntas(searchTerm);
                                        }
                                    }
                                });
                            }
                        }
                    });
                }, 1000);
            }

            function showCategory(categoryId) {
                currentState = 'categoria';
                currentCategory = categoryId;

                showTypingIndicator();

                setTimeout(function () {
                    hideTypingIndicator();

                    $.ajax({
                        url: 'chatbot.php',
                        type: 'POST',
                        data: {
                            accion: 'obtener_categoria',
                            id_categoria: categoryId
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.exito) {
                                const categoria = response.categoria;

                                let message = `
                                    <button class="back-button" id="backToMenu">
                                        <i class="fas fa-arrow-left"></i> Volver al menú
                                    </button>
                                    <div><strong>${categoria.nombre}</strong></div>
                                    <div>Selecciona una pregunta:</div>
                                    <div class="options" id="questionOptions">
                                `;

                                categoria.preguntas.forEach(function (pregunta) {
                                    message += `
                                        <button class="option-button" data-category="${categoryId}" data-question="${pregunta.id}">
                                            ${pregunta.pregunta}
                                        </button>
                                    `;
                                });

                                message += '</div>';

                                addBotMessage(message);

                                $('.option-button').click(function () {
                                    const categoryId = $(this).data('category');
                                    const questionId = $(this).data('question');
                                    showAnswer(categoryId, questionId);
                                });

                                $('#backToMenu').click(function () {
                                    showMainMenu();
                                });
                            }
                        }
                    });
                }, 1000);
            }

            function showAnswer(categoryId, questionId) {
                currentState = 'respuesta';
                currentQuestion = questionId;

                showTypingIndicator();

                setTimeout(function () {
                    hideTypingIndicator();

                    $.ajax({
                        url: 'chatbot.php',
                        type: 'POST',
                        data: {
                            accion: 'obtener_pregunta',
                            id_categoria: categoryId,
                            id_pregunta: questionId
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.exito) {
                                const pregunta = response.pregunta;

                                let message = `
                                    <button class="back-button" data-category="${categoryId}">
                                        <i class="fas fa-arrow-left"></i> Volver a ${currentCategory}
                                    </button>
                                    <div><strong>${pregunta.pregunta}</strong></div>
                                    <div class="mt-2">${pregunta.respuesta}</div>
                                    <div class="mt-3">¿Te ha sido útil esta respuesta?</div>
                                    <div class="options">
                                        <button class="option-button" id="answerYes">Sí, gracias</button>
                                        <button class="option-button" id="answerNo">No, necesito más ayuda</button>
                                    </div>
                                `;

                                addBotMessage(message);

                                $('.back-button').click(function () {
                                    const categoryId = $(this).data('category');
                                    showCategory(categoryId);
                                });

                                $('#answerYes').click(function () {
                                    addUserMessage('Sí, gracias');
                                    addBotMessage('¡Me alegra haber podido ayudarte! ¿Hay algo más en lo que pueda asistirte?');
                                    showMainMenu();
                                });

                                $('#answerNo').click(function () {
                                    addUserMessage('No, necesito más ayuda');
                                    addBotMessage('Lamento que la respuesta no haya sido suficiente. Por favor, intenta describir tu problema con más detalle o contacta con soporte técnico para recibir asistencia personalizada.');
                                    showMainMenu();
                                });
                            }
                        }
                    });
                }, 1000);
            }

            function buscarPreguntas(termino) {
                currentState = 'busqueda';

                showTypingIndicator();

                setTimeout(function () {
                    hideTypingIndicator();

                    $.ajax({
                        url: 'chatbot.php',
                        type: 'POST',
                        data: {
                            accion: 'buscar',
                            termino: termino
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.exito) {
                                const resultados = response.resultados;

                                if (resultados.length > 0) {
                                    let message = `
                                        <div>Encontré ${resultados.length} resultado(s) para "${termino}":</div>
                                        <div class="search-results">
                                    `;

                                    resultados.forEach(function (resultado) {
                                        message += `
                                            <div class="search-result" data-category="${resultado.categoria_id}" data-question="${resultado.pregunta.id}">
                                                <div class="search-result-category">${resultado.categoria}</div>
                                                <div>${resultado.pregunta.pregunta}</div>
                                            </div>
                                        `;
                                    });

                                    message += `
                                        </div>
                                        <button class="back-button" id="backToMenu">
                                            <i class="fas fa-arrow-left"></i> Volver al menú
                                        </button>
                                    `;

                                    addBotMessage(message);

                                    $('.search-result').click(function () {
                                        const categoryId = $(this).data('category');
                                        const questionId = $(this).data('question');
                                        showAnswer(categoryId, questionId);
                                    });

                                    $('#backToMenu').click(function () {
                                        showMainMenu();
                                    });
                                } else {
                                    addBotMessage(`
                                        <div>Lo siento, no encontré resultados para "${termino}".</div>
                                        <div>Por favor, intenta con otros términos o selecciona una categoría:</div>
                                        <button class="option-button" id="showCategories">Ver categorías</button>
                                    `);

                                    $('#showCategories').click(function () {
                                        showMainMenu();
                                    });
                                }
                            }
                        }
                    });
                }, 1000);
            }

            function addUserMessage(message) {
                const messageHtml = `
                    <div class="message user">
                        <div class="message-content">${message}</div>
                    </div>
                `;

                $('#chatbotMessages').append(messageHtml);
                scrollToBottom();

                messageHistory.push({
                    type: 'user',
                    content: message
                });
            }

            function addBotMessage(message) {
                const messageHtml = `
                    <div class="message bot">
                        <div class="message-content">${message}</div>
                    </div>
                `;

                $('#chatbotMessages').append(messageHtml);
                scrollToBottom();

                messageHistory.push({
                    type: 'bot',
                    content: message
                });
            }

            function showTypingIndicator() {
                const typingHtml = `
                    <div class="message bot" id="typingIndicator">
                        <div class="message-content">
                            <div class="typing-indicator">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                `;

                $('#chatbotMessages').append(typingHtml);
                scrollToBottom();
            }

            function hideTypingIndicator() {
                $('#typingIndicator').remove();
            }

            function scrollToBottom() {
                const chatMessages = document.getElementById('chatbotMessages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    </script>
</body>

</html>