<?php
require_once 'classes/usuarios.php';
require_once 'config/database.php'; // Necessário para getPDO()
require_once 'config/session.php';

// Iniciou session automaticamente via config/session.php

$erro = null; // Variável para armazenar erro

if(isset($_POST['nome'])) {
    try {
        $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
        $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
        
        // Validar entrada
        if (empty($nome) || empty($senha)) {
            throw new Exception("Nome e senha são obrigatórios.");
        }
        
        $u = new Usuario();
        
        if($u->logar($nome, $senha)){
            // Guardar tipo de usuário na sessão também
            $pdo = getPDO();
            $sql = $pdo->prepare("SELECT Tipo_Usuario FROM usuario WHERE nome = :n");
            $sql->bindValue(":n", $nome);
            $sql->execute();
            $usuario = $sql->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                setSessionDados('Tipo_Usuario', $usuario['Tipo_Usuario']);
            }
            
            header("location:index.php");
            exit;
        } else {
            throw new Exception("nome e/ou senha estão incorretos!");
        }
        
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <style>
        .alert-custom { margin-top: 10px; }
    </style>
</head>
<body class="bg-success">
    <div class="container-fluid">
        <div class="row header">
            <div class="col d-flex justify-content-center">
                <h1 class="text-monospace display-2 text-light mt-5">Calendário UFF</h1>
            </div>
        </div>
        
        <div class="row flex-column align-items-center section">
            <div class="col-3 card login p-3 mb-5 bg-white">
                <div class="card-body">
                    <?php if ($erro): ?>
                    <div class="alert alert-danger alert-custom" role="alert">
                        <strong>Erro:</strong> <?php echo htmlspecialchars($erro); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form id="login-form" class="form" action="auth_profile.php" method="POST">
                        <h2 class="text-center text-success card-title mb-4">Login</h2>
                        <div class="form-group">
                            <input type="text" name="nome" id="user" class="form-control" 
                                   placeholder="Nome" required 
                                   value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <input type="password" name="senha" id="senha" class="form-control" 
                                   placeholder="Senha" required>
                        </div>

                        <div class="form-group text-center mt-4">
                            <input type="submit" name="submit" class="btn btn-outline-success w-100" value="Entrar">
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
