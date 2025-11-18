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
$idMarcacao = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$equipe = isset($_POST['equipe']) ? (int)$_POST['equipe'] : 0;
$hora = isset($_POST['hora']) ? $_POST['hora'] : '';
$data = isset($_POST['data']) ? $_POST['data'] : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';

// Validar campos obrigatórios
if (!$idMarcacao || empty($titulo) || empty($equipe) || empty($hora) || empty($data) || empty($descricao)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Todos os campos são obrigatórios.'
    ]);
    exit;
}

// Validar formato de data
if (!strtotime($data)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Data inválida.'
    ]);
    exit;
}

// Validar formato de hora
if (!strtotime($hora)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Hora inválida.'
    ]);
    exit;
}

try {
    // Buscar marcação para validar autorização
    $stmt = $pdo->prepare("SELECT fk_Lider_ID_Lider FROM marcacao WHERE ID_Marcacao = :id");
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
    // - Gerente pode editar qualquer evento
    // - Líder pode editar apenas eventos que criou (fk_Lider_ID_Lider)
    // - Colaborador não pode editar
    if ($tipoUsuario === 'colaborador') {
        http_response_code(403);
        echo json_encode(['sucesso' => false, 'erro' => 'Colaboradores não podem editar marcações']);
        exit;
    }

    if ($tipoUsuario === 'lider' && $marcacao['fk_Lider_ID_Lider'] != $idLider) {
        http_response_code(403);
        echo json_encode(['sucesso' => false, 'erro' => 'Você pode editar apenas suas próprias marcações']);
        exit;
    }

    // Atualizar marcação
    $sql = $pdo->prepare(
        "UPDATE marcacao 
         SET titulo = :t, fk_Equipe_Numero = :equipe, Data = :d, Hora = :h, Descricao = :de
         WHERE ID_Marcacao = :id"
    );
    $sql->bindValue(":t", $titulo);
    $sql->bindValue(":equipe", $equipe, PDO::PARAM_INT);
    $sql->bindValue(":d", $data);
    $sql->bindValue(":h", $hora);
    $sql->bindValue(":de", $descricao);
    $sql->bindValue(":id", $idMarcacao, PDO::PARAM_INT);
    $sql->execute();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Marcação atualizada com sucesso.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao atualizar marcação: ' . $e->getMessage()
    ]);
}
?>
