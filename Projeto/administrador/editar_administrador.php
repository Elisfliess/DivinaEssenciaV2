<?php
session_start();
require_once('conexao_azure.php');

// Verifica se está logado
if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID do administrador foi enviado pela URL
if (!isset($_GET['id_administrador'])) {
    echo "<p style='color:red;'>ID do administrador não especificado.</p>";
    exit();
}

$id = $_GET['id_administrador'];

try {
    // Buscar dados do administrador
    $stmt = $pdo->prepare("SELECT * FROM administrador WHERE id_administrador = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $administrador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$administrador) {
        echo "<p style='color:red;'>Administrador não encontrado.</p>";
        exit();
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar administrador: " . $e->getMessage() . "</p>";
    exit();
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['adm_nome'];
    $email = $_POST['adm_email'];
    $senha = $_POST['adm_senha'];
    $ativo = isset($_POST['adm_ativo']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE administrador  SET adm_nome = :nome, adm_email = :email, adm_senha = :senha, adm_ativo = :ativo WHERE id_administrador = :id");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<p style='color:green;'>Administrador atualizado com sucesso!</p>";
        // Redireciona de volta para a listagem após a atualização
        header("Location: listar_administrador.php");
        exit();

    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao atualizar administrador: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Administrador</title>
</head>
<body>
    <h2>Editar Administrador</h2>
    <form method="post">
        <label for="adm_nome">Nome:</label>
        <input type="text" name="adm_nome" id="adm_nome" value="<?php echo htmlspecialchars($administrador['adm_nome']); ?>" required><br><br>

        <label for="adm_email">Email:</label>
        <input type="email" name="adm_email" id="adm_email" value="<?php echo htmlspecialchars($administrador['adm_email']); ?>" required><br><br>

        <label for="adm_senha">Senha:</label>
        <input type="text" name="adm_senha" id="adm_senha" value="<?php echo htmlspecialchars($administrador['adm_senha']); ?>" required><br><br>

        <label for="adm_ativo">Ativo:</label>
        <input type="checkbox" name="adm_ativo" id="adm_ativo" <?php if ($administrador['adm_ativo'] == 1) echo 'checked'; ?>><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>

    <p></p>
    <a href="listar_administrador.php">Voltar para a listagem</a>
</body>
</html>
