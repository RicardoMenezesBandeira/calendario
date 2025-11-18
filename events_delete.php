<?php

require_once 'config/database.php';
require_once 'config/session.php';

// Validar autenticação
if (!estaAutenticado()) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => 'Não autenticado']);
    exit;
}

$pdo = getPDO();
$idMarcacao = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$idMarcacao) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'erro' => 'ID de marcação inválido']);
    exit;
}

try {
    // Buscar marcação para validar autorização
    $stmt = $pdo->prepare("SELECT fk_Lider_ID_Lider, fk_Equipe_Numero FROM marcacao WHERE ID_Marcacao = :id");
    $stmt->bindValue(':id', $idMarcacao, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'erro' => 'Marcação não encontrada']);
        exit;
    }

    $marcacao = $stmt->fetch(PDO::FETCH_ASSOC);
    $tipoUsuario = getTipoUsuario();
    $idLider = getSessionDados('ID_Lider');

    // Validação de autorização:
    // - Gerente pode deletar qualquer evento
    // - Líder pode deletar apenas eventos que criou (fk_Lider_ID_Lider)
    // - Colaborador não pode deletar
    if ($tipoUsuario === 'colaborador') {
        http_response_code(403);
        echo json_encode(['sucesso' => false, 'erro' => 'Colaboradores não podem deletar marcações']);
        exit;
    }

    if ($tipoUsuario === 'lider' && $marcacao['fk_Lider_ID_Lider'] != $idLider) {
        http_response_code(403);
        echo json_encode(['sucesso' => false, 'erro' => 'Você pode deletar apenas suas próprias marcações']);
        exit;
    }

    // Deletar marcação
    $sql = $pdo->prepare("DELETE FROM marcacao WHERE ID_Marcacao = :id");
    $sql->bindValue(':id', $idMarcacao, PDO::PARAM_INT);
    $sql->execute();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Marcação deletada com sucesso'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao deletar marcação: ' . $e->getMessage()
    ]);
}
?>
