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
    <title>Cadastro de Categoria</title>

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
