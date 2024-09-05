<?php
include 'include/include.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cep = $_POST["cep"] ?? null;
    $logradouro = $_POST["logradouro"] ?? null;
    $bairro = $_POST["bairro"] ?? null;
    $cidade = $_POST["cidade"] ?? null;
    $estado = $_POST["estado"] ?? null;
    $numero = $_POST["numero"] ?? null;

    if ($cep && $logradouro && $bairro && $cidade && $estado && $numero) {
        // Prepara e executa a inserção no banco de dados
        $stmt = $conn->prepare("INSERT INTO enderecos (usuario_id, cep, logradouro, bairro, cidade, estado, numero) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $usuario_id, $cep, $logradouro, $bairro, $cidade, $estado, $numero);

        if ($stmt->execute()) {
            echo 'Endereço salvo com sucesso!';
        } else {
            echo 'Erro ao salvar o endereço: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Todos os campos são necessários!';
    }

    $conn->close();
    exit();
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chatbot CEP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #000;
            background-image: url('https://www.transparenttextures.com/patterns/stardust.png'); /* Fundo de galáxia */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .chat-container {
            background-color: rgba(255, 255, 255, 0.1);
            width: 90%;
            max-width: 400px;
            height: 600px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .chat-box {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column; /* As mensagens aparecem uma abaixo da outra */
        }
        .chat-message {
            margin: 5px 0;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
            word-wrap: break-word; /* Quebra as palavras longas */
        }
        .user-message {
            background-color: #4CAF50;
            color: #fff;
            align-self: flex-end;
        }
        .bot-message {
            background-color: #333;
            color: #fff;
            align-self: flex-start;
        }
        .input-container {
            display: flex;
            border-top: 1px solid #ddd;
        }
        input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: none;
            outline: none;
            background-color: #333;
            color: white;
        }
        button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .link {
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        let addressData = {}; // Objeto para armazenar dados do endereço

        function addMessageToChat(message, className) {
            const chatBox = document.getElementById('chat-box');
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${className}`;
            messageDiv.textContent = message;
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight; // Scroll para o fim do chat
        }

        function fetchAddressData(cep) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        addressData = data; // Armazena os dados do endereço
                        addMessageToChat(`Cidade: ${data.localidade}`, 'bot-message');
                        addMessageToChat(`Estado: ${data.uf}`, 'bot-message');
                        addMessageToChat(`Logradouro: ${data.logradouro}`, 'bot-message');
                        addMessageToChat(`Bairro: ${data.bairro}`, 'bot-message');
                        addMessageToChat('Agora, por favor, digite o número da sua casa.', 'bot-message');
                    } else {
                        addMessageToChat('CEP não encontrado!', 'bot-message');
                    }
                })
                .catch(error => addMessageToChat('Erro ao buscar CEP.', 'bot-message'));
        }

        function handleUserInput() {
            const inputField = document.getElementById('user-input');
            const userMessage = inputField.value.trim();
            
            if (userMessage) {
                addMessageToChat(userMessage, 'user-message');
                
                if (!addressData.localidade) {
                    // Se os dados do endereço não foram carregados ainda, espera o CEP
                    const cep = userMessage.replace(/\D/g, '');
                    if (cep.length === 8) {
                        fetchAddressData(cep);
                    } else {
                        addMessageToChat('Por favor, digite um CEP válido (8 dígitos).', 'bot-message');
                    }
                } else if (!addressData.numero) {
                    // Após o CEP, o usuário precisa digitar o número da casa
                    addressData.numero = userMessage;
                    addMessageToChat('Você gostaria de salvar esse endereço? (Clique em Sim ou Não)', 'bot-message');
                    showSaveButtons();
                }
            }
            
            inputField.value = ''; // Limpa o campo de entrada
        }

        function showSaveButtons() {
            const buttonContainer = document.createElement('div');
            buttonContainer.innerHTML = `
                <button onclick="saveAddress(true)">Sim</button>
                <button onclick="saveAddress(false)">Não</button>
            `;
            document.getElementById('chat-box').appendChild(buttonContainer);
        }

        function saveAddress(confirm) {
            if (confirm) {
                const dataToSend = {
                    cep: addressData.cep,
                    logradouro: addressData.logradouro,
                    bairro: addressData.bairro,
                    cidade: addressData.localidade,
                    estado: addressData.uf,
                    numero: addressData.numero
                };

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(dataToSend)
                })
                .then(response => response.text())
                .then(data => addMessageToChat(data, 'bot-message'))
                .catch(error => addMessageToChat('Erro ao salvar o endereço.', 'bot-message'));
            } else {
                addMessageToChat('O seu endereço não foi salvo.', 'bot-message');
            }
        }
    </script>
</head>
<body>
    <div class="chat-container">
        <div id="chat-box" class="chat-box">
            <!-- As mensagens aparecerão aqui -->
        </div>
        <div class="input-container">
            <input type="text" id="user-input" placeholder="Digite o seu CEP...">
            <button onclick="handleUserInput()">Enviar</button>
        </div>
        <a href="index.php" class="link">Voltar para a página inicial</a>
    </div>
</body>
</html>