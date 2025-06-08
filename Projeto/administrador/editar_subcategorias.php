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
     <link rel="stylesheet" href="../css/menu.css">

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



 <!-- MENU HAMBURGUER -->
    <button class="menu-btn" aria-label="Abrir menu" aria-expanded="false">&#9776;</button>
    
    <!-- Menu suspenso -->
    <div class="hamburguer">
        <img class="logo" src="../img/Logo.png" alt="Logo">
        <nav class="nav">
            <ul>
                <li class="category"><a href="#">ADMINISTRADOR</a>
                    <ul class="submenu">
                        <li><a href="./listar_administrador.php">LISTAR</a></li>
                        <li><a href="./cadastrar_administrador.php">CADASTRAR</a></li>
                    </ul>
                </li>
                <li class="category"><a href="#">CATEGORIA</a>
                    <ul class="submenu">
                        <li><a href="listar_categorias.php">LISTAR</a></li>
                        <li><a href="./cadastrar_categorias.php">CADASTRAR</a></li>
                    </ul>
                </li>
                <li class="category"><a href="#">FORNECEDOR</a>
                    <ul class="submenu">
                        <li><a href="listar_fornecedores.php">LISTAR</a></li>
                        <li><a href="./cadastrar_fornecedores.php">CADASTRAR</a></li>
                    </ul>
                </li>
                <li class="category"><a href="#">PRODUTO</a>
                    <ul class="submenu">
                        <li><a href="listar_produtos.php">LISTAR</a></li>
                        <li><a href="./cadastrar_produtos.php">CADASTRAR</a></li>
                    </ul>
                </li>
                <li class="category"><a href="#">SUBCATEGORIA</a>
                    <ul class="submenu">
                        <li><a href="listar_subcategorias.php">LISTAR</a></li>
                        <li><a href="./cadastrar_subcategorias.php">CADASTRAR</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>

    <!-- JavaScript para ativação do menu -->
    <script>
document.addEventListener("DOMContentLoaded", () => {
    const menuBtn = document.querySelector('.menu-btn');
    const hamburguer = document.querySelector('.hamburguer');
    const categories = document.querySelectorAll(".category");

    // Alterna o menu hambúrguer
    menuBtn.addEventListener("click", (event) => {
        hamburguer.classList.toggle("active");
        event.stopPropagation();

        const isExpanded = hamburguer.classList.contains("active");
        menuBtn.setAttribute("aria-expanded", isExpanded);
        menuBtn.innerHTML = isExpanded ? "✖" : "&#9776;";
    });

    // Submenu por categoria
    categories.forEach(category => {
        category.addEventListener("click", (event) => {
            event.stopPropagation();

            const submenu = category.querySelector(".submenu");
            const isActive = category.classList.contains("active");

            // Fecha todos
            categories.forEach(cat => {
                cat.classList.remove("active");
                const sm = cat.querySelector(".submenu");
                if (sm) {
                    sm.style.maxHeight = "0";
                    sm.style.opacity = "0";
                }
            });

            // Se não estava ativa, abre essa
            if (!isActive && submenu) {
                category.classList.add("active");
                submenu.style.maxHeight = "500px";
                submenu.style.opacity = "1";
            }
        });
    });

    // Fecha menu e submenus ao clicar fora
    document.addEventListener("click", (event) => {
        if (!hamburguer.contains(event.target) && !menuBtn.contains(event.target)) {
            hamburguer.classList.remove("active");
            menuBtn.setAttribute("aria-expanded", "false");
            menuBtn.innerHTML = "&#9776;";

            // Fecha todos submenus
            categories.forEach(category => {
                const submenu = category.querySelector(".submenu");
                if (submenu) {
                    submenu.style.maxHeight = "0";
                    submenu.style.opacity = "0";
                    category.classList.remove("active");
                }
            });
        }
    });
});
</script>

    </script>

    <!-- Fim menu Hamburguer -->
    
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
