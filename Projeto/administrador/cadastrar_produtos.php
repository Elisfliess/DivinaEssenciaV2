<?php
session_start();
require_once('conexao_azure.php');

if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $imagem = $_POST['imagem'];
    $fornecedor = $_POST['fornecedor'];
    $descricao = $_POST['descricao'];
    $subcategoria = $_POST['subcategoria'];
    $estoque = $_POST['estoque'];

$preco_raw = $_POST['preco'];
$preco_limpo = preg_replace('/[^0-9,]/', '', $preco_raw);
$preco_formatado = str_replace(',', '.', $preco_limpo);
$preco = floatval($preco_formatado);

if ($preco <= 0) {
    echo "<p style='color:red;'>Por favor, informe um valor de preço válido maior que zero.</p>";
    exit();
}

    // VALIDA CAMPOS OBRIGATÓRIOS
    if (empty($nome) || empty($fornecedor) || empty($descricao) || empty($subcategoria) || empty($estoque) || $preco_raw === '') {
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

    try {
        $sql = "INSERT INTO produto (nome_produto, imagem, id_fornecedor, descricao, id_sub, estoque, preco) 
                VALUES (:nome, :imagem, :fornecedor, :descricao, :subcategoria, :estoque, :preco)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':imagem', $imagem);
        $stmt->bindParam(':fornecedor', $fornecedor, PDO::PARAM_INT);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':estoque', $estoque, PDO::PARAM_INT);
        $stmt->bindParam(':preco', $preco); // já é float

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
     <style>
        body {
            margin: 0; padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #d5c6e0, #f0e6f5);
            height: 100vh;
            display: flex; justify-content: center; align-items: center;
        }

        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4b2d5c;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #9E7FAF;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #7f6390;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #9E7FAF;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #7f6390;
        }
        p {
            margin: 0 0 10px;
            
        }
         a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #9E7FAF;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #7f6390;
        }
        p {
            margin: 0 0 10px;
            
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #9E7FAF;
            text-decoration: none;
            display: inline-block;
            margin: 5px 0;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h2>Cadastrar Produto</h2>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" required><p>

    <label for="imagem">Imagem:</label>
    <input type="text" name="imagem" id="imagem" placeholder="add url" required><p>

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

    <label for="estoque">Estoque:</label>
    <input type="number" name="estoque" id="estoque" placeholder="50 unidades" required min="1"> unidades<p>

    <label for="preco">Preço:</label>
    <input type="text" name="preco" id="preco" oninput="mascaramoeda(this)" placeholder="R$ 00,00" required><p>

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
