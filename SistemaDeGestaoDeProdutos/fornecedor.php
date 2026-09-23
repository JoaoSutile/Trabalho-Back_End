<?php

session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}

try {

    $sql = "SELECT * FROM fornecedores ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $fornecedores = [];

}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Fornecedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="painel.php">Gestão de Produtos</a>
            <div class="d-flex align-items-center me-auto">
                <a href="painel.php" class="btn btn-light btn-sm me-2">Painel</a>
                <a href="cadastros.php" class="btn btn-light btn-sm me-2">Cadastros</a>
                <a href="produtos.php" class="btn btn-light btn-sm me-2">Produtos / Cesta</a>
            </div>
            <div class="dropdown user-dropdown">
                <a href="#" class="text-white text-decoration-none dropdown-toggle fw-semibold" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo htmlspecialchars($_SESSION["usuario_nome"] ?? "Usuário"); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item text-danger" href="login.html">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title m-0 text-primary fw-bold">Lista de Fornecedores</h4>
                        <a href="cadastros.php" class="btn btn-primary btn-sm">+ Novo Fornecedor</a>
                    </div>
                    <div class="card-body p-4">

                        <?php if (empty($fornecedores)): ?>

                            <p class="text-muted m-0">Nenhum fornecedor cadastrado no momento.</p>

                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Razão Social / Nome</th>
                                            <th>CNPJ / CPF</th>
                                            <th>E-mail</th>
                                            <th>Telefone</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ($fornecedores as $f): ?>

                                            <tr>
                                                <td><?= $f['id']; ?></td>
                                                <td class="fw-semibold"><?= htmlspecialchars($f['nome']); ?></td>
                                                <td><?= htmlspecialchars($f['cnpj']); ?></td>
                                                <td><?= htmlspecialchars($f['email'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($f['telefone'] ?? '-'); ?></td>
                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>