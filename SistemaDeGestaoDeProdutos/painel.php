<?php

session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}

try {

    $stmtProd = $pdo->query("SELECT p.*, f.nome AS nome_fornecedor FROM produtos p INNER JOIN fornecedores f ON p.id_fornecedor = f.id ORDER BY p.id DESC");
    $produtos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

    $stmtForn = $pdo->query("SELECT * FROM fornecedores ORDER BY id DESC");
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    $totalProdutos = count($produtos);
    $totalFornecedores = count($fornecedores);

} catch (PDOException $e) {

    $produtos = [];
    $fornecedores = [];
    $totalProdutos = 0;
    $totalFornecedores = 0;

}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Painel de Controle</title>
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

        <h2 class="text-primary fw-bold mb-4">Painel de Controle</h2>

        <div class="row g-4">

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title m-0 text-primary fw-bold">Produtos Cadastrados</h4>
                        <span class="badge bg-primary fs-6"><?php echo $totalProdutos; ?></span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>

                            <?php if (empty($produtos)): ?>

                                <p class="text-muted mb-4">Nenhum produto cadastrado até o momento.</p>

                            <?php else: ?>

                                <div class="table-responsive mb-3">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nome</th>
                                                <th>Fornecedor</th>
                                                <th>Preço</th>
                                                <th>Estoque</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach (array_slice($produtos, 0, 5) as $p): ?>

                                                <tr>
                                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['nome']); ?></td>
                                                    <td><?php echo htmlspecialchars($p['nome_fornecedor']); ?></td>
                                                    <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                                    <td><?php echo $p['quantidade']; ?> un.</td>
                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mt-3">
                            <a href="cadastros.php" class="btn btn-outline-primary w-100">Cadastrar Produto</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title m-0 text-primary fw-bold">Fornecedores Cadastrados</h4>
                        <span class="badge bg-success fs-6"><?php echo $totalFornecedores; ?></span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>

                            <?php if (empty($fornecedores)): ?>

                                <p class="text-muted mb-4">Nenhum fornecedor cadastrado até o momento.</p>

                            <?php else: ?>

                                <div class="table-responsive mb-3">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Razão Social / Nome</th>
                                                <th>CNPJ / CPF</th>
                                                <th>Telefone</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach (array_slice($fornecedores, 0, 5) as $f): ?>

                                                <tr>
                                                    <td class="fw-semibold"><?php echo htmlspecialchars($f['nome']); ?></td>
                                                    <td><?php echo htmlspecialchars($f['cnpj']); ?></td>
                                                    <td><?php echo htmlspecialchars($f['telefone'] ?? '-'); ?></td>
                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mt-3">
                            <a href="cadastros.php" class="btn btn-outline-success w-100">Cadastrar Fornecedor</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>