<?php
session_start();
require_once('conexao_azure.php');

if (!isset($_SESSION['admin_logado'])) {
    header("Location:login.php");
    exit();
}

$produtos = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM produto");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao listar produtos: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Listar Produtos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            text-decoration: none;
            display: inline-block;
            margin: 2px 0;
        }

        .action-btn:hover {
            background-color: #45a049;
        }

        .delete-btn {
            background-color: #f44336;
        }

        .delete-btn:hover {
            background-color: #da190b;
        }

        .show-btn {
            background-color: #2196F3;
        }

        .show-btn:hover {
            background-color: #0b7dda;
        }

        img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }
    </style>

    <script>
        function confirmDeletion() {
            return confirm('Tem certeza que deseja deletar este produto?');
        }
    </script>
</head>
<body>
    <h2>Produtos Cadastrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Imagem</th>
            <th>Fornecedor</th>
            <th>Descrição</th>
            <th>Subcategoria</th>
            <th>Estoque</th>
            <th>Preço</th>
            <th>Ações</th>
        </tr>
        <?php foreach($produtos as $prod): ?>
        <tr>
            <td><?php echo $prod['id_produto']; ?></td>
            <td><?php echo $prod['nome_produto']; ?></td>
            <td>
                <?php if (!empty($prod['imagem'])): ?>
                    <img src="<?php echo $prod['imagem']; ?>" alt="Imagem do Produto">
                <?php else: ?>
                    Sem imagem
                <?php endif; ?>
            </td>
            <td><?php echo $prod['id_fornecedor']; ?></td>
            <td><?php echo $prod['descricao']; ?></td>
            <td><?php echo $prod['id_sub']; ?></td>
            <td><?php echo $prod['estoque'] . ' unidades'; ?></td>
            <td><?php echo 'R$ ' . number_format($prod['preco'], 2, ',', '.'); ?></td>
            <td>
                <a href="mostrar_produto.php?id_produto=<?php echo $prod['id_produto']; ?>" class="action-btn show-btn">Mostrar</a>
                <a href="editar_produtos.php?id_produto=<?php echo $prod['id_produto']; ?>" class="action-btn">Editar</a>
                <a href="excluir_produtos.php?id_produto=<?php echo $prod['id_produto']; ?>" class="action-btn delete-btn" onclick="return confirmDeletion();">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="exportar_excel.php?tipo=produto" target="_blank">Exportar Produtos</a><br>
      <p></p>
    <a href="cadastrar_produtos.php">Cadastrar novo produto</a></p>
      <p></p>
    <a href="painel_admin.php">Voltar ao Painel do Administrador</a></p>
</body>
</html>
