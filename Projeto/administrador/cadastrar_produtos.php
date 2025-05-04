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
    $nome = $_POST['nome'];
    $imagem = $_POST['imagem'];
    $fornecedor = $_POST['fornecedor'];
    $descricao = $_POST['descricao'];
    $subcategoria = $_POST['subcategoria'];
    $estoque = $_POST['estoque'];
    $preco = $_POST['preco'];




    // Inserindo administrador no banco.
    try {
        $sql = "INSERT INTO produto (nome_produto, imagem, id_fornecedor, descricao, id_sub, estoque, preco) VALUES (:nome, :imagem, :fornecedor, :descricao, :subcategoria, :estoque, :preco);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':imagem', $imagem, PDO::PARAM_STR);
        $stmt->bindParam(':fornecedor', $fornecedor, PDO::PARAM_INT);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':estoque', $estoque, PDO::PARAM_STR);
        $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
        




        $stmt->execute(); // Adicionado para executar a instrução

        // Pegando o ID do administrador inserido.
        $produto_id = $pdo->lastInsertId();

        
        echo "<p style='color:green;'>Produto cadastrado com sucesso! ID: " . $produto_id . "</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar Produto: " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Início do código HTML -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>

</head>
<body>
<h2>Cadastrar Produto</h2>
<form action="" method="post" enctype="multipart/form-data">
    <!-- Campos do formulário para inserir informações do administrador -->
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" required>
    <p>

    <label for="imagem">imagem:</label>
    <input type="text" name="imagem" id="imagem" required><br>
    <p>

    <label for="fornecedor">Fornecedor:</label>
<select name="fornecedor" id="fornecedor" required>
    <option value="">Selecione um fornecedor</option>
    <?php
    $stmt = $pdo->query("SELECT id_fornecedor, nome FROM fornecedor");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='{$row['id_fornecedor']}'>{$row['nome']}</option>";
    }
    ?>
</select><br>


    <label for="descricao">Decrição:</label>
    <input type="text" name="descricao" id="descricao" required><br>
    <p>

    <label for="subcategoria">Subcategoria:</label>
    <input type="number" name="subcategoria" id="subcategoria" required><br>
    <p>

    <label for="estoque">Estoque:</label>
    <input type="text" name="estoque" id="estoque" required><br>
    <p>
    
    <label for="preco">Preço:</label>
    <input type="number" name="preco" id="preco" required><br>
    <p>
    
    <p>
    <button type="submit">Cadastrar Produto</button>
    <!-- Se você omitir o atributo type em um elemento <button> dentro de um formulário, o navegador assumirá por padrão que o botão é do tipo submit. Isso significa que, ao clicar no botão, o formulário ao qual o botão pertence será enviado. Mas é boa prática especificá-lo-->

    <p></p>
    <a href="painel_admin.php">Voltar ao Painel do Administrador</a>
    <br>
    <a href="listar_produtos.php">Listar Produto</a>

</form>
</body>
</html>
