<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['cesta'])) {
    $_SESSION['cesta'] = [];
}

if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar_cesta') {
    $id_prod = $_POST['produto_id'];
    $qtd = (int)($_POST['qtd_cesta'] ?? 1);

    if (isset($_SESSION['cesta'][$id_prod])) {
        $_SESSION['cesta'][$id_prod] += $qtd;
    } else {
        $_SESSION['cesta'][$id_prod] = $qtd;
    }
    header("Location: produtos.php");
    exit;
}

if (isset($_GET['limpar_cesta'])) {
    $_SESSION['cesta'] = [];
    header("Location: produtos.php");
    exit;
}

try {
    $sql = "SELECT p.*, f.nome AS nome_fornecedor 
            FROM produtos p 
            INNER JOIN fornecedores f ON p.id_fornecedor = f.id 
            ORDER BY p.id DESC";
    $stmt = $pdo->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Produtos e Cesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Gestão de Produtos</a>
            <div class="d-flex align-items-center text-white">
                <a href="cadastros.php" class="btn btn-light btn-sm me-3">Voltar aos Cadastros</a>
                <a href="login.html" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row g-4">
            
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title m-0 text-primary fw-bold">Produtos</h4>
                    </div>
                    <div class="card-body p-3">
                        <?php if (empty($produtos)): ?>
                            <p class="text-muted">Nenhum produto cadastrado no momento.</p>
                        <?php else: ?>
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <?php foreach ($produtos as $p): ?>
                                    <div class="col">
                                        <div class="card h-100 border shadow-sm">
                                            <div class="card-body">
                                                <h5 class="card-title text-primary fw-semibold"><?= htmlspecialchars($p['nome']); ?></h5>
                                                <h6 class="card-subtitle mb-2 text-muted">Fornecedor: <?= htmlspecialchars($p['nome_fornecedor']); ?></h6>
                                                <p class="card-text mb-1"><strong>Preço:</strong> R$ <?= number_format($p['preco'], 2, ',', '.'); ?></p>
                                                <p class="card-text mb-1"><strong>Estoque:</strong> <?= $p['quantidade']; ?> un.</p>
                                                <p class="card-text mb-2"><small class="text-muted"><?= htmlspecialchars($p['categoria']); ?></small></p>
                                                
                                                <form action="produtos.php" method="POST" class="d-flex align-items-center mt-3">
                                                    <input type="hidden" name="acao" value="adicionar_cesta">
                                                    <input type="hidden" name="produto_id" value="<?= $p['id']; ?>">
                                                    <input type="number" name="qtd_cesta" class="form-control form-control-sm me-2" value="1" min="1" max="<?= $p['quantidade']; ?>" style="width: 70px;">
                                                    <button type="submit" class="btn btn-sm btn-success w-100">Adicionar à Cesta</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title m-0 text-primary fw-bold">Sua Cesta</h4>
                        <?php if (!empty($_SESSION['cesta'])): ?>
                            <a href="produtos.php?limpar_cesta=1" class="btn btn-outline-danger btn-sm">Esvaziar</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-3">
                        <?php if (empty($_SESSION['cesta'])): ?>
                            <p class="text-muted m-0">A cesta está vazia.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush mb-3">
                                <?php
                                $totalGeral = 0;
                                foreach ($_SESSION['cesta'] as $prodId => $qtdItem):
                                    $stmtP = $pdo->prepare("SELECT nome, preco FROM produtos WHERE id = :id");
                                    $stmtP->bindValue(":id", $prodId);
                                    $stmtP->execute();
                                    $item = $stmtP->fetch(PDO::FETCH_ASSOC);
                                    if ($item):
                                        $subtotal = $item['preco'] * $qtdItem;
                                        $totalGeral += $subtotal;
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <h6 class="my-0 fw-semibold"><?= htmlspecialchars($item['nome']); ?></h6>
                                            <small class="text-muted"><?= $qtdItem; ?> x R$ <?= number_format($item['preco'], 2, ',', '.'); ?></small>
                                        </div>
                                        <span class="text-success fw-bold">R$ <?= number_format($subtotal, 2, ',', '.'); ?></span>
                                    </li>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </ul>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total:</span>
                                <span class="text-primary">R$ <?= number_format($totalGeral, 2, ',', '.'); ?></span>
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