<?php
include 'include/include.php'; // Inclua sua conexão com o banco de dados
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Recupera o ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Consulta para obter o nome do usuário
$stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($usuario_nome);
$stmt->fetch();
$stmt->close();

// Consulta para recuperar o endereço do usuário
$stmt = $conn->prepare("SELECT cep, logradouro, bairro, cidade, estado, numero FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($cep, $logradouro, $bairro, $cidade, $estado, $numero);

// Verifica se existe um endereço salvo
$endereco_salvo = $stmt->num_rows > 0;
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #000;
            background-image: url('https://www.transparenttextures.com/patterns/stardust.png');
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .header-icon {
            font-size: 30px;
            color: #4CAF50;
        }
        .address-box {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: left;
            margin-bottom: 20px;
        }
        .address-box p {
            margin: 5px 0;
        }
        .button, .link {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .button:hover, .link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="header-icon">🏠</span>
            <h1>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</h1>
        </div>
        
        <?php if ($endereco_salvo): ?>
            <div class="address-box">
                <h2>Seu Endereço</h2>
                <p><strong>CEP:</strong> <?php echo htmlspecialchars($cep); ?></p>
                <p><strong>Logradouro:</strong> <?php echo htmlspecialchars($logradouro); ?></p>
                <p><strong>Bairro:</strong> <?php echo htmlspecialchars($bairro); ?></p>
                <p><strong>Cidade:</strong> <?php echo htmlspecialchars($cidade); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($estado); ?></p>
                <p><strong>Número:</strong> <?php echo htmlspecialchars($numero); ?></p>
            </div>
            <a href="editar_endereco.php" class="link">Editar Endereço</a>
            <a href="excluir_endereco.php" class="link">Excluir Endereço</a>
        <?php else: ?>
            <p>Você ainda não possui um endereço salvo.</p>
            <a href="salvar_endereco.php" class="link">Salvar Endereço</a>
        <?php endif; ?>
        
        <a href="logout.php" class="link">Sair</a>
        <a href="index.php" class="link">Voltar para o inicio</a>
    </div>
</body>
</html>
