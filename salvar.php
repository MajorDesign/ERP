<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once 'conexao.php';

try {
    // Debug dos dados recebidos
    error_log("Dados POST recebidos: " . print_r($_POST, true));

    // Validar dados recebidos
    if (empty($_POST['nome']) || empty($_POST['descricao']) || 
        !isset($_POST['quantidade']) || !isset($_POST['valor'])) {
        throw new Exception("Todos os campos são obrigatórios");
    }

    // Sanitizar e preparar dados
    $dados = [
        'nome' => trim(strip_tags($_POST['nome'])),
        'descricao' => trim(strip_tags($_POST['descricao'])),
        'quantidade' => filter_var($_POST['quantidade'], FILTER_VALIDATE_INT),
        'valor' => filter_var(str_replace(',', '.', $_POST['valor']), FILTER_VALIDATE_FLOAT)
    ];

    // Validar valores após sanitização
    if ($dados['quantidade'] === false || $dados['quantidade'] < 0) {
        throw new Exception("Quantidade inválida");
    }
    if ($dados['valor'] === false || $dados['valor'] < 0) {
        throw new Exception("Valor inválido");
    }

    // Query de inserção
    $sql = "INSERT INTO produtos (nome, descricao, quantidade, valor) 
            VALUES (:nome, :descricao, :quantidade, :valor)";
    
    $stmt = $pdo->prepare($sql);
    
    // Execute com bind de parâmetros
    $resultado = $stmt->execute([
        ':nome' => $dados['nome'],
        ':descricao' => $dados['descricao'],
        ':quantidade' => $dados['quantidade'],
        ':valor' => $dados['valor']
    ]);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Produto cadastrado com sucesso',
            'id' => $pdo->lastInsertId()
        ]);
    } else {
        throw new Exception("Erro ao inserir no banco: " . implode(", ", $stmt->errorInfo()));
    }

} catch (Exception $e) {
    error_log("Erro em salvar.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}