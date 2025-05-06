<?php
$serverName = "tcp:projetointegradoro.database.windows.net,1433";
$connectionInfo = array(
    "UID" => "isa.avelina",
    "pwd" => "1S@b3ll@",
    "Database" => "projeto",
    "LoginTimeout" => 30,
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    echo "Conexão com o banco de dados estabelecida com sucesso!";
} else {
    echo "Erro ao conectar ao banco de dados.";
    die(print_r(sqlsrv_errors(), true));
}
?>
