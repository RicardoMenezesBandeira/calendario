<?php
/**
 * ARQUIVO DE DEBUG - Testar Deleção de Eventos
 * Use este arquivo para verificar se a deleção está funcionando
 * URL: http://localhost/calendario-uff/debug_delete.php?id=1
 */

require_once 'config/database.php';
require_once 'config/session.php';

if (!estaAutenticado()) {
    echo "Você precisa estar autenticado. <a href='auth_login.php'>Login</a>";
    exit;
}

echo "<h2>Debug - Teste de Deleção</h2>";
echo "<p>Tipo de usuário: " . getTipoUsuario() . "</p>";

$pdo = getPDO();

// Mostrar marcações
echo "<h3>Marcações no Banco de Dados:</h3>";
$stmt = $pdo->query("SELECT ID_Marcacao, titulo, fk_Lider_ID_Lider, fk_Equipe_Numero, Data FROM marcacao LIMIT 10");
$marcacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Título</th><th>Líder ID</th><th>Equipe</th><th>Data</th><th>Ação</th></tr>";
foreach ($marcacoes as $m) {
    echo "<tr>";
    echo "<td>" . $m['ID_Marcacao'] . "</td>";
    echo "<td>" . $m['titulo'] . "</td>";
    echo "<td>" . $m['fk_Lider_ID_Lider'] . "</td>";
    echo "<td>" . $m['fk_Equipe_Numero'] . "</td>";
    echo "<td>" . $m['Data'] . "</td>";
    echo "<td><a href='debug_delete.php?delete=" . $m['ID_Marcacao'] . "' onclick=\"return confirm('Deletar?')\">Deletar</a></td>";
    echo "</tr>";
}
echo "</table>";

// Se requisição de deleção
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    echo "<h3>Tentando deletar ID: " . $id . "</h3>";
    
    try {
        $stmt = $pdo->prepare("SELECT fk_Lider_ID_Lider FROM marcacao WHERE ID_Marcacao = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            echo "<p style='color: red;'>ERRO: Marcação não encontrada</p>";
        } else {
            $marcacao = $stmt->fetch(PDO::FETCH_ASSOC);
            $tipoUsuario = getTipoUsuario();
            $idLider = getSessionDados('ID_Lider');
            
            echo "<p>Tipo: $tipoUsuario | Líder ID: $idLider | Marcação Líder ID: " . $marcacao['fk_Lider_ID_Lider'] . "</p>";
            
            // Verificar permissão
            if ($tipoUsuario === 'colaborador') {
                echo "<p style='color: red;'>ERRO: Colaboradores não podem deletar</p>";
            } else if ($tipoUsuario === 'lider' && $marcacao['fk_Lider_ID_Lider'] != $idLider) {
                echo "<p style='color: red;'>ERRO: Você pode deletar apenas seus próprios eventos</p>";
            } else {
                // Deletar
                $delStmt = $pdo->prepare("DELETE FROM marcacao WHERE ID_Marcacao = :id");
                $delStmt->bindValue(':id', $id, PDO::PARAM_INT);
                
                if ($delStmt->execute()) {
                    echo "<p style='color: green;'><strong>✓ Deleção bem-sucedida!</strong></p>";
                    echo "<p><a href='debug_delete.php'>Voltar</a></p>";
                } else {
                    echo "<p style='color: red;'>ERRO: Falha ao executar DELETE</p>";
                }
            }
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>ERRO PDO: " . $e->getMessage() . "</p>";
    }
}
?>
