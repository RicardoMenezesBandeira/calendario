<?php
require_once 'classes/usuarios.php';

$u = new Usuario();

if(isset($_POST['nome'])) {
    $nome = addslashes($_POST['nome']);
    $email = addslashes($_POST['email']);
    $senha = addslashes($_POST['senha']);
    // Default para novo domínio: colaborador
    $tipouser = isset($_POST['tipouser']) ? $_POST['tipouser'] : 'colaborador';

    $resultado = $u->cadastrar($nome, $email, $senha, $tipouser);
    
    if($resultado) {
        echo "Cadastrado com sucesso! Acesse para entrar!";
        header("location:index.php");
    } else {
        echo "Erro: " . $u->msgError;
    }
}
?>
