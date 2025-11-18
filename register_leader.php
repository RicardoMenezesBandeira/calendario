<?php
session_start();
if (!isset($_SESSION['Tipo_Usuario']) || $_SESSION['Tipo_Usuario'] != 'gerente') {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Líder</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body class="p-4 bg-light">

    <div class="container">
        <h2 class="text-center mb-4">Cadastrar Novo Líder</h2>
        <form action="register_gerente.php" method="POST" class="card p-4">
            <input type="hidden" name="tipouser" value="lider">

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success mt-3">Cadastrar</button>
        </form>
    </div>
</body>
</html>
