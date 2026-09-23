<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome_produto"] ?? "");
    $id_fornecedor = $_POST["id_fornecedor"] ?? "";
    $preco = $_POST["preco"] ?? 0;
    $quantidade = $_POST["quantidade"] ?? 0;
    $categoria = trim($_POST["categoria"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    if (!empty($nome) && !empty($id_fornecedor) && $preco > 0) {

        try {
            $sqlInsert = "INSERT INTO produtos (nome, id_fornecedor, preco, quantidade, categoria, descricao) 
                          VALUES (:nome, :id_fornecedor, :preco, :quantidade, :categoria, :descricao)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindValue(":nome", $nome);
            $stmtInsert->bindValue(":id_fornecedor", $id_fornecedor);
            $stmtInsert->bindValue(":preco", $preco);
            $stmtInsert->bindValue(":quantidade", $quantidade);
            $stmtInsert->bindValue(":categoria", $categoria);
            $stmtInsert->bindValue(":descricao", $descricao);

            if ($stmtInsert->execute()) {
                echo "<script>alert('Produto cadastrado com sucesso!'); window.location.href='cadastros.php';</script>";
            } else {
                echo "<script>alert('Erro ao cadastrar produto!'); window.location.href='cadastros.php';</script>";
            }

        } catch (PDOException $e) {
            echo "Erro no banco de dados: " . $e->getMessage();
        }

    } else {
        echo "<script>alert('Preencha todos os campos obrigatórios!'); window.location.href='cadastros.php';</script>";
    }

} else {
    header("Location: cadastros.php");
    exit;
}
?>