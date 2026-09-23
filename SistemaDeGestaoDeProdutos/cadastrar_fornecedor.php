<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome_fornecedor"] ?? "");
    $cnpj = trim($_POST["cnpj"] ?? "");
    $email = trim($_POST["email_fornecedor"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");

    if (!empty($nome) && !empty($cnpj)) {

        try {
            $sqlInsert = "INSERT INTO fornecedores (nome, cnpj, email, telefone) VALUES (:nome, :cnpj, :email, :telefone)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindValue(":nome", $nome);
            $stmtInsert->bindValue(":cnpj", $cnpj);
            $stmtInsert->bindValue(":email", $email);
            $stmtInsert->bindValue(":telefone", $telefone);

            if ($stmtInsert->execute()) {
                echo "<script>alert('Fornecedor cadastrado com sucesso!'); window.location.href='cadastros.php';</script>";
            } else {
                echo "<script>alert('Erro ao cadastrar fornecedor!'); window.location.href='cadastros.php';</script>";
            }

        } catch (PDOException $e) {
            echo "Erro no banco de dados: " . $e->getMessage();
        }

    } else {
        echo "<script>alert('Preencha os campos obrigatórios!'); window.location.href='cadastros.php';</script>";
    }

} else {
    header("Location: cadastros.php");
    exit;
}
?>