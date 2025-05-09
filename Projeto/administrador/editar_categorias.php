<?php
session_start();
require_once('conexao_azure.php');

// Verifica se está logado
if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID do administrador foi enviado pela URL
if (!isset($_GET['id_categoria'])) {
    echo "<p style='color:red;'>ID do categoria não especificado.</p>";
    exit();
}

$id = $_GET['id_categoria'];

try {
    // Buscar dados da categoria
    $stmt = $pdo->prepare("SELECT * FROM categoria WHERE id_categoria = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {
        echo "<p style='color:red;'>Categoria não encontrada.</p>";
        exit();
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar categoria: " . $e->getMessage() . "</p>";
    exit();
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];

    try {
        $stmt = $pdo->prepare("UPDATE categoria SET nome = :nome WHERE id_categoria = :id");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<p style='color:green;'>Categoria atualizada com sucesso!</p>";
        // Redireciona de volta para a listagem após a atualização
        header("Location: listar_categorias.php");
        exit();

    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao atualizar categoria: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoria</title>
</head>
<body>
<h2>Editar Categoria</h2>
<form method="post">
    <!-- Campos do formulário para editar informações da categoria -->
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
    <p>
        <button type="submit">Salvar Alterações</button>
    </p>
</form>

<p></p>
<a href="listar_categorias.php">Voltar para a listagem</a>
</body>
</html>
