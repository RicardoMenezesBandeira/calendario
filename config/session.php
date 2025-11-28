<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function estaAutenticado() {
    return isset($_SESSION['ID_Usuario']) && !empty($_SESSION['ID_Usuario']);
}

function validarAutenticacao($redirect = 'auth_login.php') {
    if (!estaAutenticado()) {
        header('Location: ' . $redirect);
        exit;
    }
}


function getIdUsuario() {
    return isset($_SESSION['ID_Usuario']) ? $_SESSION['ID_Usuario'] : null;
}


function getTipoUsuario() {
    return isset($_SESSION['Tipo_Usuario']) ? $_SESSION['Tipo_Usuario'] : null;
}


function setSessionDados($chave, $valor) {
    $_SESSION[$chave] = $valor;
}


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


function sincronizarCookie() {
    if (isset($_COOKIE['login']) && !estaAutenticado()) {
        setcookie("login", "", time() - 3600);
        setcookie("senha", "", time() - 3600);
        return false;
    }
    
    return true;
}
?>
