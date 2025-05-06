<?php
// Inicia a sessão para gerenciamento do usuário.
session_start();

// Importa a configuração de conexão com o banco de dados.
require_once('conexao.php');

// Verifica se o administrador está logado.
if (!isset($_SESSION['admin_logado'])) {
    header("Location:login.php");
    exit();
}



// Bloco que será executado quando o formulário for submetido.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Administrador</title>

</head>
<body>
<h2>Cadastrar Administrador</h2>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" placeholder="Fulano da Silva" required>
    <p>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" placeholder="Fulano@divina.com" required><br>
    <p>
    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" placeholder="i@3$5" required> 
    <label for="mostrarsenha">Mostrar senha:</label>
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
   //mascara
</script>
</form>
</body>
</html>
