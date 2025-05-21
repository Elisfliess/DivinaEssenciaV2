<?php
session_start();
require_once('conexao_azure.php');

if (!isset($_SESSION['admin_logado'])) {
    header("Location:login.php");
    exit();
}

if (!isset($_GET['id_produto'])) {
    echo "<p style='color:red;'>ID do produto não fornecido.</p>";
    exit();
}

$id_produto = $_GET['id_produto'];

try {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $stmt->execute([$id_produto]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        echo "<p style='color:red;'>Produto não encontrado.</p>";
        exit();
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar produto: " . $e->getMessage() . "</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Produto</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .container {
            width: 600px;
            margin: 30px auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
        }

        h2 {
            color: #333;
        }

        .info {
            margin: 10px 0;
        }

        .label {
            font-weight: bold;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }

        a:hover {
            background-color: #45a049;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detalhes do Produto</h2>

        <div class="info"><span class="label">ID:</span> <?php echo $produto['id_produto']; ?></div>
        <div class="info"><span class="label">Nome:</span> <?php echo $produto['nome_produto']; ?></div>
        
        <div class="info">
            <span class="label">Imagem:</span><br>
            <?php if (!empty($produto['imagem'])): ?>
                <img src="<?php echo $produto['imagem']; ?>" alt="Imagem do Produto">
            <?php else: ?>
                <span style="color:gray;">Sem imagem disponível</span>
            <?php endif; ?>
        </div>

        <div class="info"><span class="label">Fornecedor (ID):</span> <?php echo $produto['id_fornecedor']; ?></div>
        <div class="info"><span class="label">Descrição:</span> <?php echo $produto['descricao']; ?></div>
        <div class="info"><span class="label">Subcategoria (ID):</span> <?php echo $produto['id_sub']; ?></div>
        <div class="info"><span class="label">Estoque:</span> <?php echo $produto['estoque']; ?> unidades</div>
        <div class="info"><span class="label">Preço:</span> R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>

        <a href="listar_produtos.php">Voltar à Lista</a>
    </div>
</body>
</html>
