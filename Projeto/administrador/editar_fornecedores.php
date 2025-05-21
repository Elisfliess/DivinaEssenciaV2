<?php
session_start();
require_once('conexao_azure.php');

// Verifica se está logado
if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID do fornecedor foi enviado pela URL
if (!isset($_GET['id_fornecedor'])) {
    echo "<p style='color:red;'>ID do fornecedor não especificado.</p>";
    exit();
}

$id = $_GET['id_fornecedor'];

try {
    $stmt = $pdo->prepare("SELECT * FROM fornecedor WHERE id_fornecedor = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fornecedor) {
        echo "<p style='color:red;'>Fornecedor não encontrado.</p>";
        exit();
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar fornecedor: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];

    try {
        $stmt = $pdo->prepare("UPDATE fornecedor SET nome = :nome, email = :email, cnpj = :cnpj, endereco = :endereco, telefone = :telefone WHERE id_fornecedor = :id");
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->bindParam(':endereco', $endereco, PDO::PARAM_STR);
        $stmt->bindParam(':telefone', $telefone, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: listar_fornecedores.php");
        exit();

    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao atualizar fornecedor: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Fornecedor</title>
</head>
<body>
<h2>Editar Fornecedor</h2>
<form method="post">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($fornecedor['nome']); ?>" required>
    <p>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($fornecedor['email']); ?>" required>
    <p>

    <label for="cnpj">CNPJ:</label>
    <input type="text" name="cnpj" id="cnpj" oninput="mascaracnpj(this)" value="<?php echo htmlspecialchars($fornecedor['cnpj']); ?>" required>
    <p>

    <label for="endereco">Endereço:</label>
    <input type="text" name="endereco" id="endereco" value="<?php echo htmlspecialchars($fornecedor['endereco']); ?>" required>
    <p>

    <label for="telefone">Telefone:</label>
    <input type="text" name="telefone" id="telefone" oninput="mascaratelefone(this)" value="<?php echo htmlspecialchars($fornecedor['telefone']); ?>" required>
    <p>

    <button type="submit">Salvar Alterações</button>
</form>

<p><a href="listar_fornecedores.php">Voltar para a listagem</a></p>
</body>
</html>
