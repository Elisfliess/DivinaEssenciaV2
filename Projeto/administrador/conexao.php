<?php
//configurações do banco de dados//
$host = 'localhost';
$db = 'Projeto';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

//criando a conexão com o banco de dados//
try{
    $pdo = new PDO($dsn, $user, $pass);
    echo "conexão com o banco de dados foi bem sucedida";
}
catch(PDOException $e){
    echo "Erro ao tentar conectar com o banco de dados <p>" .$e;
}
    
