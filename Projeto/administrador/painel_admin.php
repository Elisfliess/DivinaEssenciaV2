
<?php
session_start(); // Iniciar a sessão

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
</head>
<body>
    <h2>Bem-vindo, Administrador!</h2>
    <a href="listar_administrador.php">
        <button>Listar Administradores</button>
    </a>
    <a href="listar_categorias.php">
        <button>Listar Categorias</button>
    </a>
    <a href="listar_fornecedores.php">
        <button>Listar Fornecedores</button>
    </a>
    <a href="listar_produtos.php">
        <button>Listar Produtos</button>
    </a>
    <a href="listar_subcategorias.php">
        <button>Listar Subcategorias</button>
    </a>
<br><br>
    <a href="../E-commerce/index.html">
        <button>Logout</button>
    </a>

</body>
</html>