<?php
$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";

echo '
<!-- Botón para abrir el chatbot -->
<button id="chatbotToggle" class="chatbot-toggle">
    <i class="fas fa-question"></i>
</button>

<!-- Widget del chatbot -->
<div id="chatbotWidget" class="chatbot-widget">
    <div class="chatbot-container">
        <div class="chatbot-header">
            <i class="fas fa-robot me-2"></i> Asistente Virtual
        </div>
        <div id="chatbotMessages" class="chatbot-messages">
            <!-- Aquí se mostrarán los mensajes -->
        </div>
        <div class="chatbot-input">
            <input type="text" id="userInput" placeholder="Escribe tu pregunta...">
            <button id="sendButton"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>
';

if ($is_admin) {
    echo '
    <div class="sidebar-heading">Administración Avanzada</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="admin_auditoria.php">
                <i class="fas fa-history"></i> Auditoría
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_backup.php">
                <i class="fas fa-database"></i> Sistema de Backup
            </a>
        </li>
    </ul>
    ';
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="css/chatbot.css">
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