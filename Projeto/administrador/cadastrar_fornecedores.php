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
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];

// Validação básica
if (empty($nome) || empty($email) || empty($cnpj) || empty($endereco) || empty($telefone)) {
    echo "<p style='color:red;'>Todos os campos são obrigatórios.</p>";
    exit();
}

// Validação de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<p style='color:red;'>Formato de e-mail inválido.</p>";
    exit();
}

// Validação de CNPJ simples (formato numérico com 14 dígitos)
if (!preg_match('/^[0-9]{14}$/', $cnpj)) {
    echo "<p style='color:red;'>CNPJ inválido. Deve conter apenas 14 números.</p>";
    exit();
}

// Verifica se e-mail já existe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM fornecedor WHERE LOWER(email) = LOWER(:email)");
$stmt->bindParam(':email', $email);
$stmt->execute();
if ($stmt->fetchColumn() > 0) {
    echo "<p style='color:red;'>Este e-mail já está cadastrado.</p>";
    exit();
}

// Verifica se CNPJ já existe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM fornecedor WHERE cnpj = :cnpj");
$stmt->bindParam(':cnpj', $cnpj);
$stmt->execute();
if ($stmt->fetchColumn() > 0) {
    echo "<p style='color:red;'>Este CNPJ já está cadastrado.</p>";
    exit();
}

    // Inserindo administrador no banco.
    try {
        $sql = "INSERT INTO fornecedor (nome, email, cnpj, endereco, telefone) VALUES (:nome, :email, :cnpj, :endereco, :telefone);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->bindParam(':endereco', $endereco, PDO::PARAM_STR);
        $stmt->bindParam(':telefone', $telefone, PDO::PARAM_STR);

        $stmt->execute(); // Adicionado para executar a instrução

        // Pegando o ID do administrador inserido.
        $fornecedor_id = $pdo->lastInsertId();

        
        echo "<p style='color:green;'>Fornecedor cadastrado com sucesso! ID: " . $fornecedor_id . "</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar Fornecedor: " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Início do código HTML -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Fornecedor</title>

</head>
<body>
<h2>Cadastrar Fornecedor</h2>
<form action="" method="post" enctype="multipart/form-data">
    <!-- Campos do formulário para inserir informações do administrador -->
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" placeholder="Divina Essência" required>
    <p>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" placeholder="Divinaessência@fornece.com.br" required><br>
    <p>

    <label for="cnpj">CNPJ:</label>
    <input type="text" name="cnpj" id="cnpj" placeholder="00.000.000/0000-00" required><br>
    <p>

    <label for="endereco">Endereço:</label>
    <input type="text" name="endereco" id="endereco" placeholder="Rua tal, 123" required><br>
    <p>

    <label for="telefone">Telefone:</label>
    <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" required><br>
    
    
    <p>
    <button type="submit">Cadastrar Fornecedor</button>
    <!-- Se você omitir o atributo type em um elemento <button> dentro de um formulário, o navegador assumirá por padrão que o botão é do tipo submit. Isso significa que, ao clicar no botão, o formulário ao qual o botão pertence será enviado. Mas é boa prática especificá-lo-->

    <p></p>
    <a href="painel_admin.php">Voltar ao Painel do Administrador</a>
    <br>
    <a href="listar_fornecedores.php">Listar Fornecedor</a>

    <script>
    
</script>

</form>
</body>
</html>
