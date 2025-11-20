<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecoleta";
date_default_timezone_set('America/Sao_Paulo');

try {
    // Cria a conexao com o banco de dados
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Define o modo de erro PDO como exceção
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Erro" . $e->getMessage();
}
?>