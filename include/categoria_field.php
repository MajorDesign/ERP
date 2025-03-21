<?php
require_once '../conexao.php';

// Buscar categorias do banco de dados
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="form-group">
    <label>Categoria:</label>
    <div class="d-flex gap-2">
        <select name="categoria" id="categoria" class="form-control" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="btn btn-primary" id="cadastrarNovaCategoriaBtn">
            <i class="fas fa-plus"></i> Nova Categoria
        </button>
    </div>
</div>

<!-- Modal Nova Categoria -->
<div class="modal fade" id="novaCategoriaModal" tabindex="-1" aria-labelledby="novaCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novaCategoriaModalLabel">Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="novaCategoriaForm">
                    <div class="mb-3">
                        <label for="nomeCategoria" class="form-label">Nome da Categoria:</label>
                        <input type="text" class="form-control" id="nomeCategoria" name="nome" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="salvarCategoriaBtn" class="btn btn-primary">Salvar Categoria</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const novaCategoriaModal = new bootstrap.Modal(document.getElementById('novaCategoriaModal'));
    
    document.getElementById('cadastrarNovaCategoriaBtn').addEventListener('click', function() {
        novaCategoriaModal.show();
    });

    document.getElementById('salvarCategoriaBtn').addEventListener('click', function(e) {
        e.preventDefault();
        
        const nome = document.getElementById('nomeCategoria').value;
        
        if (!nome) {
            alert('Por favor, preencha o nome da categoria');
            return;
        }
        
        const formData = new FormData();
        formData.append('nome', nome);
        
        fetch('../salvar_categoria.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Adiciona nova categoria ao select
                const select = document.getElementById('categoria');
                const option = new Option(data.nome, data.id);
                select.add(option);
                select.value = data.id;
                
                // Limpa o campo de entrada
                document.getElementById('nomeCategoria').value = '';
                novaCategoriaModal.hide();
                
                alert('Categoria cadastrada com sucesso!');
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao cadastrar categoria: ' + error.message);
        });
    });
});
</script>