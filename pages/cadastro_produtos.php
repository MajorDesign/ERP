<head>
    <?php
    include "../include/head.html";
    require_once '../conexao.php';
    ?>
    <nav class="navbar navbar-dark bg-dark nav-mod-interna">
        <div class="container">
            <div class="row">
                <div class="col-md">
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-primary btn-modific d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <img class="img-icon" src="..\imagens\icone\adicao.png" alt="">
                            <span class="ms-2">Novo</span>
                        </button>
                        <!-- Botão Editar Produto começa aqui -->
                        <button type="button" class="btn btn-warning btn-sm disabled" id="editarProdutoBtn" disabled>
                            <i class="fa-solid fa-file-pen"></i>
                            <span class="ms-2">Editar</span>
                        </button>
                        <!-- Botão Editar Produto termina aqui -->
                        <button type="button" id="deleteProductBtn" class="btn btn-danger disabled" disabled>Excluir Produto</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>

<body>

    <!-- Modal Novo Produto -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content container-sm">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Novo Produto</h5>
                    <nav class="navbar navbar-dark bg-dark nav-mod-interna">Cadastro de Produto</nav>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Formulário dentro do modal -->
                <form id="produtoForm" action="../salvar.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="card text-black">
                                    <div class="container">
                                        <h2>INFORMAÇÕES</h2>

                                        <label>Nome do Produto:</label>
                                        <input type="text" name="nome" required class="form-control"><br>

                                        <label>Quantidade:</label>
                                        <input type="number" name="quantidade" required class="form-control"><br>

                                        <label>Preço do Produto:</label>
                                        <input type="text" name="valor" required class="form-control"><br>

                                        <?php include "../include/categoria_field.php"; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Footer do modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Produto -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content container-sm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Produto</h5>
                    <nav class="navbar navbar-dark bg-dark nav-mod-interna">Editar Produto</nav>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Formulário dentro do modal -->
                <form id="editProdutoForm" action="../atualizar.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="card text-black">
                                    <div class="container">
                                        <h2>INFORMAÇÕES</h2>

                                        <input type="hidden" name="id" id="editProdutoId">

                                        <label>Nome do Produto:</label>
                                        <input type="text" name="nome" id="editProdutoNome" required class="form-control"><br>

                                        <label>Descrição:</label>
                                        <textarea name="descricao" id="editProdutoDescricao" required class="form-control"></textarea><br>

                                        <label>Preço do Produto:</label>
                                        <input type="number" name="valor" id="editProdutoValor" required class="form-control" step="0.01"><br>

                                        <?php include "../include/categoria_field.php"; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Footer do modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <button type="button" id="toggleStatus" class="btn btn-warning">Alterar Status</button>
                        <button type="button" id="deleteProduct" class="btn btn-danger">Excluir Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para corrigir backdrop do modal -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = "auto"; // Permite rolagem da página
                });
            });

            // Adiciona evento de clique ao botão Editar
            document.getElementById('editarProdutoBtn').addEventListener('click', function() {
                if (this.classList.contains('disabled')) {
                    return;
                }
                const selectedRow = document.querySelector('.product-row.selected');
                if (selectedRow) {
                    const id = selectedRow.dataset.id;
                    const nome = selectedRow.dataset.nome;
                    const descricao = selectedRow.dataset.descricao;
                    const valor = selectedRow.dataset.valor;
                    abrirModalEditar(id, nome, descricao, valor);
                } else {
                    alert('Por favor, selecione um produto para editar.');
                }
            });

            document.getElementById("toggleStatus").addEventListener("click", function() {
                const id = document.getElementById("editProdutoId").value;
                const statusAtual = document.querySelector('.product-row.selected').dataset.status;
                const novoStatus = (statusAtual == 1) ? 0 : 1;

                fetch('../atualizar_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${id}&status=${novoStatus}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert("Erro ao atualizar o status.");
                        }
                    })
                    .catch(error => console.error("Erro:", error));
            });

            document.getElementById("editProdutoForm").addEventListener("submit", function(event) {
                event.preventDefault();
                fetch(this.action, {
                        method: this.method,
                        body: new FormData(this)
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert("Erro ao salvar alterações.");
                        }
                    }).catch(error => console.error("Erro:", error));
            });

            document.getElementById("deleteProductBtn").addEventListener("click", function() {
                const selectedRow = document.querySelector('.product-row.selected');
                if (!selectedRow) {
                    alert('Por favor, selecione um produto para excluir.');
                    return;
                }
                const id = selectedRow.dataset.id;

                if (confirm("Tem certeza que deseja excluir este produto?")) {
                    fetch('../remover_produto.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id=${id}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert("Erro ao excluir o produto: " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Erro:", error);
                            alert("Erro ao excluir o produto: " + error.message);
                        });
                }
            });

            // Adiciona evento de clique aos produtos para abrir o modal de edição
            document.querySelectorAll('.product-row').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.product-row').forEach(r => r.classList.remove('selected'));
                    this.classList.add('selected');
                    const editarBtn = document.getElementById('editarProdutoBtn');
                    editarBtn.classList.remove('disabled');
                    editarBtn.classList.add('enabled');
                    editarBtn.disabled = false;

                    const deleteBtn = document.getElementById('deleteProductBtn');
                    deleteBtn.classList.remove('disabled');
                    deleteBtn.classList.add('enabled');
                    deleteBtn.disabled = false;
                });

                item.addEventListener('dblclick', function() {
                    const id = this.dataset.id;
                    const nome = this.dataset.nome;
                    const descricao = this.dataset.descricao;
                    const valor = this.dataset.valor;
                    abrirModalEditar(id, nome, descricao, valor);
                });
            });
        });

        function abrirModalEditar(id, nome, descricao, valor) {
            document.getElementById('editProdutoId').value = id;
            document.getElementById('editProdutoNome').value = nome;
            document.getElementById('editProdutoDescricao').value = descricao;
            document.getElementById('editProdutoValor').value = valor;
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
    </script>

    <!-- Pop-up modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2>Lista de Produtos</h2>

            <!-- Aqui será carregada a tabela de produtos via AJAX -->
            <div id="conteudo-modal"></div>
        </div>
    </div>
    </div>
    </div>
    <div class="container">
        <div class="row">
            <?php
            include '../listarprodutos.php';
            ?>
            <script>
                function editarProduto(id, nome, descricao, valor) {
                    abrirModalEditar(id, nome, descricao, valor);
                }

                // Adiciona evento de clique aos produtos para abrir o modal de edição
                document.querySelectorAll('.product-row').forEach(item => {
                    item.addEventListener('click', function() {
                        document.querySelectorAll('.product-row').forEach(r => r.classList.remove('selected'));
                        this.classList.add('selected');
                        const editarBtn = document.getElementById('editarProdutoBtn');
                        editarBtn.classList.remove('disabled');
                        editarBtn.classList.add('enabled');
                        editarBtn.disabled = false;

                        const deleteBtn = document.getElementById('deleteProductBtn');
                        deleteBtn.classList.remove('disabled');
                        deleteBtn.classList.add('enabled');
                        deleteBtn.disabled = false;
                    });

                    item.addEventListener('dblclick', function() {
                        const id = this.dataset.id;
                        const nome = this.dataset.nome;
                        const descricao = this.dataset.descricao;
                        const valor = this.dataset.valor;
                        abrirModalEditar(id, nome, descricao, valor);
                    });
                });
            </script>

            <!-- Pop-up modal -->
            <div id="modal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="fecharModal()">&times;</span>
                    <h2>Lista de Produtos</h2>

                    <!-- Aqui será carregada a tabela de produtos via AJAX -->
                    <div id="conteudo-modal"></div>
                </div>
            </div>
        </div>
    </div>
    </table>
    </div>
    </div>
</body>