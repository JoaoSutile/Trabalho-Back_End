<?php

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if (!empty($nome) && !empty($email) && !empty($senha)) {

        $senhaHash = hash("sha256", $senha);

        try {

            $sqlCheck = "SELECT id FROM usuarios WHERE email = :email";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindValue(":email", $email);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {

                echo "<script>alert('Este e-mail já está cadastrado!'); window.location.href='login.html';</script>";

            } else {

                $sqlInsert = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->bindValue(":nome", $nome);
                $stmtInsert->bindValue(":email", $email);
                $stmtInsert->bindValue(":senha", $senhaHash);

                if ($stmtInsert->execute()) {

                    echo "<script>alert('Usuário cadastrado com sucesso!'); window.location.href='login.html';</script>";

                } else {

                    echo "<script>alert('Erro ao cadastrar usuário!'); window.location.href='login.html';</script>";

                }

            }

        } catch (PDOException $e) {

            echo "Erro no banco de dados: " . $e->getMessage();

        }

    } else {

        echo "<script>alert('Preencha todos os campos!'); window.location.href='login.html';</script>";

    }

} else {

    header("Location: login.html");
    exit;

}

?>