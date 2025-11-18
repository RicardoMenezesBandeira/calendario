<?php
require_once 'config/database.php';
require_once 'classes/usuarios.php';

$u = new Usuario();

if(isset($_POST['nome'])) {
    $nome = addslashes($_POST['nome']);
    $email = addslashes($_POST['email']);
    $senha = addslashes($_POST['senha']);
    
    // Determinar tipo de usuário baseado no contexto
    // Se foi enviado 'equipe', é um colaborador; senão, verifica se foi enviado tipo específico
    if(isset($_POST['equipe'])) {
        // Registro de colaborador
        $tipouser = 'colaborador';
        $equipe = intval($_POST['equipe']);
        
        // Passar equipe como parâmetro na função cadastrar
        $resultado = $u->cadastrar($nome, $email, $senha, $tipouser, $equipe);
        
        if($resultado) {
            echo "Colaborador cadastrado com sucesso!";
            header("location:index.php");
        } else {
            echo "Erro: " . $u->msgError;
        }
    } else {
        // Registro padrão (sem equipe) - compatibilidade com formulários antigos
        $tipouser = isset($_POST['tipouser']) ? $_POST['tipouser'] : 'colaborador';
        
        $resultado = $u->cadastrar($nome, $email, $senha, $tipouser);
        
        if($resultado) {
            echo "Cadastrado com sucesso!";
            header("location:index.php");
        } else {
            echo "Erro: " . $u->msgError;
        }
    }
} else {
    header("location:no_allow.php");
}
?>
