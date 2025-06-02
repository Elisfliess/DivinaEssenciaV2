<?php
session_start(); // Inicia a sessão

// Tenta conectar com o banco e fazer o login
try {
    require_once('conexao_azure.php'); // Inclui o arquivo de configuração da conexão

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Incluindo o schema dbo no nome da tabela
    $sql = "SELECT * FROM administrador WHERE adm_email = :email AND adm_senha = :senha AND adm_ativo = 1"; 
    $query = $pdo->prepare($sql);

    // Vincula os parâmetros para prevenir injeção de SQL
    $query->bindParam(':email', $email, PDO::PARAM_STR); 
    $query->bindParam(':senha', $senha, PDO::PARAM_STR); 
    $query->execute();

    // Verifica se o usuário existe
    if ($query->execute()) {
        //echo "<p>Consulta executada com sucesso.</p>";
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);
        // echo "<pre>"; print_r($resultados); echo "</pre>";

        if ($query->rowCount() > 0) {
            //echo "<p>Usuário encontrado. Redirecionando...</p>";
            $_SESSION['admin_logado'] = true;
            header('Location: painel_admin.php');
            exit;
        } else {
            $_SESSION['mensagem_erro'] = "Nome de usuário ou senha incorreto";
            header('Location:login.php?erro');
            exit;
        }
    } else {
        $_SESSION['mensagem_erro'] = "NOME DE USUÁRIO OU SENHA INCORRETO";
        header('Location: login.php?erro');
        exit; // Encerra o script após o redirecionamento
    }
} catch (Exception $e) {
    // Armazena a mensagem de erro na sessão
    $_SESSION['mensagem_erro'] = "Erro de conexão: " . $e->getMessage();
    header('Location: login.php?erro');
    exit; // Encerra o script após o redirecionamento
}

?>
