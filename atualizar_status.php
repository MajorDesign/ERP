<?php
require_once 'conexao.php';

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método inválido."]);
    exit;
}

// Verifica se os parâmetros necessários foram enviados
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode(["success" => false, "error" => "Dados incompletos"]);
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];

// Atualiza o status no banco de dados
$stmt = $pdo->prepare("UPDATE produtos SET status = ? WHERE id = ?");
$sucesso = $stmt->execute([$status, $id]);

if ($sucesso) {
    echo json_encode(["success" => true, "message" => "Status atualizado com sucesso"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao atualizar o status"]);
}
?>
