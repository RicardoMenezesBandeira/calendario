<?php
/**
 * Gerenciamento centralizado de sessões
 * Evita múltiplas chamadas de session_start()
 */

// Iniciar sessão apenas uma vez
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se usuário está autenticado
 * @return bool
 */
function estaAutenticado() {
    return isset($_SESSION['ID_Usuario']) && !empty($_SESSION['ID_Usuario']);
}

/**
 * Verifica se usuário está autenticado, senão redireciona
 * @param string $redirect Página para redirecionar
 */
function validarAutenticacao($redirect = 'auth_login.php') {
    if (!estaAutenticado()) {
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Retorna o ID do usuário autenticado
 * @return int|null
 */
function getIdUsuario() {
    return isset($_SESSION['ID_Usuario']) ? $_SESSION['ID_Usuario'] : null;
}

/**
 * Retorna o tipo de usuário autenticado
 * @return string|null
 */
function getTipoUsuario() {
    return isset($_SESSION['Tipo_Usuario']) ? $_SESSION['Tipo_Usuario'] : null;
}

/**
 * Define dados na sessão
 * @param string $chave
 * @param mixed $valor
 */
function setSessionDados($chave, $valor) {
    $_SESSION[$chave] = $valor;
}

/**
 * Obtém dados da sessão
 * @param string $chave
 * @param mixed $padrao Valor padrão se não existir
 * @return mixed
 */
function getSessionDados($chave, $padrao = null) {
    return isset($_SESSION[$chave]) ? $_SESSION[$chave] : $padrao;
}

/**
 * Destroi sessão e cookies
 */
function finalizarSessao() {
    // Limpar todos os dados de sessão
    $_SESSION = array();
    
    // Limpar cookies
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir sessão
    session_destroy();
    
    // Remover cookies personalizados
    setcookie("login", "", time() - 3600);
    setcookie("senha", "", time() - 3600);
}

/**
 * Verifica e sincroniza cookie com session
 * Necessário para manter consistência
 */
function sincronizarCookie() {
    // Se tem cookie mas não tem sessão = sessão expirou
    if (isset($_COOKIE['login']) && !estaAutenticado()) {
        // Limpar cookie obsoleto
        setcookie("login", "", time() - 3600);
        setcookie("senha", "", time() - 3600);
        return false;
    }
    
    return true;
}
?>
