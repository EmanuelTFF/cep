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
    // Exclui o endereço do usuário
    $stmt = $conn->prepare("DELETE FROM enderecos WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);

    if ($stmt->execute()) {
        $successMessage = "Endereço excluído com sucesso!";
        header("Location: perfil.php");
        exit();
    } else {
        $errorMessage = "Erro ao excluir o endereço: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Endereço</title>
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
            width: 90%;
            max-width: 400px;
            text-align: center;
        }
        button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            outline: none;
            background-color: #007BFF;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Excluir Endereço</h2>
        <?php if (isset($successMessage)) echo "<p>$successMessage</p>"; ?>
        <?php if (isset($errorMessage)) echo "<p>$errorMessage</p>"; ?>
        <form method="post">
            <p>Tem certeza de que deseja excluir seu endereço?</p>
            <button type="submit">Excluir Endereço</button>
        </form>
        <a href="perfil.php" style="color: #007BFF; text-decoration: none; margin-top: 10px;">Voltar ao Perfil</a>
    </div>
</body>
</html>
