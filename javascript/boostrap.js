document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".product-row").forEach(row => {
        row.addEventListener("click", function() {
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

        row.addEventListener("dblclick", function() {
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });
    });
});