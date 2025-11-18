<?php

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'classes/autorizacao.php';

// Validar autorização (apenas gerentes)
Autorizacao::validargerente("adicionar equipe");

try {
    // Validar entrada
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    
    // Validar campos
    if (empty($nome)) {
        throw new Exception("Nome da equipe é obrigatório.");
    }
    
    if (strlen($nome) > 100) {
        throw new Exception("Nome muito longo (máx. 100 caracteres).");
    }
    
    $pdo = getPDO();

    $sql = $pdo->prepare(
        "INSERT INTO equipe (Nome_Equipe) 
         VALUES (:n)"
    );
    $sql->bindValue(":n", $nome);
    $sql->execute();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Equipe criada com sucesso.',
        'id' => $pdo->lastInsertId()
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => $e->getMessage()
    ]);
}
?>
