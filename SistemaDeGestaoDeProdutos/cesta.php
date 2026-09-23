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
$mensagemErro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['acao']) && $_POST['acao'] === 'remover_item') {

        $id = (int)$_POST['produto_id'];
        unset($_SESSION['cesta'][$id]);
        header("Location: cesta.php");
        exit;

    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'atualizar_qtd') {

        $id = (int)$_POST['produto_id'];
        $qtd = (int)$_POST['qtd'];

        if ($qtd > 0) {

            $_SESSION['cesta'][$id] = $qtd;

        } else {

            unset($_SESSION['cesta'][$id]);

        }

        header("Location: cesta.php");
        exit;

    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'finalizar_pedido') {

        if (!empty($_SESSION['cesta'])) {

            try {

                $pdo->beginTransaction();

                $ids = array_keys($_SESSION['cesta']);
                $in  = str_repeat('?,', count($ids) - 1) . '?';

                $stmt = $pdo->prepare("SELECT id, preco, quantidade FROM produtos WHERE id IN ($in)");
                $stmt->execute($ids);
                $produtosDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $totalGeral = 0;
                $itensParaInserir = [];

                foreach ($produtosDb as $p) {

                    $qtdDesejada = $_SESSION['cesta'][$p['id']];
                    $subtotal = $p['preco'] * $qtdDesejada;
                    $totalGeral += $subtotal;

                    $itensParaInserir[] = [
                        'id_produto' => $p['id'],
                        'quantidade' => $qtdDesejada,
                        'preco_unitario' => $p['preco']
                    ];

                }

                $stmtPed = $pdo->prepare("INSERT INTO pedidos (id_usuario, total, data_pedido) VALUES (?, ?, NOW())");
                $stmtPed->execute([$_SESSION['usuario_id'], $totalGeral]);
                $idPedido = $pdo->lastInsertId();

                $stmtItem = $pdo->prepare("INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
                $stmtEstoque = $pdo->prepare("UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?");

                foreach ($itensParaInserir as $item) {

                    $stmtItem->execute([$idPedido, $item['id_produto'], $item['quantidade'], $item['preco_unitario']]);
                    $stmtEstoque->execute([$item['quantidade'], $item['id_produto']]);

                }

                $pdo->commit();
                $_SESSION['cesta'] = [];
                $mensagemSucesso = "Pedido finalizado com sucesso! Seu histórico foi atualizado no Painel.";

            } catch (Exception $e) {

                $pdo->rollBack();
                $mensagemErro = "Erro ao finalizar pedido: " . $e->getMessage();

            }

        }

    }

}

if (isset($_GET['limpar_cesta'])) {

    $_SESSION['cesta'] = [];
    header("Location: cesta.php");
    exit;

}

$itensCesta = [];
$totalGeral = 0;

if (!empty($_SESSION['cesta'])) {

    $ids = array_keys($_SESSION['cesta']);
    $in  = str_repeat('?,', count($ids) - 1) . '?';

    try {

        $stmt = $pdo->prepare("SELECT p.*, f.nome AS nome_fornecedor FROM produtos p INNER JOIN fornecedores f ON p.id_fornecedor = f.id WHERE p.id IN ($in)");
        $stmt->execute($ids);
        $produtosDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($produtosDb as $p) {

            $qtd = $_SESSION['cesta'][$p['id']];
            $subtotal = $p['preco'] * $qtd;
            $totalGeral += $subtotal;

            $itensCesta[] = [
                'id' => $p['id'],
                'nome' => $p['nome'],
                'fornecedor' => $p['nome_fornecedor'],
                'preco' => $p['preco'],
                'quantidade' => $qtd,
                'estoque_max' => $p['quantidade'],
                'subtotal' => $subtotal
            ];

        }

    } catch (PDOException $e) {

        $itensCesta = [];

    }

}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Sua Cesta</title>
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
                <a href="catalogo.php" class="btn btn-light btn-sm me-2">Catálogo</a>
                <a href="cesta.php" class="btn btn-light btn-sm me-2 fw-semibold active">Cesta</a>
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

            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <?= htmlspecialchars($mensagemSucesso); ?>
                <a href="painel.php" class="alert-link ms-2">Ir para o Painel</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        <?php endif; ?>

        <?php if (!empty($mensagemErro)): ?>

            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <?= htmlspecialchars($mensagemErro); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="text-primary fw-bold m-0">Sua Cesta de Compras</h2>
            <a href="catalogo.php" class="btn btn-outline-primary fw-semibold">
                &larr; Continuar Comprando
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <?php if (empty($itensCesta)): ?>

                    <div class="text-center py-5">
                        <h4 class="text-muted fw-normal mb-3">Sua cesta está vazia no momento.</h4>
                        <p class="text-secondary mb-4">Acesse nosso catálogo para adicionar produtos.</p>
                        <a href="catalogo.php" class="btn btn-primary btn-lg">Ir para o Catálogo</a>
                    </div>

                <?php else: ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Fornecedor</th>
                                    <th>Preço Unitário</th>
                                    <th style="width: 130px;">Quantidade</th>
                                    <th>Subtotal</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($itensCesta as $item): ?>

                                    <tr>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($item['nome']); ?></td>
                                        <td class="text-muted"><?= htmlspecialchars($item['fornecedor']); ?></td>
                                        <td>R$ <?= number_format($item['preco'], 2, ',', '.'); ?></td>
                                        <td>
                                            <form action="cesta.php" method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="acao" value="atualizar_qtd">
                                                <input type="hidden" name="produto_id" value="<?= $item['id']; ?>">
                                                <input type="number" name="qtd" class="form-control form-control-sm text-center" value="<?= $item['quantidade']; ?>" min="1" max="<?= $item['estoque_max']; ?>" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="fw-bold text-success">R$ <?= number_format($item['subtotal'], 2, ',', '.'); ?></td>
                                        <td class="text-end">
                                            <form action="cesta.php" method="POST" class="d-inline">
                                                <input type="hidden" name="acao" value="remover_item">
                                                <input type="hidden" name="produto_id" value="<?= $item['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                            </form>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-3">
                        <a href="cesta.php?limpar_cesta=1" class="btn btn-outline-danger">Esvaziar Cesta</a>

                        <div class="d-flex align-items-center gap-4">
                            <div class="text-end">
                                <span class="fs-5 me-2">Total:</span>
                                <span class="fs-3 fw-bold text-primary">R$ <?= number_format($totalGeral, 2, ',', '.'); ?></span>
                            </div>
                            <form action="cesta.php" method="POST" onsubmit="return confirm('Confirma a finalização deste pedido?');">
                                <input type="hidden" name="acao" value="finalizar_pedido">
                                <button type="submit" class="btn btn-success btn-lg fw-bold px-4">Finalizar Pedido</button>
                            </form>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>