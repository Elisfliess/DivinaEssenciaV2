<?php
session_start();
require_once('conexao_azure.php');

// Verifica se está logado
if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID da subcategoria foi enviado pela URL
if (!isset($_GET['id_sub'])) {
    echo "<p style='color:red;'>ID da subcategoria não especificado.</p>";
    exit();
}

$id = $_GET['id_sub'];

try {
    // Buscar dados da subcategoria com base no id_sub (não id_categoria!)
    $stmt = $pdo->prepare("SELECT * FROM subcategoria WHERE id_sub = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $subcategoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subcategoria) {
        echo "<p style='color:red;'>Subcategoria não encontrada.</p>";
        exit();
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar subcategoria: " . $e->getMessage() . "</p>";
    exit();
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idcategoria = $_POST['idcategoria'];
    $nome = $_POST['nome'];

    try {
        $stmt = $pdo->prepare("UPDATE subcategoria SET nome = :nome, id_categoria = :idcategoria WHERE id_sub = :id");
        $stmt->bindParam(':idcategoria', $idcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<p style='color:green;'>Subcategoria atualizada com sucesso!</p>";
        header("Location: listar_subcategorias.php");
        exit();

    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao atualizar subcategoria: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Subcategoria</title>
</head>
<body>
<h2>Editar Subcategoria</h2>
<form method="post">
    <label for="idcategoria">Categoria:</label>
    <select name="idcategoria" id="idcategoria" required>
        <option value="">Selecione a categoria</option>
        <?php
        $stmt = $pdo->query("SELECT id_categoria, nome FROM categoria");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($row['id_categoria'] == $subcategoria['id_categoria']) ? "selected" : "";
            echo "<option value='{$row['id_categoria']}' $selected>{$row['nome']}</option>";
        }
        ?>
    </select><br>

    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($subcategoria['nome']); ?>" required><br>

    <p><button type="submit">Salvar Alterações</button></p>
</form>

<p><a href="listar_categorias.php">Voltar para a listagem</a></p>
</body>
</html>
