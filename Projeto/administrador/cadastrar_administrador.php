<?php
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
    $email = $_POST['email'] . '@divina.com';
    $senha = $_POST['senha'];
    $ativo = isset($_POST['ativo']) ? 1 : 0; 
    
     // Validações
    if (empty($nome) || empty($email) || empty($senha)) {
        echo "<p style='color:red;'>Todos os campos são obrigatórios.</p>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>E-mail inválido.</p>";
        exit();
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ADMINISTRADOR WHERE ADM_EMAIL = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo "<p style='color:red;'>Este e-mail já está cadastrado.</p>";
        exit();
    }

    // Inserindo administrador no banco.
    try {
        $sql = "INSERT INTO ADMINISTRADOR (ADM_NOME, ADM_EMAIL, ADM_SENHA,ADM_ATIVO) VALUES (:nome, :email, :senha, :ativo);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT); 

        $stmt->execute(); 

        $adm_id = $pdo->lastInsertId();

        
        echo "<p style='color:green;'>Administrador cadastrado com sucesso! ID: " . $adm_id . "</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar Administrador: " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Início do código HTML -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Cadastro de Administrador</title>
          <link rel="stylesheet" href="../css/style.css">
        <link rel="icon" type="image/png" href="../img/logo.png">

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


<!-- MENU HAMBURGUER-->


<div class="hamburguer">
        <button class="menu-btn">&#9776;</button>
        <img class="imglogo" src="../img/Logo.png" alt="Logo" class="logo">
        <nav class="nav">
            <ul>
                <li class="category"><a href="#">ADMINISTRADOR</a>
                    <ul class="submenu">
                        <li><a href="./listar_administrador.php">LISTAR</a></li>
                        <li><a href="./cadastrar_administrador.php">CADASTRAR</a></li>
                    </ul>
                </li>
                <li class="category"><a href="#">CATEGORIA</a>
                    <ul class="submenu">
                        <li><a href="listar_categorias.php">LISTAR</a></li>
                        <li><a href="./cadastrar_categorias.php">CADASTRAR</a></li>
                       
                    </ul>
                </li>
                <li class="category"><a href="#">FORNECEDOR</a>
                    <ul class="submenu">
                        <li><a href="listar_fornecedores.php">LISTAR</a></li>
                        <li><a href="./cadastrar_fornecedores.php">CADASTRAR</a></li>
                       
                    </ul>
                </li>
                <li class="category"><a href="#">PRODUTO</a>
                    <ul class="submenu">
                        <li><a href="listar_produtos.php">LISTAR</a></li>
                        <li><a href="./cadastrar_produtos.php">CADASTRAR</a></li>
                        
                    </ul>
                </li>
                <li class="category"><a href="#">SUBCATEGORIA</a>
                    <ul class="submenu">
                        <li><a href="listar_subcategorias.php">LISTAR</a></li>
                        <li><a href="./cadastrar_subcategorias.php">CADASTRAR</a></li>
                        
                    </ul>
                </li>
            </ul>
        </nav>
    </div>

    <script>
        const menuBtn = document.querySelector('.menu-btn');
        const nav = document.querySelector('.nav');

        menuBtn.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    </script>



<h2>Cadastrar Administrador</h2>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" placeholder="Fulano da Silva" required>
    <p>
    <label for="email">Email:</label>
    <input type="text" name="email" id="email" placeholder="fulano.silva" required>@divina.com<br>
    <p>
    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" placeholder="i@3$5" required> 
    <input type="checkbox" id="mostrar_senha" onclick="mostrarSenha()"> Mostrar senha
 <p>
    <label for="ativo">Ativo:</label>
    <input type="checkbox" name="ativo" id="ativo" value="1" checked>
    <p>
    <p>
    <button type="submit">Cadastrar Administrador</button>
    <p></p>
    <a href="painel_admin.php">Voltar ao Painel do Administrador</a>
    <br>
    <a href="listar_administrador.php">Listar Administrador</a>
<script>
    function mostrarSenha() {
        var inputSenha = document.getElementById("senha");
        if (inputSenha.type === "password") {
            inputSenha.type = "text";
        } else {
            inputSenha.type = "password";
        }
    }

    document.querySelector('form').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value;

    if (!nome || !email || !senha) {
        alert('Todos os campos são obrigatórios!');
        e.preventDefault();
        return;
    }

    if (email.includes('@')) {
        alert('Digite apenas a parte antes do @ no campo de e-mail.');
        e.preventDefault();
        return;
    }

    if (senha.length < 4) {
        alert('A senha deve ter pelo menos 4 caracteres.');
        e.preventDefault();
        return;
    }
});


</script>
</form>
</body>
</html>
