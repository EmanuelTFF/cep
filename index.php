<?php
session_start();
$logado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Portal de Usuário</title>
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
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 90%;
            max-width: 350px;
        }
        .link {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }
        .link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao Portal</h1>
        <?php if ($logado): ?>
            <a href="perfil.php" class="link">Perfil</a>
            <a href="logout.php" class="link">Sair</a>
        <?php else: ?>
            <a href="login.php" class="link">Login</a>
            <a href="cadastro.php" class="link">Cadastro</a>
        <?php endif; ?>
    </div>
</body>
</html>
