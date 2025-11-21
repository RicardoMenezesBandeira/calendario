<?php
require_once 'config/database.php';
require_once 'classes/usuarios.php';

$u = new Usuario();

if (!isset($_POST['nome'])) {
    header("location:no_allow.php");
    exit;
}

$nome  = addslashes($_POST['nome']);
$email = addslashes($_POST['email']);
$senha = addslashes($_POST['senha']);

// O formulário DEVE enviar o tipo de usuário
if (!isset($_POST['tipouser'])) {
    echo "Erro: tipo de usuário não informado.";
    exit;
}

$tipouser = $_POST['tipouser'];

// Tratamento baseado no tipo de usuário
switch ($tipouser) {

    case 'colaborador':
        // Colaborador OBRIGATORIAMENTE precisa de equipe
        if (!isset($_POST['equipe']) || empty($_POST['equipe'])) {
            echo "Erro: equipe é obrigatória para colaboradores!";
            exit;
        }

        $equipe = intval($_POST['equipe']);

        $resultado = $u->cadastrar($nome, $email, $senha, 'colaborador', $equipe);

        if ($resultado) {
            header("location:index.php");
            exit;
        } else {
            echo "Erro: " . $u->msgError;
            exit;
        }

    break;

    case 'lider':
    case 'gerente':
        // Líder e gerente NÃO utilizam equipe
        $resultado = $u->cadastrar($nome, $email, $senha, $tipouser);

        if ($resultado) {
            header("location:index.php");
            exit;
        } else {
            echo "Erro: " . $u->msgError;
            exit;
        }

    break;

    default:
        echo "Erro: tipo de usuário inválido.";
        exit;
}

?>
