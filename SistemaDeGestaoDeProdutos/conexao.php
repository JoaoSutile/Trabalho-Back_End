<?php

$host = "localhost";
$dbname = "sistema_produtos";
$usuario = "root";
$senha = "";

try {

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Erro na conexão com o banco de dados: " . $e->getMessage());

}

?>