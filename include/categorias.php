<?php
// filepath: /C:/wamp64/www/Projeto/pages/categorias.php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar o formulário de cadastro de categoria
    $nomeCategoria = trim($_POST['nome_categoria'] ?? '');

    if (!empty($nomeCategoria)) {
        try {
            $stmt = $conn->prepare("INSERT INTO categorias (nome) VALUES (?)");
            $stmt->bind_param("s", $nomeCategoria);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $mensagem = "Categoria cadastrada com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar a categoria.";
            }
        } catch (Exception $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    } else {
        $mensagem = "O campo Nome da Categoria é obrigatório.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    
    
</head>
<body>
    <div class="container mt-5">




        <!-- Mensagem de Feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <!-- Lista de Categorias -->
        
        <div>
            <?php include __DIR__ . '/categoria_field.php'; ?>
        </div>
    </div>
</body>
</html>