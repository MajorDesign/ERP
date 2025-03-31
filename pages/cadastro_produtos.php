<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php
    include "../include/head.html";

    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $dbname = "banco_projeto";

    try {
        $conn = new mysqli($servidor, $usuario, $senha, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Falha na conexão: " . $conn->connect_error);
        }

        $conn->set_charset("utf8");
    } catch (Exception $e) {
        error_log("Erro de conexão: " . $e->getMessage());
        die("Erro ao conectar com o banco de dados");
    }
    // Busca as categorias no banco de dados
    try {
        $queryCategorias = "SELECT id, nome FROM categorias ORDER BY nome ASC";
        $resultCategorias = $conn->query($queryCategorias);

        $categorias = [];
        if ($resultCategorias && $resultCategorias->num_rows > 0) {
            while ($row = $resultCategorias->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
    } catch (Exception $e) {
        error_log("Erro ao buscar categorias: " . $e->getMessage());
        $categorias = [];
    }
    ?>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark nav-mod-interna">
        <div class="container">
            <div class="row">
                <div class="col-md">
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-primary btn-modific d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalNovoProduto">
                            <img class="img-icon" src="../imagens/icone/adicao.png" alt="">
                            <span class="ms-2">Novo</span>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm disabled" id="editarProdutoBtn" disabled>
                            <i class="fa-solid fa-file-pen"></i>
                            <span class="ms-2">Editar</span>
                        </button>
                        <button type="button" id="deleteProductBtn" class="btn btn-danger disabled" disabled>Excluir Produto</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal Novo Produto -->
    <div class="modal fade" id="modalNovoProduto" tabindex="-1" role="dialog" aria-labelledby="modalNovoProdutoLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoProdutoLabel">Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="novoProdutoForm" method="POST" novalidate>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="card text-black">
                                    <div class="card-body">
                                        <h2>INFORMAÇÕES</h2>

                                        <div class="mb-3">
                                            <label for="nome" class="form-label">Nome do Produto:</label>
                                            <input type="text" name="nome" id="nome" required class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição:</label>
                                            <textarea name="descricao" id="descricao" required class="form-control"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="quantidade" class="form-label">Quantidade:</label>
                                            <input type="number" name="quantidade" id="quantidade" required class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label for="valor" class="form-label">Preço do Produto:</label>
                                            <input type="number" name="valor" id="valor" required class="form-control" step="0.01">
                                        </div>

                                        <!-- Modal Categoria -->

                                        <!-- Botão para abrir o modal com o iframe -->
                                        <button type="button" class="btn btn-success mt-2" id="btnNovaCategoria">
                                            <i class="fas fa-plus"></i> Nova Categoria
                                        </button>
                                        <!-- Campo de categoria dentro da pagina de cadastrar produto -->
                                        <div class="mb-3">
                                            <label for="categoria" class="form-label">Categoria:</label>
                                            <select name="categoria_id" id="categoria" class="form-control" required>
                                                <option value="">Selecione uma categoria</option>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?= htmlspecialchars($categoria['id']) ?>">
                                                        <?= htmlspecialchars($categoria['nome']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!--Fim campo cadastro-->

                                        <!-- Modal com iframe para carregar a página categoria.php -->
                                        <div class="modal fade" id="iframeModal" tabindex="-1" aria-labelledby="iframeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="iframeModalLabel">Gerenciar Categorias</h5>
                                                        <button type="button" class="btn-close" id="btnCloseIframeModal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Iframe para carregar a página categoria.php -->
                                                        <iframe src="../include/categorias.php" width="100%" height="600px" frameborder="0"></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" id="btnFecharIframeModal">Fechar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            document.getElementById('iframeModal').addEventListener('hidden.bs.modal', function() {
                                                // Recarregar a lista de categorias via AJAX
                                                fetch('../include/get_categorias.php')
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        const categoriaSelect = document.getElementById('categoria');
                                                        categoriaSelect.innerHTML = '<option value="">Selecione uma categoria</option>'; // Limpa as opções atuais
                                                        data.forEach(categoria => {
                                                            const option = document.createElement('option');
                                                            option.value = categoria.id;
                                                            option.textContent = categoria.nome;
                                                            categoriaSelect.appendChild(option);
                                                        });
                                                    })
                                                    .catch(error => console.error('Erro ao carregar categorias:', error));
                                            });
                                        </script>
                                        
                                        <script>
                                            document.getElementById('btnNovaCategoria').addEventListener('click', function() {
                                                // Exibe o modal de categoria sem fechar o modal de cadastro de produto
                                                const categoriaModal = new bootstrap.Modal(document.getElementById('iframeModal'));
                                                categoriaModal.show();
                                            });

                                            document.getElementById('btnCloseIframeModal').addEventListener('click', function() {
                                                // Fecha apenas o modal de categoria
                                                const categoriaModal = bootstrap.Modal.getInstance(document.getElementById('iframeModal'));
                                                categoriaModal.hide();
                                            });

                                            document.getElementById('btnFecharIframeModal').addEventListener('click', function() {
                                                // Fecha apenas o modal de categoria
                                                const categoriaModal = bootstrap.Modal.getInstance(document.getElementById('iframeModal'));
                                                categoriaModal.hide();
                                            });

                                            document.getElementById('iframeModal').addEventListener('hidden.bs.modal', function() {
                                                // Recarregar a lista de categorias após fechar o modal de categoria
                                                console.log('Modal de categoria fechado.');
                                            });
                                        </script>
                                        <!-- Fim da Seção de Categorias -->

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary" id="btnSalvarProduto">Salvar Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Produto -->
    <div class="modal fade" id="modalEditarProduto" tabindex="-1" aria-labelledby="modalEditarProdutoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarProdutoLabel">Editar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editProdutoForm" action="../atualizar.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="card">
                                    <div class="card-body">
                                        <input type="hidden" name="id" id="editProdutoId">

                                        <div class="mb-3">
                                            <label for="editProdutoNome" class="form-label">Nome do Produto:</label>
                                            <input type="text" name="nome" id="editProdutoNome" required class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label for="editProdutoDescricao" class="form-label">Descrição:</label>
                                            <textarea name="descricao" id="editProdutoDescricao" required class="form-control"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="editProdutoValor" class="form-label">Preço do Produto:</label>
                                            <input type="number" name="valor" id="editProdutoValor" required class="form-control" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const novoProdutoForm = document.getElementById('novoProdutoForm');
            const btnSalvar = document.getElementById('btnSalvarProduto');

            if (novoProdutoForm && btnSalvar) {
                novoProdutoForm.addEventListener('submit', async function(event) {
                    event.preventDefault();

                    const formData = new FormData(this);

                    const camposObrigatorios = ['nome', 'descricao', 'quantidade', 'valor', 'categoria_id'];
                    const camposVazios = [];

                    camposObrigatorios.forEach(campo => {
                        const valor = formData.get(campo);
                        if (!valor || valor.trim() === '') {
                            camposVazios.push(campo);
                        }
                    });

                    if (camposVazios.length > 0) {
                        alert(`Por favor, preencha todos os campos obrigatórios: ${camposVazios.join(', ')}`);
                        return;
                    }

                    btnSalvar.disabled = true;
                    btnSalvar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';

                    try {
                        const response = await fetch('../salvar.php', {
                            method: 'POST',
                            body: formData
                        });

                        const responseText = await response.text();
                        let data;
                        try {
                            data = JSON.parse(responseText);
                        } catch (e) {
                            throw new Error(`Resposta inválida do servidor: ${responseText}`);
                        }

                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalNovoProduto'));
                            modal.hide();
                            alert('Produto cadastrado com sucesso!');
                            location.reload();
                        } else {
                            throw new Error(data.message || 'Erro ao cadastrar produto');
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        alert('Erro ao salvar produto: ' + error.message);
                    } finally {
                        btnSalvar.disabled = false;
                        btnSalvar.innerHTML = 'Salvar Produto';
                    }
                });
            }

            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = "auto";
                });
            });

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

            // Adicionando evento para abrir o modal de categoria
            document.getElementById('btnNovaCategoria').addEventListener('click', function() {
                const categoriaModal = new bootstrap.Modal(document.getElementById('modalNovaCategoria'));
                categoriaModal.show();
            });

            // Tornar o modal de categoria arrastável
            $('#modalNovaCategoria').on('shown.bs.modal', function() {
                $('.modal-dialog').draggable({
                    handle: ".modal-header"
                });
            });

            // Evitar que o fechamento do modal de categoria feche o modal de produto
            $('#modalNovaCategoria').on('hidden.bs.modal', function(e) {
                e.stopPropagation();
            });

            // Evitar que o fechamento do modal de categoria feche o modal de produto ao clicar fora do modal
            $('#modalNovaCategoria').on('hidePrevented.bs.modal', function(e) {
                e.preventDefault();
            });

            document.getElementById('btnVoltar').addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNovaCategoria'));
                modal.hide();
                const produtoModal = new bootstrap.Modal(document.getElementById('modalNovoProduto'));
                produtoModal.show();
            });
        });

        function abrirModalEditar(id, nome, descricao, valor) {
            document.getElementById('editProdutoId').value = id;
            document.getElementById('editProdutoNome').value = nome;
            document.getElementById('editProdutoDescricao').value = descricao;
            document.getElementById('editProdutoValor').value = valor;
            const editModal = new bootstrap.Modal(document.getElementById('modalEditarProduto'));
            editModal.show();
        }
    </script>

    <div class="container">
        <div class="row">
            <?php include '../listarprodutos.php'; ?>
        </div>
    </div>
</body>

</html>