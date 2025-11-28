<?php

require_once 'config/database.php';
require_once 'classes/autorizacao.php';

session_start();

Autorizacao::validargerente("criar relacionamentos");

try {
    $equipe = isset($_POST['equipe']) ? (int)$_POST['equipe'] : 0;
    $lider = isset($_POST['lider']) ? (int)$_POST['lider'] : 0;

    if ($equipe <= 0 || $lider <= 0) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'erro' => 'Equipe e líder são obrigatórios.'
        ]);
        exit;
    }

    $pdo = getPDO();

    $teste = $pdo->prepare(
        "SELECT * FROM lidera 
         WHERE fk_Equipe_Numero = :equipe 
         AND fk_Lider_ID_Lider = :lider"
    );
    $teste->bindValue(":equipe", $equipe, PDO::PARAM_INT);
    $teste->bindValue(":lider", $lider, PDO::PARAM_INT);
    $teste->execute();

    if ($teste->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'sucesso' => false,
            'erro' => 'Esta relação líder-equipe já existe.'
        ]);
        exit;
    }

    $sql = $pdo->prepare(
        "INSERT INTO lidera (fk_Lider_ID_Lider, fk_Equipe_Numero) 
         VALUES (:lider, :equipe)"
    );
    $sql->bindValue(":lider", $lider, PDO::PARAM_INT);
    $sql->bindValue(":equipe", $equipe, PDO::PARAM_INT);
    $sql->execute();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Relacionamento líder-equipe criado com sucesso.'
    ]);

} catch (PDOException $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao criar relacionamentos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro: ' . $e->getMessage()
    ]);
}
?>
