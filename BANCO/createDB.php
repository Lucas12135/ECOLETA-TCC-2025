<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);


// SQL to create table
$sql = "CREATE DATABASE IF NOT EXISTS ecoleta";

if ($conn->query($sql) === TRUE) {
    echo "Banco de dados 'ecoleta' criado com sucesso";
} else {
    echo "Erro ao criar banco de dados: " . $conn->error;
}

$conn->close();
?>