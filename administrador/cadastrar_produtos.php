<?php
session_start();
require_once('conexao_azure.php');

if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $fornecedor = $_POST['fornecedor'];
    $descricao = $_POST['descricao'];
    $subcategoria = $_POST['subcategoria'];
    $estoque = (int) $_POST['estoque'];
    $preco = str_replace(['R$', '.', ','], ['', '', '.'], $_POST['preco']);

    // VALIDA CAMPOS OBRIGATÓRIOS
    if (empty($nome) || empty($fornecedor) || empty($descricao) || empty($subcategoria) || empty($_FILES['imagem']['name']) || $estoque === "" || $preco === "") {
        echo "<p style='color:red;'>Todos os campos são obrigatórios.</p>";
        exit();
    }

    // VALIDA FORNECEDOR
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fornecedor WHERE id_fornecedor = :id");
    $stmt->bindParam(':id', $fornecedor, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        echo "<p style='color:red;'>Fornecedor inválido.</p>";
        exit();
    }

    // VALIDA SUBCATEGORIA
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subcategoria WHERE id_sub = :id");
    $stmt->bindParam(':id', $subcategoria, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        echo "<p style='color:red;'>Subcategoria inválida.</p>";
        exit();
    }

    // PROCESSA IMAGEM
    $imagem_nome = uniqid() . '_' . basename($_FILES['imagem']['name']);
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $imagem_caminho_servidor = $upload_dir . $imagem_nome;
    $imagem_caminho_banco = 'uploads/' . $imagem_nome;

    if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem_caminho_servidor)) {
        echo "<p style='color:red;'>Erro ao fazer upload da imagem.</p>";
        exit();
    }

    try {
        $sql = "INSERT INTO produto (nome_produto, imagem, id_fornecedor, descricao, id_sub, estoque, preco) 
                VALUES (:nome, :imagem, :fornecedor, :descricao, :subcategoria, :estoque, :preco)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':imagem', $imagem_caminho_banco);
        $stmt->bindParam(':fornecedor', $fornecedor, PDO::PARAM_INT);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':estoque', $estoque);
        $stmt->bindParam(':preco', $preco);

        $stmt->execute();
        $produto_id = $pdo->lastInsertId();
        echo "<p style='color:green;'>Produto cadastrado com sucesso! ID: $produto_id</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar Produto: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>
</head>
<body>
<h2>Cadastrar Produto</h2>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" required><p>

    <label for="imagem">Imagem:</label>
    <input type="file" name="imagem" id="imagem" accept="image/*" required><p>

    <label for="fornecedor">Fornecedor:</label>
    <select name="fornecedor" id="fornecedor" required>
        <option value="">Selecione um fornecedor</option>
        <?php
        $stmt = $pdo->query("SELECT id_fornecedor, nome FROM fornecedor");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$row['id_fornecedor']}'>{$row['nome']}</option>";
        }
        ?>
    </select><p>

    <label for="descricao">Descrição:</label>
    <input type="text" name="descricao" id="descricao" required><p>

    <label for="subcategoria">Subcategoria:</label>
    <select name="subcategoria" id="subcategoria" required>
        <option value="">Selecione uma subcategoria</option>
        <?php
        $stmt = $pdo->query("SELECT id_sub, nome FROM subcategoria");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$row['id_sub']}'>{$row['nome']}</option>";
        }
        ?>
    </select><p>

    <label for="estoque">Estoque (unidades):</label>
    <input type="number" name="estoque" id="estoque" required><p>

    <label for="preco">Preço:</label>
    <input type="text" name="preco" id="preco" oninput="mascaramoeda(this)" required><p>

    <button type="submit">Cadastrar Produto</button>
</form>
<p><a href="painel_admin.php">Voltar ao Painel do Administrador</a></p>
<p><a href="listar_produtos.php">Listar Produtos</a></p>

<script>
function mascaramoeda(campo) {
    let v = campo.value.replace(/\D/g, '');
    if (v.length === 0) v = '0';
    let valor = (parseInt(v) / 100).toFixed(2);
    campo.value = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}
</script>
</body>
</html>
