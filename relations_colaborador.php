<?php
require_once 'config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$pdo = getPDO();

// Recebe dados
$colaboradorId = intval($_POST['colaborador_id'] ?? 0);
$equipe = intval($_POST['equipe'] ?? 0);

// Validação
if ($colaboradorId <= 0 || $equipe <= 0) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Colaborador e equipe são obrigatórios."
    ]);
    exit;
}

try {
    $update = $pdo->prepare("
        UPDATE colaboradores 
        SET fk_Equipe_Numero = :equipe
        WHERE ID_Colaborador = :id
    ");

    $update->execute([
        ":equipe" => $equipe,
        ":id" => $colaboradorId
    ]);

    echo json_encode(["sucesso" => true]);

} catch (PDOException $e) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Erro no banco: " . $e->getMessage()
    ]);
}
