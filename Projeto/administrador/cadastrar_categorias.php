<?php
// Inicia a sessão para gerenciamento do usuário.
session_start();

// Importa a configuração de conexão com o banco de dados.
require_once('conexao_azure.php');

// Verifica se o administrador está logado.
if (!isset($_SESSION['admin_logado'])) {
    header("Location:login.php");
    exit();
}

// Bloco que será executado quando o formulário for submetido.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];

 // Validação: campo obrigatório
 if (empty($nome)) {
    echo "<p style='color:red;'>O nome da categoria é obrigatório.</p>";
    exit();
}

// Verifica se a categoria já existe (case insensitive)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM categoria WHERE LOWER(nome) = LOWER(:nome)");
$stmt->bindParam(':nome', $nome);
$stmt->execute();

if ($stmt->fetchColumn() > 0) {
    echo "<p style='color:red;'>Esta categoria já está cadastrada.</p>";
    exit();
}

    // Inserindo categoria no banco.
    try {
        $sql = "INSERT INTO categoria (nome) VALUES (:nome);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->execute();

        echo "<p style='color:green;'>Categoria cadastrada com sucesso!</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar Categoria: " . $e->getMessage() . "</p>";
    }
}
?>


<!-- Início do código HTML -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .cubo {
            background-color: white;
            border: 2px solid #9E7FAF;
            border-radius: 15px;
            padding: 30px 40px;
            width: 400px;
            box-shadow: 0px 0px 20px rgba(158, 127, 175, 0.3);
        }

        .cubo h2 {
            text-align: center;
            color: #9E7FAF;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        input[type="checkbox"] {
            margin-top: 10px;
        }

  
    .cubo button {
    width: 100%;
    background-color: #9E7FAF;
    color: white;
    border: none;
    padding: 12px;
    margin-top: 20px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
}



        button:hover {
            background-color: #7e5f90;
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

        .email-group {
    position: relative;
    display: flex;
    align-items: center;
}

.email-group input[type="text"] {
    padding-right: 110px;
    flex: 1;
}

.email-domain {
    position: absolute;
    right: 10px;
    color: #555;
    pointer-events: none;
    font-size: 14px;
}
    </style>

</head>
<body>
<h2>Cadastrar Categoria</h2>
<form action="" method="post" enctype="multipart/form-data">
    <!-- Campos do formulário para inserir informações do administrador -->
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" required>
    <p>

    <p>
    <button type="submit">Cadastrar Categoria</button>
    <!-- Se você omitir o atributo type em um elemento <button> dentro de um formulário, o navegador assumirá por padrão que o botão é do tipo submit. Isso significa que, ao clicar no botão, o formulário ao qual o botão pertence será enviado. Mas é boa prática especificá-lo-->

    <p></p>
    <a href="painel_admin.php">Voltar ao Painel do Administrador</a>
    <br>
    <a href="listar_categorias.php">Listar Categoria</a>

</form>
</body>
</html>
