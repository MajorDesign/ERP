<div class="mb-3">
    <label for="categoria" class="form-label">Categoria:</label>
    <div class="input-group">
        <select name="categoria" id="categoria" required class="form-control">
            <option value="">Selecione uma categoria</option>
            <?php
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

<!-- Modal Nova Categoria -->
<div class="modal fade" id="novaCategoriaModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="novaCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novaCategoriaModalLabel">Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="novaCategoriaForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomeCategoria" class="form-label">Nome da Categoria:</label>
                        <input type="text" class="form-control" id="nomeCategoria" name="nome" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarCategoria">Salvar Categoria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const novaCategoriaModal = document.getElementById('novaCategoriaModal');
    const novaCategoriaForm = document.getElementById('novaCategoriaForm');
    const categoriaSelect = document.getElementById('categoria');
    const btnSalvarCategoria = document.getElementById('btnSalvarCategoria');

    if (novaCategoriaModal) {
        novaCategoriaModal.addEventListener('click', function(event) {
            if (event.target === this) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    }

    if (novaCategoriaForm && categoriaSelect && btnSalvarCategoria) {
        novaCategoriaForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            event.stopPropagation();

            btnSalvarCategoria.disabled = true;
            btnSalvarCategoria.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';

            try {
                const formData = new FormData(this);
                const response = await fetch('../salvar_categoria.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    const option = new Option(formData.get('nome'), data.id);
                    categoriaSelect.add(option);
                    categoriaSelect.value = data.id;

                    const categoryModal = bootstrap.Modal.getInstance(novaCategoriaModal);
                    categoryModal.hide();

                    this.reset();
                    alert('Categoria cadastrada com sucesso!');
                } else {
                    throw new Error(data.message || 'Erro ao cadastrar categoria');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao salvar categoria: ' + error.message);
            } finally {
                btnSalvarCategoria.disabled = false;
                btnSalvarCategoria.innerHTML = 'Salvar Categoria';
            }
        });
    }
});
</script>

