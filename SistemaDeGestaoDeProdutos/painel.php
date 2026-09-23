<?php

session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['acao']) && $_POST['acao'] === 'excluir_produto') {

        $id = (int)$_POST['id_produto'];
        try {

            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
            if ($stmt->execute([$id])) {

                $mensagem = "Produto excluído com sucesso!";
                $tipoMensagem = "success";

            }

        } catch (PDOException $e) {

            $mensagem = "Erro ao excluir o produto: " . $e->getMessage();
            $tipoMensagem = "danger";

        }

    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'excluir_fornecedor') {

        $id = (int)$_POST['id_fornecedor'];
        try {

            $stmt = $pdo->prepare("DELETE FROM fornecedores WHERE id = ?");
            if ($stmt->execute([$id])) {

                $mensagem = "Fornecedor excluído com sucesso!";
                $tipoMensagem = "success";

            }

        } catch (PDOException $e) {

            $mensagem = "Erro ao excluir fornecedor. Verifique se existem produtos associados a ele.";
            $tipoMensagem = "danger";

        }

    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'editar_produto') {

        $id = (int)$_POST['id_produto'];
        $nome = trim($_POST['nome_produto']);
        $id_fornecedor = (int)$_POST['id_fornecedor'];
        $preco = (float)$_POST['preco'];
        $quantidade = (int)$_POST['quantidade'];
        $categoria = trim($_POST['categoria'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        try {

            $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, id_fornecedor = ?, preco = ?, quantidade = ?, categoria = ?, descricao = ? WHERE id = ?");
            if ($stmt->execute([$nome, $id_fornecedor, $preco, $quantidade, $categoria, $descricao, $id])) {

                $mensagem = "Produto atualizado com sucesso!";
                $tipoMensagem = "success";

            }

        } catch (PDOException $e) {

            $mensagem = "Erro ao atualizar produto: " . $e->getMessage();
            $tipoMensagem = "danger";

        }

    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'editar_fornecedor') {

        $id = (int)$_POST['id_fornecedor'];
        $nome = trim($_POST['nome_fornecedor']);
        $cnpj = trim($_POST['cnpj']);
        $email = trim($_POST['email_fornecedor'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        try {

            $stmt = $pdo->prepare("UPDATE fornecedores SET nome = ?, cnpj = ?, email = ?, telefone = ? WHERE id = ?");
            if ($stmt->execute([$nome, $cnpj, $email, $telefone, $id])) {

                $mensagem = "Fornecedor atualizado com sucesso!";
                $tipoMensagem = "success";

            }

        } catch (PDOException $e) {

            $mensagem = "Erro ao atualizar fornecedor: " . $e->getMessage();
            $tipoMensagem = "danger";

        }

    }

}

$busca = trim($_GET['busca'] ?? '');

try {

    if (!empty($busca)) {

        $stmtProd = $pdo->prepare("SELECT p.*, f.nome AS nome_fornecedor 
                                    FROM produtos p 
                                    INNER JOIN fornecedores f ON p.id_fornecedor = f.id 
                                    WHERE p.nome LIKE :busca OR f.nome LIKE :busca OR p.categoria LIKE :busca 
                                    ORDER BY p.id DESC");
        $stmtProd->execute([':busca' => "%$busca%"]);
        $produtos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

        $stmtForn = $pdo->prepare("SELECT * FROM fornecedores 
                                    WHERE nome LIKE :busca OR cnpj LIKE :busca OR email LIKE :busca 
                                    ORDER BY id DESC");
        $stmtForn->execute([':busca' => "%$busca%"]);
        $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    } else {

        $stmtProd = $pdo->query("SELECT p.*, f.nome AS nome_fornecedor FROM produtos p INNER JOIN fornecedores f ON p.id_fornecedor = f.id ORDER BY p.id DESC");
        $produtos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

        $stmtForn = $pdo->query("SELECT * FROM fornecedores ORDER BY id DESC");
        $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    }

    $stmtAllForn = $pdo->query("SELECT id, nome FROM fornecedores ORDER BY nome ASC");
    $todosFornecedores = $stmtAllForn->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $produtos = [];
    $fornecedores = [];
    $todosFornecedores = [];

}

$totalProdutos = count($produtos);
$totalFornecedores = count($fornecedores);

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
                <a href="produtos.php" class="btn btn-light btn-sm me-2">Cesta</a>
                <a href="catalogo.php" class="btn btn-light btn-sm me-2">Catálogo</a>
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

        <?php if (!empty($mensagem)): ?>

            <div class="alert alert-<?= $tipoMensagem; ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="text-primary fw-bold m-0">Painel de Controle</h2>

            <form action="painel.php" method="GET" class="d-flex" style="max-width: 420px; width: 100%;">
                <input class="form-control me-2" type="search" name="busca" placeholder="Pesquisar produto ou fornecedor..." value="<?= htmlspecialchars($busca); ?>">
                <button class="btn btn-primary me-1" type="submit">Buscar</button>

                <?php if (!empty($busca)): ?>

                    <a href="painel.php" class="btn btn-outline-secondary">Limpar</a>

                <?php endif; ?>

            </form>
        </div>

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

                                <p class="text-muted mb-4">Nenhum produto encontrado.</p>

                            <?php else: ?>

                                <div class="table-responsive mb-3">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nome</th>
                                                <th>Fornecedor</th>
                                                <th>Preço</th>
                                                <th>Estoque</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($produtos as $p): ?>

                                                <tr>
                                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['nome']); ?></td>
                                                    <td><?php echo htmlspecialchars($p['nome_fornecedor']); ?></td>
                                                    <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                                    <td><?php echo $p['quantidade']; ?> un.</td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditProduto<?= $p['id']; ?>">
                                                            Editar
                                                        </button>
                                                        <form action="painel.php" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                                            <input type="hidden" name="acao" value="excluir_produto">
                                                            <input type="hidden" name="id_produto" value="<?= $p['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="modalEditProduto<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <form action="painel.php" method="POST">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title text-primary fw-bold">Editar Produto</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="acao" value="editar_produto">
                                                                    <input type="hidden" name="id_produto" value="<?= $p['id']; ?>">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Nome do Produto</label>
                                                                            <input type="text" class="form-control" name="nome_produto" value="<?= htmlspecialchars($p['nome']); ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Fornecedor</label>
                                                                            <select class="form-select" name="id_fornecedor" required>

                                                                                <?php foreach ($todosFornecedores as $fOpt): ?>

                                                                                    <option value="<?= $fOpt['id']; ?>" <?= ($fOpt['id'] == $p['id_fornecedor']) ? 'selected' : ''; ?>>
                                                                                        <?= htmlspecialchars($fOpt['nome']); ?>
                                                                                    </option>

                                                                                <?php endforeach; ?>

                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Preço (R$)</label>
                                                                            <input type="number" step="0.01" class="form-control" name="preco" value="<?= $p['preco']; ?>" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Estoque</label>
                                                                            <input type="number" class="form-control" name="quantidade" value="<?= $p['quantidade']; ?>" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Categoria</label>
                                                                            <input type="text" class="form-control" name="categoria" value="<?= htmlspecialchars($p['categoria']); ?>">
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label class="form-label">Descrição</label>
                                                                            <textarea class="form-control" name="descricao" rows="2"><?= htmlspecialchars($p['descricao']); ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

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

                                <p class="text-muted mb-4">Nenhum fornecedor encontrado.</p>

                            <?php else: ?>

                                <div class="table-responsive mb-3">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nome</th>
                                                <th>CNPJ</th>
                                                <th>Telefone</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($fornecedores as $f): ?>

                                                <tr>
                                                    <td class="fw-semibold"><?php echo htmlspecialchars($f['nome']); ?></td>
                                                    <td><?php echo htmlspecialchars($f['cnpj']); ?></td>
                                                    <td><?php echo htmlspecialchars($f['telefone'] ?? '-'); ?></td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#modalEditFornecedor<?= $f['id']; ?>">
                                                            Editar
                                                        </button>
                                                        <form action="painel.php" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');">
                                                            <input type="hidden" name="acao" value="excluir_fornecedor">
                                                            <input type="hidden" name="id_fornecedor" value="<?= $f['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="modalEditFornecedor<?= $f['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <form action="painel.php" method="POST">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title text-success fw-bold">Editar Fornecedor</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="acao" value="editar_fornecedor">
                                                                    <input type="hidden" name="id_fornecedor" value="<?= $f['id']; ?>">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Razão Social / Nome</label>
                                                                            <input type="text" class="form-control" name="nome_fornecedor" value="<?= htmlspecialchars($f['nome']); ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">CNPJ / CPF</label>
                                                                            <input type="text" class="form-control" name="cnpj" value="<?= htmlspecialchars($f['cnpj']); ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">E-mail</label>
                                                                            <input type="email" class="form-control" name="email_fornecedor" value="<?= htmlspecialchars($f['email'] ?? ''); ?>">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Telefone</label>
                                                                            <input type="text" class="form-control" name="telefone" value="<?= htmlspecialchars($f['telefone'] ?? ''); ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

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