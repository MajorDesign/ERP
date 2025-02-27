<!-- filepath: /c:/wamp64/www/Projeto/listarprodutos.php -->
<?php
require_once 'conexao.php';

try {
    $stmt = $pdo->query("SELECT * FROM produtos");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar produtos: " . $e->getMessage();
    $produtos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .highlight-row {
            background-color: #ffe5aa !important;
        }
        .table-hover tbody tr:hover {
            background-color: transparent !important; /* Desativa o hover do Bootstrap */
        }
        .custom-table tbody tr.highlight-row {
            background-color: #ffe5aa !important;
        }
        /* Adicionando mais especificidade para garantir que a cor de fundo seja aplicada */
        .custom-table tbody tr.highlight-row td {
            background-color: #ffe5aa !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Lista de Produtos</h2>
        <div id="tableContainer">
            <table class="table table-bordered custom-table" id="productTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr class="product-row clickableRow" data-id="<?= $produto['id'] ?>"
                            data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                            data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                            data-valor="<?= htmlspecialchars($produto['valor']) ?>"
                            data-status="<?= htmlspecialchars($produto['status']) ?>">
                            <td><?= htmlspecialchars($produto['id']) ?></td>
                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                            <td><?= htmlspecialchars($produto['descricao']) ?></td>
                            <td>R$ <?= number_format($produto['valor'], 2, ',', '.') ?></td>
                            <td>
                                <?php if ($produto['status'] == 1): ?>
                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Ativo</span>
                                <?php else: ?>
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Inativo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="../atualizar.php" method="POST">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nome:</label>
                            <input type="text" id="edit-nome" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição:</label>
                            <textarea id="edit-descricao" name="descricao" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preço:</label>
                            <input type="number" id="edit-valor" name="valor" class="form-control" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </form>
                    <button type="button" id="toggleStatus" class="btn btn-warning mt-3">Alterar Status</button>
                    <button type="button" id="deleteProduct" class="btn btn-danger mt-3">Excluir Produto</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("DOM totalmente carregado e analisado");
            document.querySelectorAll(".product-row").forEach(row => {
                console.log("Adicionando evento de clique à linha:", row);
                row.addEventListener("click", function() {
                    // Remove a classe 'highlight-row' de todas as linhas
                    document.querySelectorAll(".product-row").forEach(r => r.classList.remove("highlight-row"));
                    // Adiciona a classe 'highlight-row' à linha clicada
                    this.classList.add("highlight-row");
                    console.log("Linha selecionada:", this); // Adicione este log para depuração

                    document.getElementById("edit-id").value = this.getAttribute("data-id");
                    document.getElementById("edit-nome").value = this.getAttribute("data-nome");
                    document.getElementById("edit-descricao").value = this.getAttribute("data-descricao");
                    document.getElementById("edit-valor").value = this.getAttribute("data-valor");

                    const status = this.getAttribute("data-status");
                    const toggleButton = document.getElementById("toggleStatus");
                    toggleButton.textContent = (status == 1) ? "Desativar Produto" : "Ativar Produto";
                    toggleButton.dataset.id = this.getAttribute("data-id");
                    toggleButton.dataset.status = status;

                    const deleteButton = document.getElementById("deleteProduct");
                    deleteButton.dataset.id = this.getAttribute("data-id");
                });

                row.addEventListener("dblclick", function() {
                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                });
            });

            document.getElementById("toggleStatus").addEventListener("click", function() {
                const id = this.dataset.id;
                const statusAtual = this.dataset.status;
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

            document.getElementById("editForm").addEventListener("submit", function(event) {
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

            document.getElementById("deleteProduct").addEventListener("click", function() {
                const id = this.dataset.id;

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
        });
    </script>
</body>

</html>