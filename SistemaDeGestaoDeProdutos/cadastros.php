<?php
session_start();
require_once "conexao.php";

// Busca os fornecedores cadastrados para popular o select de produto
try {
    $stmt = $pdo->query("SELECT id, nome FROM fornecedores ORDER BY nome ASC");
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
    <title>Sistema de Gestão - Cadastros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Gestão de Produtos</a>
            <div class="d-flex align-items-center text-white">               
                <a href="login.html" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <ul class="nav nav-tabs card-header-tabs" id="gestaoTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="produtos-tab" data-bs-toggle="tab" data-bs-target="#produtos" type="button" role="tab">Cadastrar Produto</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="fornecedores-tab" data-bs-toggle="tab" data-bs-target="#fornecedores" type="button" role="tab">Cadastrar Fornecedor</button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="gestaoTabsContent">

                            <div class="tab-pane fade show active" id="produtos" role="tabpanel">
                                <h4 class="card-title mb-4 text-primary">Novo Produto</h4>
                                <form action="cadastrar_produto.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nome_produto" class="form-label">Nome do Produto</label>
                                            <input type="text" class="form-control" id="nome_produto" name="nome_produto" placeholder="Ex: Teclado Mecânico RGB" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="id_fornecedor" class="form-label">Fornecedor</label>
                                            <select class="form-select" id="id_fornecedor" name="id_fornecedor" required>
                                                <option value="" selected disabled>Selecione um fornecedor...</option>
                                                <?php foreach ($fornecedores as $fornecedor): ?>
                                                    <option value="<?= $fornecedor['id']; ?>"><?= htmlspecialchars($fornecedor['nome']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="preco" class="form-label">Preço (R$)</label>
                                            <input type="number" step="0.01" class="form-control" id="preco" name="preco" placeholder="0.00" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="quantidade" class="form-label">Quantidade em Estoque</label>
                                            <input type="number" class="form-control" id="quantidade" name="quantidade" placeholder="0" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="categoria" class="form-label">Categoria</label>
                                            <input type="text" class="form-control" id="categoria" name="categoria" placeholder="Ex: Periféricos">
                                        </div>
                                        <div class="col-12">
                                            <label for="descricao" class="form-label">Descrição do Produto</label>
                                            <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Insira detalhes adicionais do produto..."></textarea>
                                        </div>
                                        <div class="col-12 text-end mt-4">
                                            <button type="reset" class="btn btn-secondary me-2">Limpar</button>
                                            <button type="submit" class="btn btn-primary">Salvar Produto</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="fornecedores" role="tabpanel">
                                <h4 class="card-title mb-4 text-primary">Novo Fornecedor</h4>
                                <form action="cadastrar_fornecedor.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nome_fornecedor" class="form-label">Razão Social / Nome</label>
                                            <input type="text" class="form-control" id="nome_fornecedor" name="nome_fornecedor" placeholder="Ex: Tech Distribuidora LTDA" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="cnpj" class="form-label">CNPJ / CPF</label>
                                            <input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="00.000.000/0001-00" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email_fornecedor" class="form-label">E-mail de Contato</label>
                                            <input type="email" class="form-control" id="email_fornecedor" name="email_fornecedor" placeholder="contato@fornecedor.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telefone" class="form-label">Telefone / WhatsApp</label>
                                            <input type="text" class="form-control" id="telefone" name="telefone" placeholder="(11) 99999-9999">
                                        </div>
                                        <div class="col-12 text-end mt-4">
                                            <button type="reset" class="btn btn-secondary me-2">Limpar</button>
                                            <button type="submit" class="btn btn-primary">Salvar Fornecedor</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>