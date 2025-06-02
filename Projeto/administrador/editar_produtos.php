<?php
session_start();
require_once('conexao_azure.php');

// Verifica se está logado
if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID do produto foi enviado
if (!isset($_GET['id_produto'])) {
    echo "<p style='color:red;'>ID do produto não especificado.</p>";
    exit();
}

$id = $_GET['id_produto'];

try {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o produto foi encontrado
    if (!$produto) {
        echo "<p style='color:red;'>Produto não encontrado.</p>";
        exit();
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar produto: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $imagem = $_POST['imagem'];
    $fornecedor = $_POST['fornecedor'];
    $descricao = $_POST['descricao'];
    $subcategoria = $_POST['subcategoria'];
    $estoque = $_POST['estoque'];
    $preco = $_POST['preco'];

    try {
        $stmt = $pdo->prepare("UPDATE produto SET nome_produto = :nome, imagem = :imagem, id_fornecedor = :fornecedor, descricao = :descricao, id_sub = :subcategoria, estoque = :estoque, preco = :preco WHERE id_produto = :id");

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':imagem', $imagem);
        $stmt->bindParam(':fornecedor', $fornecedor, PDO::PARAM_INT);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':estoque', $estoque, PDO::PARAM_INT);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: listar_produtos.php");
        exit();

    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao atualizar produto: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
</head>
<body>
<h2>Editar Produto</h2>
<form method="post">

    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($produto['nome_produto']); ?>" required>
    <p>

    <label for="imagem">Imagem:</label>
    <input type="text" name="imagem" id="imagem" value="<?php echo isset($produto['imagem']) ? htmlspecialchars($produto['imagem']) : ''; ?>" placeholder="add url" required><p>

    <label for="fornecedor">Fornecedor:</label>
    <select name="fornecedor" id="fornecedor" required>
        <option value="">Selecione um fornecedor</option>
        <?php
        $stmt = $pdo->query("SELECT id_fornecedor, nome FROM fornecedor");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($row['id_fornecedor'] == $produto['id_fornecedor']) ? 'selected' : '';
            echo "<option value='{$row['id_fornecedor']}' $selected>{$row['nome']}</option>";
        }
        ?>
    </select><p>

    <label for="descricao">Descrição:</label>
    <input type="text" name="descricao" id="descricao" value="<?php echo isset($produto['descricao']) ? htmlspecialchars($produto['descricao']) : ''; ?>" required><p>

    <label for="subcategoria">Subcategoria:</label>
    <select name="subcategoria" id="subcategoria" required>
        <option value="">Selecione uma subcategoria</option>
        <?php
        $stmt = $pdo->query("SELECT id_sub, nome FROM subcategoria");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($row['id_sub'] == $produto['id_sub']) ? 'selected' : '';
            echo "<option value='{$row['id_sub']}' $selected>{$row['nome']}</option>";
        }
        ?>
    </select><p>

    <label for="estoque">Estoque:</label>
    <input type="number" name="estoque" id="estoque" value="<?php echo isset($produto['estoque']) ? $produto['estoque'] : ''; ?>" required> unidades<p>

    <label for="preco">Preço:</label>
    <input type="text" name="preco" id="preco" value="<?php echo isset($produto['preco']) ? $produto['preco'] : ''; ?>" oninput="mascaramoeda(this)" placeholder="R$ 00,00" required><p>

    <p><button type="submit">Salvar Alterações</button></p>
</form>

<p><a href="listar_produtos.php">Voltar para a listagem</a></p>

<script>
// Função para formatar preço
function mascaramoeda(input) {
    let valor = input.value.replace(/\D/g, '');
    valor = valor.replace(/(\d)(\d{2})$/, '$1,$2');
    valor = valor.replace(/(\d)(\d{3})$/, '$1.$2');
    input.value = valor;
}
</script>
</body>
</html>
