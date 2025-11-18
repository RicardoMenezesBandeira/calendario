<?php
require_once 'config/session.php';
require_once 'classes/usuarios.php';

validarAutenticacao('auth_login.php');

$u = new Usuario();

if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha'])) {
    // Obter id do usuário autenticado a partir da sessão centralizada
    $id = getIdUsuario();

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($id) || empty($nome) || empty($email) || empty($senha)) {
        echo "Dados incompletos.";
        exit;
    }

    if ($u->editar($id, $nome, $email, $senha)) {
        header("location:index.php");
        exit;
    } else {
        echo "Erro: " . htmlspecialchars($u->msgError);
        exit;
    }

} else {
    echo "Dados incompletos.";
    exit;
}
?>
