<?php

require_once 'config/database.php';
require_once 'config/session.php'; // Já inicia session

// Apenas usuários autenticados podem criar marcações (gerente/lider/colaborador)
if (!estaAutenticado()) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => 'Não autenticado']);
    exit;
}

$pdo = getPDO();

// Validar entrada
$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$equipe = isset($_POST['equipe']) ? (int)$_POST['equipe'] : 0;
$hora = isset($_POST['hora']) ? $_POST['hora'] : '';
$data = isset($_POST['data']) ? $_POST['data'] : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';

// Validar campos obrigatórios
if (empty($titulo) || empty($equipe) || empty($hora) || empty($data) || empty($descricao)) {
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
    $chave = NULL; // fk_Lider_ID_Lider
    $tipo = getTipoUsuario();
    if ($tipo === 'lider' && getSessionDados('ID_Lider')) {
        $chave = (int)getSessionDados('ID_Lider');
    } else {
        // Tentar atribuir o lider responsável pela equipe (lidera)
    $stmtProf = $pdo->prepare("SELECT fk_Lider_ID_Lider FROM lidera WHERE fk_Equipe_Numero = :equipe LIMIT 1");
    $stmtProf->bindValue(':equipe', $equipe, PDO::PARAM_INT);
        $stmtProf->execute();
        if ($stmtProf->rowCount() > 0) {
            $resProf = $stmtProf->fetch(PDO::FETCH_ASSOC);
            $chave = (int)$resProf['fk_Lider_ID_Lider'];
        }
    }

    // Se não encontramos lider vinculado, abortar
    if (empty($chave)) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'erro' => 'Nenhum lider vinculado à equipe. Contate o gerente.'
        ]);
        exit;
    }

    // Insere marcação com prepared statement
    $sql = $pdo->prepare(
        "INSERT INTO marcacao (titulo, fk_Equipe_Numero, Data, Hora, Descricao, fk_Lider_ID_Lider)
         VALUES (:t, :equipe, :d, :h, :de, :c)"
    );
    $sql->bindValue(":t", $titulo);
    $sql->bindValue(":equipe", $equipe, PDO::PARAM_INT);
    $sql->bindValue(":d", $data);
    $sql->bindValue(":h", $hora);
    $sql->bindValue(":de", $descricao);
    $sql->bindValue(":c", $chave);
    $sql->execute();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Marcação criada com sucesso.',
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao criar marcação: ' . $e->getMessage()
    ]);
}
?>
