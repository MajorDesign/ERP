<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Cadastro de Produtos</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .debug-panel {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 300px;
            height: 200px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            overflow-y: auto;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Teste - Cadastro de Produto</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCadastroProduto">
                            Cadastro de Produto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cadastro de Produto -->
    <div class="modal fade" id="modalCadastroProduto" tabindex="-1" aria-labelledby="modalCadastroProdutoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCadastroProdutoLabel">Cadastro de Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário de cadastro de produto -->
                    <form id="produtoForm">
                        <div class="mb-3">
                            <label for="nomeProduto" class="form-label">Nome do Produto:</label>
                            <input type="text" class="form-control" id="nomeProduto" name="nomeProduto" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricaoProduto" class="form-label">Descrição:</label>
                            <textarea class="form-control" id="descricaoProduto" name="descricaoProduto" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria:</label>
                            <div class="input-group">
                                <select name="categoria" id="categoria" required class="form-control">
                                    <option value="">Selecione uma categoria</option>
                                    <?php
                                    require_once '../conexao.php';
                                    try {
                                        $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
                                        while ($categoria = $stmt->fetch()) {
                                            echo "<option value='{$categoria['id']}'>{$categoria['nome']}</option>";
                                        }
                                    } catch (PDOException $e) {
                                        error_log("Erro ao carregar categorias: " . $e->getMessage());
                                        echo "<option value=''>Erro ao carregar categorias</option>";
                                    }
                                    ?>
                                </select>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novaCategoriaModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnSalvarProduto">Salvar Produto</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Categoria -->
    <div class="modal fade" id="novaCategoriaModal" tabindex="-1" aria-labelledby="novaCategoriaModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novaCategoriaModalLabel">Nova Categoria</h5>
                </div>
                <form id="novaCategoriaForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomeCategoria" class="form-label">Nome da Categoria:</label>
                            <input type="text" class="form-control" id="nomeCategoria" name="nome" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnVoltar">Voltar</button>
                        <button type="submit" class="btn btn-primary" id="btnSalvarCategoria">Salvar Categoria</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Painel de Debug -->
    <div class="debug-panel">
        <h6>Log de Debug</h6>
        <div id="debugLog"></div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função para adicionar logs ao painel de debug
        function addDebugLog(message) {
            const debugLog = document.getElementById('debugLog');
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.textContent = `${timestamp} - ${message}`;
            debugLog.appendChild(logEntry);
            debugLog.scrollTop = debugLog.scrollHeight;
            console.log(message); // Also log to console
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.clear();
            addDebugLog('Página carregada');

            // Elementos do formulário de categoria
            const novaCategoriaForm = document.getElementById('novaCategoriaForm');
            const categoriaSelect = document.getElementById('categoria');
            const novaCategoriaModal = document.getElementById('novaCategoriaModal');
            const btnSalvarCategoria = document.getElementById('btnSalvarCategoria');
            const btnVoltar = document.getElementById('btnVoltar');

            if (novaCategoriaForm && categoriaSelect && novaCategoriaModal && btnSalvarCategoria && btnVoltar) {
                addDebugLog('Elementos do formulário de categoria encontrados');

                novaCategoriaForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    btnSalvarCategoria.disabled = true;
                    btnSalvarCategoria.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';

                    try {
                        const formData = new FormData(this);
                        addDebugLog('Dados do formulário: ' + JSON.stringify(Object.fromEntries(formData)));

                        const response = await fetch('../salvar_categoria.php', {
                            method: 'POST',
                            body: formData
                        });

                        addDebugLog('Status da resposta: ' + response.status);

                        // Tenta ler o texto da resposta primeiro
                        const responseText = await response.text();
                        addDebugLog('Resposta bruta: ' + responseText);

                        // Tenta fazer o parse do JSON
                        let data;
                        try {
                            data = JSON.parse(responseText);
                        } catch (e) {
                            throw new Error('Erro ao processar resposta do servidor: ' + responseText);
                        }

                        if (data.success) {
                            // Adiciona nova opção ao select
                            const option = new Option(formData.get('nome'), data.id);
                            categoriaSelect.add(option);
                            categoriaSelect.value = data.id;

                            // Limpa o formulário de categoria
                            this.reset();
                            
                            addDebugLog('Categoria cadastrada com sucesso');
                            alert('Categoria cadastrada com sucesso!');
                        } else {
                            throw new Error(data.message || 'Erro ao cadastrar categoria');
                        }
                    } catch (error) {
                        addDebugLog('Erro: ' + error.message);
                        console.error('Erro completo:', error);
                        alert('Erro ao salvar categoria: ' + error.message);
                    } finally {
                        btnSalvarCategoria.disabled = false;
                        btnSalvarCategoria.innerHTML = 'Salvar Categoria';
                    }
                });

                btnVoltar.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(novaCategoriaModal);
                    modal.hide();
                    const produtoModal = new bootstrap.Modal(document.getElementById('modalCadastroProduto'));
                    produtoModal.show();
                });
            } else {
                addDebugLog('Erro: Elementos do formulário não encontrados');
                console.error('Elementos não encontrados:', {
                    form: novaCategoriaForm,
                    select: categoriaSelect,
                    modal: novaCategoriaModal,
                    button: btnSalvarCategoria,
                    backButton: btnVoltar
                });
            }
        });
    </script>
</body>
</html>