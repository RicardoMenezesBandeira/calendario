<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/login.css">
</head>
<body class="bg-success">

    <div class="container-fluid">

        <div class="row header">
            <div class="col d-flex justify-content-center">
                <h1 class="text-monospace display-2 text-light mt-5">Calendário de Equipe</h1> 
            </div>
        </div>

        <div class="row flex-column align-items-center section">
            <div class="col-3 card login p-3 mb-5 bg-white roundedrounded">
                <div class="card-body">
                    <form id="login-form" class="form" action="auth_profile.php" method="POST">
                        <h2 class="text-center text-success card-title mb-4">Login</h2>
                        <div class="form-group">
                            <input type="text" name="email" id="user" class="form-control m-auto" placeholder="E-mail" required>
                        </div>

                        <div class="form-group">
                            <input type="password" name="senha" id="senha" class="form-control m-auto" placeholder="Senha" required>
                        </div>

                        <div class="form-group text-center mt-4 mx-4">
                            <input type="submit" name="submit" class="btn btn-outline-success w-100" value="Entrar">
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </div>
 
</body>
</html>
