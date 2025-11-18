<?php

require_once 'config/database.php';
require_once 'config/session.php';

// Validar autenticação
if (!estaAutenticado()) {
    http_response_code(403);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

// Validar entrada
$mon = isset($_GET["mon"]) ? (int)$_GET["mon"] : 0;
$ano = isset($_GET["ano"]) ? (int)$_GET["ano"] : 0;
$equipe = isset($_GET["equipe"]) ? $_GET["equipe"] : "all";

// Validar mês e ano
if ($mon < 1 || $mon > 12 || $ano < 2000 || $ano > 2100) {
    http_response_code(400);
    echo json_encode(['erro' => 'Mês ou ano inválido']);
    exit;
}

try {
    $pdo = getPDO();

    if ($equipe == "all") {
        $query = "SELECT ID_Marcacao, titulo, fk_Equipe_Numero, Data, Hora, Descricao, fk_Lider_ID_Lider FROM marcacao WHERE MONTH(Data) = :mon AND YEAR(Data) = :ano LIMIT 500";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':mon', $mon, PDO::PARAM_INT);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Validar se equipe é número
        $equipe = (int)$equipe;
        if ($equipe <= 0) {
            http_response_code(400);
            echo json_encode(['erro' => 'Equipe inválida']);
            exit;
        }
        
        $query = "SELECT ID_Marcacao, titulo, fk_Equipe_Numero, Data, Hora, Descricao, fk_Lider_ID_Lider FROM marcacao WHERE MONTH(Data) = :mon AND YEAR(Data) = :ano AND fk_Equipe_Numero = :equipe LIMIT 500";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':mon', $mon, PDO::PARAM_INT);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':equipe', $equipe, PDO::PARAM_INT);
        $stmt->execute();
    }

    $marcacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($marcacoes);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar marcações']);
}
?>
