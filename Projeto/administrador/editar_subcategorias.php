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
     <style>
        body {
            margin: 0; 
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #d5c6e0, #f0e6f5);
            height: 100vh;
            display: flex; 
            justify-content: center; 
            align-items: center;
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
            margin-top: 15px;
            text-decoration: none;
            color: #9E7FAF;
            font-weight: bold;
            transition: color 0.3s;
        }

        a:hover {
            color: #7f6390;
        }
    </style>
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
