<?php

session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}

if (!isset($_SESSION['cesta'])) {

    $_SESSION['cesta'] = [];

}

$mensagemSucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar_cesta') {

    $id_prod = (int)$_POST['produto_id'];
    $qtd = (int)($_POST['qtd_cesta'] ?? 1);

    if (isset($_SESSION['cesta'][$id_prod])) {

        $_SESSION['cesta'][$id_prod] += $qtd;

    } else {

        $_SESSION['cesta'][$id_prod] = $qtd;

    }

    $mensagemSucesso = "Produto adicionado à cesta!";

}

$busca = trim($_GET['busca'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');

try {

    $stmtCat = $pdo->query("SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria ASC");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

    $sql = "SELECT p.*, f.nome AS nome_fornecedor 
            FROM produtos p 
            INNER JOIN fornecedores f ON p.id_fornecedor = f.id 
            WHERE 1=1";
    $params = [];

    if (!empty($busca)) {

        $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca OR f.nome LIKE :busca)";
        $params[':busca'] = "%$busca%";

    }

    if (!empty($categoria)) {

        $sql .= " AND p.categoria = :categoria";
        $params[':categoria'] = $categoria;

    }

    $sql .= " ORDER BY p.nome ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $produtos = [];
    $categorias = [];

}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Catálogo de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
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
                <a href="catalogo.php" class="btn btn-light btn-sm me-2 fw-semibold active">Catálogo</a>
                <a href="cesta.php" class="btn btn-light btn-sm me-2">Cesta</a>
            </div>
            <div class="dropdown user-dropdown">
                <a href="#" class="text-white text-decoration-none dropdown-toggle fw-semibold" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo htmlspecialchars($_SESSION["usuario_nome"] ?? "Usuário"); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownUser">
                    <li>
                        <span class="dropdown-item-text text-muted small">
                            <strong>E-mail:</strong><br>
                            <?php echo htmlspecialchars($_SESSION["usuario_email"] ?? "email@nao.informado"); ?>
                        </span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger fw-semibold" href="login.html">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">

        <?php if (!empty($mensagemSucesso)): ?>

            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($mensagemSucesso); ?>
                <a href="cesta.php" class="alert-link ms-2">Ver Cesta</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="text-primary fw-bold m-0">Catálogo de Produtos</h2>
                <p class="text-muted m-0">Visualização de todos os produtos disponíveis</p>
            </div>

            <form action="catalogo.php" method="GET" class="d-flex gap-2 flex-wrap" style="max-width: 550px; width: 100%;">
                <input class="form-control" type="search" name="busca" placeholder="Buscar produto..." value="<?= htmlspecialchars($busca); ?>">
                <select name="categoria" class="form-select" style="max-width: 180px;" onchange="this.form.submit()">
                    <option value="">Todas Categorias</option>

                    <?php foreach ($categorias as $cat): ?>

                        <option value="<?= htmlspecialchars($cat); ?>" <?= ($categoria === $cat) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat); ?>
                        </option>

                    <?php endforeach; ?>

                </select>
                <button class="btn btn-primary" type="submit">Filtrar</button>

                <?php if (!empty($busca) || !empty($categoria)): ?>

                    <a href="catalogo.php" class="btn btn-outline-secondary">Limpar</a>

                <?php endif; ?>

            </form>
        </div>

        <?php if (empty($produtos)): ?>

            <div class="alert alert-info shadow-sm" role="alert">
                Nenhum produto foi encontrado no catálogo com os filtros selecionados.
            </div>

        <?php else: ?>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">

                <?php foreach ($produtos as $p): ?>

                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                            <?= htmlspecialchars($p['categoria'] ?: 'Geral'); ?>
                                        </span>
                                        <span class="badge <?= $p['quantidade'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?= $p['quantidade'] > 0 ? $p['quantidade'] . ' em estoque' : 'Esgotado'; ?>
                                        </span>
                                    </div>

                                    <h5 class="card-title text-dark fw-bold mb-2"><?= htmlspecialchars($p['nome']); ?></h5>
                                    <p class="text-muted small mb-2">
                                        <strong>Fornecedor:</strong> <?= htmlspecialchars($p['nome_fornecedor']); ?>
                                    </p>

                                    <p class="card-text text-secondary small mb-3">
                                        <?= htmlspecialchars($p['descricao'] ?: 'Sem descrição informada.'); ?>
                                    </p>
                                </div>

                                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">Preço</small>
                                        <span class="fs-5 fw-bold text-primary">R$ <?= number_format($p['preco'], 2, ',', '.'); ?></span>
                                    </div>

                                    <form action="catalogo.php" method="POST" class="d-inline">
                                        <input type="hidden" name="acao" value="adicionar_cesta">
                                        <input type="hidden" name="produto_id" value="<?= $p['id']; ?>">
                                        <input type="hidden" name="qtd_cesta" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" <?= $p['quantidade'] <= 0 ? 'disabled' : ''; ?>>
                                            + Cesta
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>