<?php

session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}

$mensagem = "";
$tipoMensagem = "";
$abaAtiva = "fornecedor";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar_fornecedor') {

        $abaAtiva = "fornecedor";
        $nome = trim($_POST['nome_fornecedor']);
        $cnpj = trim($_POST['cnpj']);
        $email = trim($_POST['email_fornecedor'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if (!empty($nome) && !empty($cnpj)) {

            try {

                $stmt = $pdo->prepare("INSERT INTO fornecedores (nome, cnpj, email, telefone) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$nome, $cnpj, $email, $telefone])) {

                    $mensagem = "Fornecedor registrado com sucesso!";
                    $tipoMensagem = "success";

                }

            } catch (PDOException $e) {

                $mensagem = "Erro ao cadastrar fornecedor: " . $e->getMessage();
                $tipoMensagem = "danger";

            }

        } else {

            $mensagem = "Por favor, preencha todos os campos obrigatórios do fornecedor.";
            $tipoMensagem = "warning";

        }

    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar_produto') {

        $abaAtiva = "produto";
        $nome = trim($_POST['nome_produto']);
        $id_fornecedor = (int)$_POST['id_fornecedor'];
        $preco = (float)$_POST['preco'];
        $quantidade = (int)$_POST['quantidade'];
        $categoria = trim($_POST['categoria'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        if (!empty($nome) && $id_fornecedor > 0 && $preco >= 0 && $quantidade >= 0) {

            try {

                $stmt = $pdo->prepare("INSERT INTO produtos (nome, id_fornecedor, preco, quantidade, categoria, descricao) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$nome, $id_fornecedor, $preco, $quantidade, $categoria, $descricao])) {

                    $mensagem = "Produto registrado com sucesso!";
                    $tipoMensagem = "success";

                }

            } catch (PDOException $e) {

                $mensagem = "Erro ao cadastrar produto: " . $e->getMessage();
                $tipoMensagem = "danger";

            }

        } else {

            $mensagem = "Por favor, preencha todos os campos obrigatórios do produto.";
            $tipoMensagem = "warning";

        }

    }

}

try {

    $stmtForn = $pdo->query("SELECT id, nome FROM fornecedores ORDER BY nome ASC");
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $fornecedores = [];

}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Central de Cadastros</title>
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
                <a href="cadastros.php" class="btn btn-light btn-sm me-2 fw-semibold active">Cadastros</a>
                <a href="catalogo.php" class="btn btn-light btn-sm me-2">Catálogo</a>
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

    <div class="container mb-5" style="max-width: 800px;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold m-0">Central de Cadastros</h2>
            <a href="painel.php" class="btn btn-outline-secondary btn-sm">Voltar ao Painel</a>
        </div>

        <?php if (!empty($mensagem)): ?>

            <div class="alert alert-<?= $tipoMensagem; ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white pt-3 pb-0 border-bottom-0">
                <ul class="nav nav-tabs card-header-tabs" id="cadastroTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $abaAtiva === 'fornecedor' ? 'active fw-bold' : ''; ?>" id="fornecedor-tab" data-bs-toggle="tab" data-bs-target="#fornecedor-pane" type="button" role="tab">
                            1. Cadastrar Fornecedor
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $abaAtiva === 'produto' ? 'active fw-bold' : ''; ?>" id="produto-tab" data-bs-toggle="tab" data-bs-target="#produto-pane" type="button" role="tab">
                            2. Cadastrar Produto
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="cadastroTabsContent">

                    <div class="tab-pane fade <?= $abaAtiva === 'fornecedor' ? 'show active' : ''; ?>" id="fornecedor-pane" role="tabpanel">
                        <form action="cadastros.php" method="POST">
                            <input type="hidden" name="acao" value="cadastrar_fornecedor">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Razão Social / Nome <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nome_fornecedor" placeholder="Ex: TechDistribuidora LTDA" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">CNPJ / CPF <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="cnpj" placeholder="00.000.000/0001-00" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">E-mail de Contato</label>
                                    <input type="email" class="form-control" name="email_fornecedor" placeholder="contato@empresa.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telefone / WhatsApp</label>
                                    <input type="text" class="form-control" name="telefone" placeholder="(11) 98765-4321">
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-success px-4">Cadastrar Fornecedor</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade <?= $abaAtiva === 'produto' ? 'show active' : ''; ?>" id="produto-pane" role="tabpanel">

                        <?php if (empty($fornecedores)): ?>

                            <div class="alert alert-warning my-2" role="alert">
                                <strong>Atenção:</strong> Você precisa cadastrar pelo menos um fornecedor na aba ao lado antes de registrar produtos.
                            </div>

                        <?php else: ?>

                            <form action="cadastros.php" method="POST">
                                <input type="hidden" name="acao" value="cadastrar_produto">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nome do Produto <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nome_produto" placeholder="Ex: Monitor Gamer 24" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Fornecedor <span class="text-danger">*</span></label>
                                        <select class="form-select" name="id_fornecedor" required>
                                            <option value="" selected disabled>Selecione um fornecedor...</option>

                                            <?php foreach ($fornecedores as $f): ?>

                                                <option value="<?= $f['id']; ?>"><?= htmlspecialchars($f['nome']); ?></option>

                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Preço (R$) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="preco" placeholder="0.00" min="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Estoque Inicial <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="quantidade" placeholder="0" min="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Categoria</label>
                                        <input type="text" class="form-control" name="categoria" placeholder="Ex: Eletrônicos">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Descrição do Produto</label>
                                        <textarea class="form-control" name="descricao" rows="3" placeholder="Insira detalhes técnicas do produto..."></textarea>
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary px-4">Cadastrar Produto</button>
                                    </div>
                                </div>
                            </form>

                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
