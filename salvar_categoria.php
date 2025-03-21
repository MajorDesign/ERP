<?php
require_once 'conexao.php';
header('Content-Type: application/json');

try {
    // Log inicial
    error_log("=== Início do processamento de nova categoria ===");
    error_log("Método da requisição: " . $_SERVER['REQUEST_METHOD']);
    error_log("Dados POST recebidos: " . print_r($_POST, true));

    // Validação
    if (empty($_POST['nome'])) {
        throw new Exception('Nome da categoria é obrigatório');
    }

    // Verifica conexão com o banco
    if (!$pdo) {
        throw new Exception('Conexão com o banco de dados não estabelecida');
    }

    // Log da query
    error_log("Preparando query para inserir categoria: " . $_POST['nome']);

    // Prepara e executa a query
    $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
    if (!$stmt) {
        throw new Exception('Erro ao preparar query: ' . implode(', ', $pdo->errorInfo()));
    }

    $resultado = $stmt->execute([$_POST['nome']]);
    error_log("Query executada. Resultado: " . ($resultado ? "Sucesso" : "Falha"));
    
    if (!$resultado) {
        throw new Exception('Erro na execução: ' . implode(', ', $stmt->errorInfo()));
    }

    $id = $pdo->lastInsertId();
    error_log("Nova categoria inserida com ID: " . $id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Categoria cadastrada com sucesso',
        'id' => $id
    ]);

} catch (Exception $e) {
    error_log("Erro no salvar_categoria.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

error_log("=== Fim do processamento ===");