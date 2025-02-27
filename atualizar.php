<?php
require_once 'conexao.php';

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método inválido."]);
    exit;
}

// Verifica se os dados obrigatórios foram enviados
if (!isset($_POST['id'], $_POST['nome'], $_POST['descricao'], $_POST['valor'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$id = intval($_POST['id']);
$nome = trim($_POST['nome']);
$descricao = trim($_POST['descricao']);
$valor = floatval($_POST['valor']);

// Atualiza os dados do produto no banco
$stmt = $pdo->prepare("UPDATE produtos SET nome = ?, descricao = ?, valor = ? WHERE id = ?");
$atualizado = $stmt->execute([$nome, $descricao, $valor, $id]);

if ($atualizado) {
    echo json_encode(["success" => true, "message" => "Produto atualizado com sucesso!"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao atualizar o produto."]);
}
?>
