
# Trabalho-Back_End

# Sistema de Gestão de Produtos e Fornecedores

Um sistema web para gerenciamento de estoque, cadastro de fornecedores e simulação de cesta de compras.

## Funcionalidades

**Autenticação de Usuários:** Tela de login e cadastro seguro de novos usuários.
**Gestão de Fornecedores:** Formular para cadastrar fornecedores com Razão Social, CNPJ, e-mail e telefone.
**Gestão de Produtos:** Cadastro de produtos com preço, estoque, categoria, descrição e vinculação direta ao fornecedor responsável.
**Cesta de Compras:** Interface de visualização de produtos cadastrados com funcionalidade para adicionar itens, calcular o valor total e gerenciar a cesta.

---

# Modelagem (DER)

Abaixo está a representação do Diagrama Entidade Relacionamento (DER) da aplicação:

[DER](der_sistema.png)

---

# Prototipagem de Telas (Figma)

Os esboços das interfaces da aplicação foram desenhados utilizando o **Figma**.

## Telas do Sistema:

**Link do Projeto no Figma:** [Clique aqui para acessar o protótipo navegável](https://www.figma.com/make/mS7NaLgAOluFbq53BMU18Z/Alta-fidelidade-Sistema-Estoque?t=AbfOPJDK2L0H7BMz-20&fullscreen=1)

---

# Tecnologias Utilizadas

**Front-end:** HTML, CSS e Bootstrap
**Back-end:** PHP
**Banco de Dados:** MySQL
**Servidor Local:** XAMPP

##  Como Executar o Projeto Localmente

1. **Clonar o repositório:**
    Git bash
    git clone [https://github.com/JoaoSutile/Trabalho-Back_End.git](https://github.com/JoaoSutile/Trabalho-Back_End.git)

2. **Mover para o servidor local:**
    Copie a pasta do projeto para o diretório htdocs do seu XAMPP.

3. **Configurar o Banco de Dados:**
    Inicie os serviços do Apache e MySQL no XAMPP Control Panel.
    Acesse o phpMyAdmin em http://localhost/phpmyadmin.
    Crie um banco de dados chamado sistema_produtos.
    Importe ou execute os comandos presentes no arquivo sistema_produtos.sql.

4. **Acessar a aplicação:**
    Abra o navegador e digite:
    http://localhost/NOME-DA-SUA-PASTA/login.html


