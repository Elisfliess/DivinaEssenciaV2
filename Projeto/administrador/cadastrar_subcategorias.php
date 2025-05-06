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
    $idcategoria = $_POST['idcategoria'];
    $nome = $_POST['nome'];

    try {
        $sql = "INSERT INTO subcategoria (id_categoria, nome) VALUES (:idcategoria, :nome);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idcategoria', $idcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->execute();

        $sub_id = $pdo->lastInsertId();
        echo "<p style='color:green;'>Subcategoria cadastrada com sucesso! ID: " . $sub_id . "</p>";
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
        <label for="idcategoria">Categoria:</label>
    <select name="idcategoria" id="idcategoria" required>
        <option value="">Selecione a categoria</option>
        <?php
        $stmt = $pdo->query("SELECT id_categoria, nome FROM categoria");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$row['id_categoria']}'>{$row['nome']}</option>";
        }
        ?>
    </select><br>
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
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const nomeInput = document.getElementById("nome");

        nomeInput.addEventListener("input", function() {
            // Remove caracteres que não sejam letras (incluindo acentuação) ou espaço
            this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');

            // Converte tudo para maiúsculas
            this.value = this.value.toUpperCase();
        });
    });
</script>

</form>
</body>
</html>
