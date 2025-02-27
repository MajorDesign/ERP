<?php
require_once 'conexao.php';

// Verifica se o ID foi enviado corretamente e é um número válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erro: ID do produto não fornecido ou inválido.");
}

$id = (int)$_GET['id']; // Converte para inteiro

// Verifica se a conexão PDO está ativa
if (!isset($pdo)) {
    die("Erro: Conexão com o banco de dados não estabelecida.");
}

// Busca os dados do produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o produto foi encontrado
if (!$produto) {
    die("Erro: Produto não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Editar Produto</h2>

        <form action="atualizar.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($produto['id']) ?>">

            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição:</label>
                <textarea name="descricao" class="form-control" required><?= htmlspecialchars($produto['descricao']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Preço:</label>
                <input type="number" name="valor" class="form-control" step="0.01" value="<?= htmlspecialchars($produto['valor']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="listar_produtos.php" class="btn btn-secondary">Cancelar</a>
            <button type="button" class="btn btn-danger" onclick="excluirProduto(<?= htmlspecialchars($produto['id']) ?>)">Excluir Produto</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function excluirProduto(id) {
            if (confirm("Tem certeza que deseja excluir este produto?")) {
                fetch('excluir_produto.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Produto excluído com sucesso!");
                            window.location.href = 'listar_produtos.php';
                        } else {
                            alert("Erro ao excluir produto: " + data.error);
                        }
                    })
                    .catch(error => console.error("Erro:", error));
            }
        }
    </script>
</body>

</html>