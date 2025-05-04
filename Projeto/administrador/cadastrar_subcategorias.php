<?php
// Inicia a sessão para gerenciamento do usuário.
session_start();

// Importa a configuração de conexão com o banco de dados.
//require_once('conexao.php');
require_once('conexao.php');

// Verifica se o administrador está logado.
if (!isset($_SESSION['admin_logado'])) {
    header("Location:login.php");
    exit();
}



// Bloco que será executado quando o formulário for submetido.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegando os valores do POST.
    $subcategoria = $_POST['subcategoria'];
    $nome = $_POST['nome'];

    // Inserindo administrador no banco.
    try {
        $sql = "INSERT INTO subcategoria (id_categoria, nome) VALUES (:subcategoria, :nome);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);        
        

        $stmt->execute(); // Adicionado para executar a instrução

        // Pegando o ID do administrador inserido.
        $sub_id = $pdo->lastInsertId();

        
        echo "<p style='color:green;'>Subcategoria cadastrado com sucesso! ID: " . $sub_id . "</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar Subcategoria: " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Início do código HTML -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Subcategoria</title>

</head>
<body>
<h2>Cadastrar Subcategoria</h2>
<form action="" method="post" enctype="multipart/form-data">
    <!-- Campos do formulário para inserir informações do administrador -->
    <label for="idcategoria">IDcategoria:</label>
    <input type="number" name="subcategoria" id="subcategoria" required>
    <p>
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" required>
    <p>


    <p>
    <button type="submit">Cadastrar Subcategoria</button>
    <!-- Se você omitir o atributo type em um elemento <button> dentro de um formulário, o navegador assumirá por padrão que o botão é do tipo submit. Isso significa que, ao clicar no botão, o formulário ao qual o botão pertence será enviado. Mas é boa prática especificá-lo-->

    <p></p>
    <a href="painel_admin.php">Voltar ao Painel do Administrador</a>
    <br>
    <a href="listar_subcategorias.php">Listar Subcategoria</a>

</form>
</body>
</html>
