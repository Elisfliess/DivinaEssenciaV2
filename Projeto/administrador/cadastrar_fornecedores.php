<?php
session_start();
require_once('conexao_azure.php');

if (!isset($_SESSION['admin_logado'])) {
    header("Location:login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];

    // Validação básica
    if (empty($nome) || empty($email) || empty($cnpj) || empty($endereco) || empty($telefone)) {
        echo "<p style='color:red;'>Todos os campos são obrigatórios.</p>";
        exit();
    }

    // Validação de e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Formato de e-mail inválido.</p>";
        exit();
    }


    // Validação de número no endereço
    if (!preg_match('/\d+/', $endereco)) {
        echo "<p style='color:red;'>Endereço inválido. Inclua o número (ex: Rua tal, 123).</p>";
        exit();
    }
    
    if (!preg_match('/-/', $endereco)) {
        echo "<p style='color:red;'>Endereço inválido. Certifique-se de incluir '- SP' no final (ex: Rua tal, 123 - SP).</p>";
        exit();
    }

    // Verifica se o e-mail já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fornecedor WHERE LOWER(email) = LOWER(:email)");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo "<p style='color:red;'>Este e-mail já está cadastrado.</p>";
        exit();
    }

    // Verifica se o CNPJ já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fornecedor WHERE cnpj = :cnpj");
    $stmt->bindParam(':cnpj', $cnpj);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo "<p style='color:red;'>Este CNPJ já está cadastrado.</p>";
        exit();
    }

    // Inserção no banco
    try {
        $sql = "INSERT INTO fornecedor (nome, email, cnpj, endereco, telefone) 
                VALUES (:nome, :email, :cnpj, :endereco, :telefone);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->bindParam(':endereco', $endereco, PDO::PARAM_STR);
        $stmt->bindParam(':telefone', $telefone, PDO::PARAM_STR);
        $stmt->execute();

        $fornecedor_id = $pdo->lastInsertId();
        echo "<p style='color:green;'>Fornecedor cadastrado com sucesso! ID: " . $fornecedor_id . "</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar Fornecedor: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Fornecedor</title>

 <style>
        body {
            margin: 0; padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #d5c6e0, #f0e6f5);
            height: 100vh;
            display: flex; justify-content: center; align-items: center;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4b2d5c;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .input-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }
        .input-wrapper input {
            width: 100%;
            padding-right: 120px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .input-wrapper::after {
            content: "@divina.com";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #555;
            font-size: 16px;
            pointer-events: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #9E7FAF;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #7f6390;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #9E7FAF;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #7f6390;
        }
        p {
            margin: 0 0 10px;
            
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #9E7FAF;
            text-decoration: none;
            display: inline-block;
            margin: 5px 0;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
    
</head>
<body>
<h2>Cadastrar Fornecedor</h2>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" placeholder="Divina Essência" required>
    <p>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" placeholder="Divinaessência@fornece.com.br" required><br>
    <p>

    <label for="cnpj">CNPJ:</label>
    <input type="text" name="cnpj" id="cnpj" oninput="mascaracnpj(this)" placeholder="00.000.000/0000-00" required><br>
    <p>

    <label for="endereco">Endereço:</label>
    <input type="text" name="endereco" id="endereco"7 placeholder="Rua tal, 123 - SP" required><br>
    <p>

    <label for="telefone">Telefone:</label>
    <input type="text" name="telefone" id="telefone" oninput="mascaratelefone(this)" placeholder="(00) 00000-0000" required><br>

    <p>
    <button type="submit">Cadastrar Fornecedor</button>
    <p></p>
    <a href="painel_admin.php">Voltar ao Painel do Administrador</a><br>
    <a href="listar_fornecedores.php">Listar Fornecedor</a>
</form>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const endereco = document.getElementById('endereco').value.trim();
    const temNumero = /\d/.test(endereco);
    if (!temNumero) {
        alert('Por favor, inclua o número no campo de endereço.');
        e.preventDefault();
    }
});
function mascaracnpj(campo) {
      let v = campo.value.replaceAll('.', '').replaceAll('/', '').replaceAll('-', '');
      if (v.length > 2) v = v.slice(0, 2) + '.' + v.slice(2);
      if (v.length > 6) v = v.slice(0, 6) + '.' + v.slice(6);
      if (v.length > 10) v = v.slice(0, 10) + '/' + v.slice(10);
      if (v.length > 15) v = v.slice(0, 15) + '-' + v.slice(15, 17);
      campo.value = v;
    }

    function mascaratelefone(campo) {
      let v = campo.value.replace(/\D/g, ''); // Remove tudo que não for número
      if (v.length > 2) v = '(' + v.slice(0, 2) + ') ' + v.slice(2);
      if (v.length > 7) v = v.slice(0, 10) + '-' + v.slice(10, 14);
      campo.value = v;
    }
</script>
</body>
</html>
