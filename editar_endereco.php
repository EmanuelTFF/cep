<?php
include 'include/include.php'; 
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Recupera o endereço atual do usuário
$stmt = $conn->prepare("SELECT cep, logradouro, bairro, cidade, estado, numero FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($cep, $logradouro, $bairro, $cidade, $estado, $numero);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cep = $_POST["cep"];
    $logradouro = $_POST["logradouro"];
    $bairro = $_POST["bairro"];
    $cidade = $_POST["cidade"];
    $estado = $_POST["estado"];
    $numero = $_POST["numero"];

    // Atualiza o endereço do usuário no banco de dados
    $stmt = $conn->prepare("UPDATE enderecos SET cep = ?, logradouro = ?, bairro = ?, cidade = ?, estado = ?, numero = ? WHERE usuario_id = ?");
    $stmt->bind_param("ssssssi", $cep, $logradouro, $bairro, $cidade, $estado, $numero, $usuario_id);

    if ($stmt->execute()) {
        $successMessage = "Endereço atualizado com sucesso!";
        header("Location: perfil.php");
        exit();
    } else {
        $errorMessage = "Erro ao atualizar o endereço: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Endereço</title>
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
        input[type="text"], button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            outline: none;
            background-color: #333;
            color: #fff;
        }
        button {
            background-color: #007BFF;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Endereço</h2>
        <?php if (isset($successMessage)) echo "<p>$successMessage</p>"; ?>
        <?php if (isset($errorMessage)) echo "<p>$errorMessage</p>"; ?>
        <form method="post">
            <input type="text" name="cep" value="<?php echo htmlspecialchars($cep); ?>" placeholder="CEP">
            <input type="text" name="logradouro" value="<?php echo htmlspecialchars($logradouro); ?>" placeholder="Logradouro">
            <input type="text" name="bairro" value="<?php echo htmlspecialchars($bairro); ?>" placeholder="Bairro">
            <input type="text" name="cidade" value="<?php echo htmlspecialchars($cidade); ?>" placeholder="Cidade">
            <input type="text" name="estado" value="<?php echo htmlspecialchars($estado); ?>" placeholder="Estado">
            <input type="text" name="numero" value="<?php echo htmlspecialchars($numero); ?>" placeholder="Número">
            <button type="submit">Atualizar Endereço</button>
        </form>
        <a href="perfil.php" style="color: #007BFF; text-decoration: none; margin-top: 10px;">Voltar ao Perfil</a>
    </div>
</body>
</html>
