document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".product-row").forEach(row => {
        row.addEventListener("click", function () {
            // Remove a classe 'selected' de todas as linhas
            document.querySelectorAll(".product-row").forEach(r => r.classList.remove("selected"));
            // Adiciona a classe 'selected' à linha clicada
            this.classList.add("selected");
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

        row.addEventListener("dblclick", function () {
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });
    });
});

novoProdutoForm.addEventListener('submit', async function(event) {
    event.preventDefault();

    try {
        const formData = new FormData(this);
        
        // Debug - mostrar dados sendo enviados
        console.log('Dados sendo enviados:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        btnSalvar.disabled = true;
        btnSalvar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';

        const response = await fetch('../salvar.php', {
            method: 'POST',
            body: formData
        });

        const responseText = await response.text();
        console.log('Resposta do servidor:', responseText);

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            throw new Error(`Resposta inválida do servidor: ${responseText}`);
        }

        if (!data.success) {
            throw new Error(data.message || 'Erro ao cadastrar produto');
        }

        // Produto foi salvo com sucesso
        const modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
        modal.hide();
        alert('Produto cadastrado com sucesso!');
        window.location.reload();

    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao salvar produto: ' + error.message);
    } finally {
        btnSalvar.disabled = false;
        btnSalvar.innerHTML = 'Salvar Produto';
    }
});

// Substitua a função abrirModalEditar existente por esta:
function abrirModalEditar(id, nome, descricao, valor) {
    // Preenche os campos
    document.getElementById('editProdutoId').value = id;
    document.getElementById('editProdutoNome').value = nome;
    document.getElementById('editProdutoDescricao').value = descricao;
    document.getElementById('editProdutoValor').value = valor;

    // Remove qualquer backdrop existente
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

    // Inicializa e abre o modal
    const editModal = new bootstrap.Modal(document.getElementById('editModal'), {
        backdrop: 'static',
        keyboard: false
    });
    editModal.show();
}

// Adicione este código junto com seus outros event listeners
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        });
    }
});

// script categoria

// Adicionar no bloco de script existente
document.addEventListener("DOMContentLoaded", function() {
    // ...código existente...

    // Função para atualizar lista de categorias
    window.atualizarCategorias = async function() {
        try {
            const response = await fetch('../get_categorias.php');
            const data = await response.json();
            
            const select = document.querySelector('select[name="categoria_id"]');
            select.innerHTML = '<option value="">Selecione uma categoria</option>';
            
            data.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.id;
                option.textContent = categoria.nome;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Erro ao atualizar categorias:', error);
        }
    }

    // Evento para quando o modal de categoria for fechado
    const categoriaModal = document.getElementById('categoriaModal');
    if (categoriaModal) {
        categoriaModal.addEventListener('hidden.bs.modal', function () {
            atualizarCategorias();
        });
    }
});

// Adicione ao seu bloco de script existente
document.addEventListener("DOMContentLoaded", function() {
    // Atualizar categorias quando o modal for fechado
    const categoriaModal = document.getElementById('categoriaModal');
    if (categoriaModal) {
        categoriaModal.addEventListener('hidden.bs.modal', function () {
            // Recarregar lista de categorias
            fetch('../get_categorias.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.querySelector('select[name="categoria_id"]');
                    select.innerHTML = '<option value="">Selecione uma categoria</option>';
                    data.forEach(categoria => {
                        select.innerHTML += `<option value="${categoria.id}">${categoria.nome}</option>`;
                    });
                })
                .catch(error => console.error('Erro ao atualizar categorias:', error));
        });
    }
});