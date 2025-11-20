<?php
require_once 'config/database.php';
header("Content-Type: application/json; charset=UTF-8");

$pdo = getPDO();

$colaboradorId = intval($_POST['colaborador_id'] ?? 0);

if ($colaboradorId <= 0) {
    echo json_encode(["sucesso" => false, "erro" => "ID inválido"]);
    exit;
}

try {
    // 1. Obter usuário do colaborador
    $busca = $pdo->prepare("
        SELECT fk_Usuario_ID_Usuario 
        FROM colaboradores 
        WHERE ID_Colaborador = :id
    ");
    $busca->execute([":id" => $colaboradorId]);

    $row = $busca->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["sucesso" => false, "erro" => "Colaborador não encontrado"]);
        exit;
    }

    $usuarioId = $row["fk_Usuario_ID_Usuario"];

    // 2. Apagar o usuário (o colaborador será apagado automaticamente)
    $delete = $pdo->prepare("
        DELETE FROM usuario WHERE ID_Usuario = :uid
    ");
    $delete->execute([":uid" => $usuarioId]);

    echo json_encode(["sucesso" => true]);

} catch (PDOException $e) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Erro ao deletar: " . $e->getMessage()
    ]);
}
