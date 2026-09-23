<?php

session_start();
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if (!empty($email) && !empty($senha)) {

        $senhaHash = hash("sha256", $senha);

        try {

            $sql = "SELECT id, nome, email FROM usuarios WHERE email = :email AND senha = :senha";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":email", $email);
            $stmt->bindValue(":senha", $senhaHash);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["usuario_nome"] = $usuario["nome"];

                echo "<script>alert('Login realizado com sucesso!'); window.location.href='cadastros.php';</script>";

            } else {

                echo "<script>alert('E-mail ou senha incorretos!'); window.location.href='login.html';</script>";

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