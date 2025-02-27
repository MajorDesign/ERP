<?php
require_once '../conexao.php';

// Buscar categorias do banco de dados
$stmt = $pdo->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<label>Categoria:</label>
<select name="categoria" id="categoria" class="form-control" required>
    <?php foreach ($categorias as $categoria): ?>
        <option value="<?= htmlspecialchars($categoria['nome']) ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
    <?php endforeach; ?>
    <option value="nova_categoria">Adicionar Nova Categoria</option>
</select><br>
<button type="button" class="btn btn-secondary" id="cadastrarNovaCategoriaBtn">Cadastrar Nova Categoria</button>

<!-- Modal para cadastrar nova categoria -->
<div class="modal fade" id="novaCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="novaCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novaCategoriaModalLabel">Cadastrar Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="novaCategoriaForm" action="../salvar_categoria.php" method="POST">
                    <div class="mb-3">
                        <label for="nomeCategoria" class="form-label">Nome da Categoria</label>
                        <input type="text" class="form-control" id="nomeCategoria" name="nome" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Categoria</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('cadastrarNovaCategoriaBtn').addEventListener('click', function() {
        const novaCategoriaModal = new bootstrap.Modal(document.getElementById('novaCategoriaModal'));
        novaCategoriaModal.show();
    });

    document.getElementById('novaCategoriaForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenir o comportamento padrão do formulário
        fetch(this.action, {
            method: this.method,
            body: new FormData(this)
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar a lista de categorias
                const categoriaSelect = document.getElementById('categoria');
                const novaCategoriaOption = document.createElement('option');
                novaCategoriaOption.value = data.nome;
                novaCategoriaOption.text = data.nome;
                categoriaSelect.add(novaCategoriaOption);
                categoriaSelect.value = data.nome;
                const novaCategoriaModal = bootstrap.Modal.getInstance(document.getElementById('novaCategoriaModal'));
                novaCategoriaModal.hide();
            } else {
                alert("Erro ao salvar categoria.");
            }
        }).catch(error => console.error("Erro:", error));
    });
</script>