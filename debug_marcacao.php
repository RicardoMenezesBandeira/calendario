<?php
/**
 * DEBUG - Verificar Campos de Marcação
 * URL: http://localhost/calendario-uff/debug_marcacao.php
 */

require_once 'config/database.php';
require_once 'config/session.php';

if (!estaAutenticado()) {
    echo "Você precisa estar autenticado. <a href='auth_login.php'>Login</a>";
    exit;
}

$pdo = getPDO();

echo "<h2>Debug - Estrutura de Marcações</h2>";

// Pegar primeira marcação
$stmt = $pdo->query("SELECT * FROM marcacao LIMIT 1");
$marcacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$marcacao) {
    echo "<p style='color: orange;'>Nenhuma marcação encontrada no banco.</p>";
} else {
    echo "<h3>Primeira Marcação do Banco:</h3>";
    echo "<pre>";
    print_r($marcacao);
    echo "</pre>";
    
    echo "<h3>Chaves Retornadas:</h3>";
    echo "<ul>";
    foreach (array_keys($marcacao) as $key) {
        echo "<li><code>" . htmlspecialchars($key) . "</code></li>";
    }
    echo "</ul>";
    
    echo "<h3>JSON Retornado (como enviado ao JavaScript):</h3>";
    echo "<pre>";
    echo htmlspecialchars(json_encode([$marcacao], JSON_PRETTY_PRINT));
    echo "</pre>";
}

// Testar endpoint events.php
echo "<h3>Teste do Endpoint: /events.php</h3>";
$mes = date('m');
$ano = date('Y');
echo "<p>Mês: $mes | Ano: $ano</p>";
echo "<p><a href='events.php?mon=$mes&ano=$ano&equipe=all' target='_blank'>Ver JSON (all equipes)</a></p>";
?>
