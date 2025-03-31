<?php
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

try {
    $result = $conn->query("SELECT id, nome FROM categorias ORDER BY id DESC");

    if ($result && $result->num_rows > 0) {
        echo '<div class="d-flex justify-content-center mt-4">';
        echo '<div class="card" style="width: 70%; max-height: 500px; overflow-y: auto;">'; // Contêiner centralizado e com barra de rolagem
        echo '<div class="card-header bg-escuro text-white d-flex justify-content-between align-items-center">';
        echo '<h5 class="mb-0">Lista de Categorias</h5>';
        echo '<button class="btn btn-success btn-sm" id="adicionarCategoriaBtn">Adicionar Categoria</button>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nome</th>';
        echo '<th>Ações</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['nome']) . '</td>';
            echo '<td>';
            echo '<button class="btn btn-warning btn-sm editarCategoriaBtn" data-id="' . $row['id'] . '" data-nome="' . htmlspecialchars($row['nome']) . '">Editar</button> ';
            echo '<button class="btn btn-danger btn-sm excluirCategoriaBtn" data-id="' . $row['id'] . '">Excluir</button>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning text-center">Nenhuma categoria cadastrada.</div>';
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger text-center">Erro ao carregar categorias: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
<link rel="stylesheet" href="../css/css.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adicionarCategoriaModal = new bootstrap.Modal(document.getElementById('adicionarCategoriaModal'));

        // Evento para abrir o modal de adicionar categoria
        document.getElementById('adicionarCategoriaBtn').addEventListener('click', function() {
            adicionarCategoriaModal.show();
        });

        // Evento para salvar a nova categoria
        document.getElementById('salvarCategoriaBtn').addEventListener('click', function() {
            const nome = document.getElementById('nomeCategoria').value;

            if (!nome) {
                alert('Por favor, preencha o nome da categoria');
                return;
            }

            const formData = new FormData();
            formData.append('nome', nome);

            fetch('adicionar_categoria.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Categoria adicionada com sucesso!');
                        location.reload(); // Recarrega a página para atualizar a lista de categorias
                    } else {
                        throw new Error(data.message || 'Erro desconhecido');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao adicionar categoria: ' + error.message);
                });
        });
    });
</script>

<!-- Modal Adicionar Categoria -->
<div class="modal fade" id="adicionarCategoriaModal" tabindex="-1" aria-labelledby="adicionarCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarCategoriaModalLabel">Adicionar Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adicionarCategoriaForm">
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
<!-- Modal Editar Categoria -->
<div class="modal fade" id="editarCategoriaModal" tabindex="-1" aria-labelledby="editarCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarCategoriaModalLabel">Editar Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarCategoriaForm">
                    <input type="hidden" id="editarCategoriaId" name="id">
                    <div class="mb-3">
                        <label for="editarNomeCategoria" class="form-label">Nome da Categoria:</label>
                        <input type="text" class="form-control" id="editarNomeCategoria" name="nome" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="salvarEdicaoCategoriaBtn" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa o modal de adicionar categoria
        const adicionarCategoriaModal = new bootstrap.Modal(document.getElementById('adicionarCategoriaModal'));

        // Evento para abrir o modal de adicionar categoria
        document.getElementById('adicionarCategoriaBtn').addEventListener('click', function() {
            adicionarCategoriaModal.show();
        });

        // Evento para salvar a nova categoria
        document.addEventListener('DOMContentLoaded', function() {
            const adicionarCategoriaModal = new bootstrap.Modal(document.getElementById('adicionarCategoriaModal'));

            // Evento para abrir o modal de adicionar categoria
            document.getElementById('adicionarCategoriaBtn').addEventListener('click', function() {
                adicionarCategoriaModal.show();
            });

            // Remove qualquer evento duplicado antes de adicionar o evento de clique
            const salvarCategoriaBtn = document.getElementById('salvarCategoriaBtn');
            salvarCategoriaBtn.removeEventListener('click', salvarCategoriaHandler);

            // Define o evento de clique para salvar a nova categoria
            salvarCategoriaBtn.addEventListener('click', salvarCategoriaHandler);

            function salvarCategoriaHandler() {
                const nome = document.getElementById('nomeCategoria').value;

                if (!nome) {
                    alert('Por favor, preencha o nome da categoria');
                    return;
                }

                const formData = new FormData();
                formData.append('nome', nome);

                fetch('adicionar_categoria.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Categoria adicionada com sucesso!');
                            location.reload(); // Recarrega a página para atualizar a lista de categorias
                        } else {
                            throw new Error(data.message || 'Erro desconhecido');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao adicionar categoria: ' + error.message);
                    });
            }
        });
        // Inicializa o modal de editar categoria
        const editarCategoriaModal = new bootstrap.Modal(document.getElementById('editarCategoriaModal'));

        // Evento para abrir o modal de edição
        document.querySelectorAll('.editarCategoriaBtn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nome = this.getAttribute('data-nome');

                // Preenche os campos do modal com os dados da categoria
                document.getElementById('editarCategoriaId').value = id;
                document.getElementById('editarNomeCategoria').value = nome;

                // Exibe o modal de edição
                editarCategoriaModal.show();
            });
        });

        // Evento para salvar as alterações de edição
        document.getElementById('salvarEdicaoCategoriaBtn').addEventListener('click', function() {
            const id = document.getElementById('editarCategoriaId').value;
            const nome = document.getElementById('editarNomeCategoria').value;

            if (!nome) {
                alert('Por favor, preencha o nome da categoria');
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('nome', nome);

            fetch('editar_categoria.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Categoria editada com sucesso!');
                        location.reload(); // Recarrega a página para atualizar a lista de categorias
                    } else {
                        throw new Error(data.message || 'Erro desconhecido');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao editar categoria: ' + error.message);
                });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializa o modal de editar categoria
        const editarCategoriaModal = new bootstrap.Modal(document.getElementById('editarCategoriaModal'));

        // Evento para abrir o modal de edição
        document.querySelectorAll('.editarCategoriaBtn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nome = this.getAttribute('data-nome');

                // Preenche os campos do modal com os dados da categoria
                document.getElementById('editarCategoriaId').value = id;
                document.getElementById('editarNomeCategoria').value = nome;

                // Exibe o modal de edição
                editarCategoriaModal.show();
            });
        });

        // Evento para excluir a categoria
        document.querySelectorAll('.excluirCategoriaBtn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');

                if (confirm('Tem certeza que deseja excluir esta categoria?')) {
                    fetch('excluir_categoria.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Categoria excluída com sucesso!');
                            location.reload(); // Recarrega a página para atualizar a lista de categorias
                        } else {
                            throw new Error(data.message || 'Erro desconhecido');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao excluir categoria: ' + error.message);
                    });
                }
            });
        });
    });
</script>