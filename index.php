<!DOCTYPE html>
<?php

require_once 'config/database.php';
require_once 'config/session.php';

// Validar autenticação
validarAutenticacao('about.php');

// Inicializar variáveis
$pdo = getPDO();
$dados = null;
$equipes = [];
$equipe = [];
$idProf = [];

try {
    // Buscar dados do usuário autenticado
    $userId = getIdUsuario();
    $sql = $pdo->prepare("
        SELECT u.ID_Usuario, u.nome, u.email, u.Tipo_Usuario, 
               c.fk_Equipe_Numero, l.ID_Lider
        FROM usuario u
        LEFT JOIN colaboradores c ON u.ID_Usuario = c.fk_Usuario_ID_Usuario
        LEFT JOIN lider l ON u.ID_Usuario = l.fk_Usuario_ID_Usuario
        WHERE u.ID_Usuario = :id
    ");
    $sql->bindValue(":id", $userId, PDO::PARAM_INT);
    $sql->execute();
    $dados = $sql->fetch(PDO::FETCH_ASSOC);
    
    if (!$dados) {
        header('location:about.php');
        exit;
    }
    
    // Guardar na sessão
    setSessionDados('nome', $dados['nome']);
    setSessionDados('Tipo_Usuario', $dados['Tipo_Usuario']);
    
    // Guardar dados específicos por tipo
    if ($dados['Tipo_Usuario'] == "colaborador" && isset($dados['fk_Equipe_Numero'])) {
        $equipe['fk_Equipe_Numero'] = $dados['fk_Equipe_Numero'];
    } else if ($dados['Tipo_Usuario'] == "lider" && isset($dados['ID_Lider'])) {
        setSessionDados('ID_Lider', $dados['ID_Lider']);
        $idProf['ID_Lider'] = $dados['ID_Lider'];
    }
    
    // Buscar equipes conforme tipo de usuário
    if ($dados['Tipo_Usuario'] == "gerente") {
        // Gerente: pega todas as equipes
        $sqlequipe = $pdo->query("SELECT Numero, Nome_Equipe FROM equipe");
        $equipes = $sqlequipe->fetchAll(PDO::FETCH_ASSOC);
    } else if ($dados['Tipo_Usuario'] == "lider") {
        // Lider: pega equipes que lidera
        if (isset($dados['ID_Lider'])) {
            $stmt = $pdo->prepare("
                SELECT equipe.Numero, equipe.Nome_Equipe 
                FROM lidera
                INNER JOIN equipe ON lidera.fk_Equipe_Numero = equipe.Numero
                WHERE lidera.fk_Lider_ID_Lider = :idLider
            ");
            $stmt->bindValue(":idLider", $dados['ID_Lider'], PDO::PARAM_INT);
            $stmt->execute();
            $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
} catch (Exception $e) {
    die("Erro ao carregar usuário: " . $e->getMessage());
}
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <!-- Ícones locais (substitui kit externo que causava 403) -->
    <link rel="stylesheet" href="fluxograma_files/libs/bootstrap/bootstrap-icons.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="assets/calendario.css">
    <link rel="stylesheet" href="assets/main.css">
</head>
<body>

    <div class="container-fluid">
          
        <header class="row text-light">
            <nav class="col navbar navbar-expand-lg navbar-dark">
                <img src="assets/img/logo-calendario-branco.svg" alt="" style="height: 40px;">
                <ul class="navbar-nav row ml-auto">                    
                   

                    <?php 
                    if ($dados['Tipo_Usuario'] == "gerente"){
                         echo ' <li>
                         <h3>'.$_SESSION['nome']. '</h3>
                         </li>
                         <li>
                         
                         <div class="dropdown show  dropleft">
                             <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 <i class="h4 fas fa-users-cog"></i>
                             </a>
     
                             <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#adduser">Adicionar Usuario</a>
                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addequipe">Adicionar Equipe</a>
                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addrelacao">Estabelecer relação líder-equipe</a>
                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#edituser">Edita colaborador</a>
                             </div>
                             </div>
                         </li>';

                    }
                    ?>

                    <li class="nav-item col">
                      <a class="nav-link" href="#"  data-toggle="modal" data-target="#usuario"><i class="h4 fas fa-address-card"></i></a>
                    </li>
                    <li class="nav-item col">
                      <a class="nav-link" href="auth_logout.php"><i class="h4 fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
              </nav>
        </header>

        <section class="row">

        <?php 
        
           if ($dados['Tipo_Usuario'] != "colaborador") {

            echo "<div class='col-12 col-md bg-light order-2 order-md-1' id='equipe'>
                    <div class='row'>
                        <div class='col'> 
                        <p class='h3 my-4 d-flex justify-content-center align-self-center text-success'>Equipes</p>
                        </div>
                    </div>
                 <div class='row overflow-hidden'>
                    <div class='d-flex flex-md-column col equipebtn'>";

            // --- Exibição das equipes (já buscadas no PHP principal) ---
                    if ($equipes) {
                        foreach ($equipes as $equipe) {
                            echo '<div class="card text-center mb-2 equipeoption" onclick="trocaequipe(event)" data-equipe="' . $equipe["Numero"] . '" >
                                <div class="card-body" data-equipe="' . $equipe["Numero"] . '">
                                    <p class="card-title" data-equipe="' . $equipe["Numero"] . '">' . $equipe["Numero"] . '</p>
                                    <p class="card-subtitle" data-equipe="' . $equipe["Numero"] . '">' . (isset($equipe["Nome_Equipe"]) ? $equipe["Nome_Equipe"] : $equipe["Curso"]) . '</p>
                                </div>
                            </div>';
                        }
                    }

                    echo "
                        </div>
                        </div>
                        </div>";
        }

        ?>

            <div  class="col-12 d-flex align-items-center justify-content-center col-md-7 order-1 order-md-2 bg-light" id="principal">

                    <div class="calendario w-100 d-flex flex-column">
                        <div class="data-headline meses text-light d-flex d-flex justify-content-between align-items-baseline p-3">
                            <i class="h1 fas fa-angle-left anter"></i>
                            <div class="data">
                                <h1></h1>
                            </div>
                            <i class="h1 fas fa-angle-right prox"></i>
                        </div>

                        <div class="diasSemana text-center d-flex justify-content-around align-items-baseline">
                            <div class="">Dom</div>
                            <div class="">Seg</div>
                            <div class="">Ter</div>
                            <div class="">Qua</div>
                            <div class="">Qui</div>
                            <div class="">Sex</div>
                            <div class="">Sab</div>
                        </div>

                        <div class="dias d-flex justify-content-around text-center align-content-stretch align-self-stretch flex-wrap">
                        </div>
                    </div> 

            </div>

            <div class="col-12 col-md bg-light overflow-hidden order-3" id="tarefas">
                <div class="row">
                    <div class="col d-flex justify-content-center"> 
                        <p class="h3 my-4 text-success mr-3">Tarefas</p>
                        <button class="btn btn-sm btn-outline-success rounded-circle align-self-center" data-toggle="modal" data-target="#addmarcacao"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="row overflow-hidden">
                    <div class="col marcacaoTarefa d-flex flex-column" id="accordion"> 
                    </div>
                </div>
            </div>

        </section>

        <footer class="row ">
            <div class="col text-light d-flex justify-content-center align-self-center">
                <p text-center>Todos os direitos reservados</p>
            </div>
        </footer>
    </div>



    <div class="modal fade" id="addmarcacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Marcação</h5>
                </div>

                <div class="modal-body text-center mb-1">

                    <form class="form container-fluid" id="form-addmarcarcao">

                        <div class="form-group row">
                            <label class="text-success">Titulo:</label>
                            <input type="text" name="titulo" class="form-control m-auto titulomarc" class="form-control m-auto" required>
                        </div>

                        <div class="form-group row">
                            <label class="text-success">Equipe:</label>
                            <input type="text" name="equipe" id="tarefaequipe" class="form-control m-auto" readonly class="form-control-plaintext" value="">
                        </div>

                        <div class="form-group row ">
                            <label class="text-success">Hora:</label>
                            <input type="time" class="form-control m-auto" name="hora" required>
                        </div>

                        <div class="form-group row ">
                            <label class="text-success">Data:</label>
                            <input type="date" name="data" class="form-control m-auto" required>
                        </div>

                        <div class="form-group row">
                            <label for="descricao" class="text-success">Descricao:</label>
                            <textarea class="form-control m-auto" maxlength="100" name="descricao" required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="addMarcacao(event)">Adicionar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="editMarcacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Editar Marcação</h5>
                </div>

                <div class="modal-body text-center mb-1">

                    <form class="form container-fluid" id="form-editmarcacao">

                        <input type="hidden" id="editMarcacaoId" name="id" value="">

                        <div class="form-group row">
                            <label class="text-success">Título:</label>
                            <input type="text" id="editMarcacaoTitulo" class="form-control m-auto" required>
                        </div>

                        <div class="form-group row">
                            <label class="text-success">Equipe:</label>
                            <select id="editMarcacaoEquipe" class="form-control m-auto" required>
                                <?php 
                                    $lista = $pdo->query("SELECT Numero, Nome_Equipe FROM equipe");
                                    $resul = $lista->fetchAll(PDO::FETCH_ASSOC);

                                    if($resul){
                                        foreach ($resul as $i){
                                            echo "<option value='".$i['Numero']."'>".$i['Nome_Equipe']."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group row">
                            <label class="text-success">Hora:</label>
                            <input type="time" id="editMarcacaoHora" class="form-control m-auto" required>
                        </div>

                        <div class="form-group row">
                            <label class="text-success">Data:</label>
                            <input type="date" id="editMarcacaoData" class="form-control m-auto" required>
                        </div>

                        <div class="form-group row">
                            <label class="text-success">Descrição:</label>
                            <textarea id="editMarcacaoDescricao" class="form-control m-auto" maxlength="100" required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="salvarEdicaoMarcacao(event)">Salvar Alterações</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addequipe" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Equipe</h5>
                </div>

                <div class="modal-body text-center mb-1">
                <form class="formulario" action="equipes_add.php" method="POST">

                        <div class="form-group">
                            <input type="text" name="nome" class="form-control m-auto" placeholder="Nome da Equipe" required  maxlength="100">
                        </div>

                        </div>  
                    
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Adicionar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addrelacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Relacionamento</h5>
                </div>

                <div class="modal-body text-center mb-1">
                <form class="formulario" id="form-relacionar">

                    <div class="form-group mx-5">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Equipe</label>
                            <select class="custom-select" name="equipe">
                                <?php 
                                    $lista = $pdo->query("SELECT Numero, Nome_Equipe FROM equipe");
                                    $resul = $lista->fetchAll(PDO::FETCH_ASSOC);

                                    if($resul){
                                        foreach ($resul as $i){
                                            echo "<option value=".$i['Numero'].">".$i['Numero']." - ".$i['Nome_Equipe']."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mx-5">
                        <div class="input-group-prepend">
                            <label class="input-group-text">ID Líder</label>
                            <select class="custom-select" name="lider">
                                <?php 
                                    $lista = $pdo->query("SELECT lider.ID_Lider, usuario.nome FROM lider, usuario WHERE lider.fk_Usuario_ID_Usuario = usuario.ID_Usuario");
                                    $resul = $lista->fetchAll(PDO::FETCH_ASSOC);

                                    if($resul){
                                        foreach ($resul as $i){
                                            echo "<option value=".$i['ID_Lider'].">".$i['ID_Lider']." - ".$i['nome']."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                        <div class="modal-footer">
                            <button onclick="relacionar()" class="btn btn-success">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                </form>
            </div>  

            </div>
        </div>
    </div>

   <div class="modal fade" id="edituser" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Colaboradores</h5>
            </div>

            <div class="modal-body">

                <!-- CAMPO DE BUSCA -->
                <input type="text" 
                       id="buscarColaborador" 
                       class="form-control mb-3"
                       placeholder="Buscar colaborador...">

                <!-- LISTA DE COLABORADORES -->
                <div id="listaColaboradores">
                    <?php
                        $sql = $pdo->query("
                          SELECT 
    colaboradores.ID_Colaborador,
    usuario.nome,
    equipe.Nome_Equipe,
    equipe.Numero AS equipeNumero,
    'colaborador' AS tipo
FROM colaboradores
INNER JOIN usuario 
    ON usuario.ID_Usuario = colaboradores.fk_Usuario_ID_Usuario
LEFT JOIN equipe 
    ON equipe.Numero = colaboradores.fk_Equipe_Numero

UNION

SELECT
    lider.ID_Lider AS ID_Colaborador,
    usuario.nome,
    NULL AS Nome_Equipe,
    NULL AS equipeNumero,
    'lider' AS tipo
FROM lider
INNER JOIN usuario
    ON usuario.ID_Usuario = lider.fk_Usuario_ID_Usuario

ORDER BY nome ASC

                        ");

                        $colaboradores = $sql->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($colaboradores as $c):
                    ?>

                    <div class="card p-3 mb-2 item-colaborador">
                        <div class="row align-items-center">

                            <div class="col-md-4">
                                <strong><?= $c['nome'] ?></strong>
                            </div>

                          <!-- SE FOR COLABORADOR: SELECT + SALVAR + EXCLUIR -->
<?php if ($c['tipo'] === 'colaborador'): ?>

    <div class="col-md-4">
        <select class="form-control select-equipe" 
                data-id="<?= $c['ID_Colaborador'] ?>">
            <?php
                $equipes = $pdo->query("SELECT Numero, Nome_Equipe FROM equipe")->fetchAll();

                foreach ($equipes as $e) {
                    $selected = ($e['Numero'] == $c['equipeNumero']) ? "selected" : "";
                    echo "<option value='{$e['Numero']}' $selected>{$e['Nome_Equipe']}</option>";
                }
            ?>
        </select>
    </div>

    <div class="col-md-4">
        <button class="btn btn-success" 
                onclick="salvarEquipe(<?= $c['ID_Colaborador'] ?>)">
            Salvar
        </button>
        <button class="btn btn-danger btn-sm"
            onclick="deletarColaborador(<?= $c['ID_Colaborador'] ?>)">
            Excluir
        </button>
    </div>

<!-- SE FOR LÍDER: SOMENTE BOTÃO EXCLUIR -->
<?php else: ?>

    <div class="col-md-8 text-right">
        
    </div>

<?php endif; ?>

                           
                            

                        </div>
                    </div>

                    <?php endforeach; ?>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" 
                        class="btn btn-secondary" 
                        data-dismiss="modal">Fechar</button>
            </div>

        </div>
    </div>
</div>


   <div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Adicionar Usuário</h5>
            </div>

            <div class="modal-body text-center mb-1">

                <form class="formulario" action="register_gerente.php" method="POST">

                    <h2 class="text-center text-success card-title mb-4">Registro</h2>

                    <div class="form-group">
                        <input type="text" name="nome" class="form-control m-auto" placeholder="Nome Completo" 
                               required maxlength="30">
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" class="form-control m-auto" placeholder="E-mail"
                               required maxlength="40">
                    </div>

                    <div class="form-group">
                        <input type="password" name="senha" id="senha" class="form-control m-auto"
                               placeholder="Senha (mínimo 6 caracteres)" required minlength="6" maxlength="15">
                    </div>

                    <div class="form-group form-check text-left">
                        <input class="form-check-input" type="radio" name="tipouser" value="gerente" checked>
                        <label class="form-check-label">Gerente</label>
                    </div>

                    <div class="form-group form-check text-left">
                        <input class="form-check-input" type="radio" name="tipouser" value="lider">
                        <label class="form-check-label">Líder</label>
                    </div>

                    <div class="form-group form-check text-left">
                        <input class="form-check-input" type="radio" name="tipouser" value="colaborador">
                        <label class="form-check-label">Colaborador</label>
                    </div>

                    <!-- SELECT QUE APARECE APENAS PARA COLABORADOR -->
                    <div class="form-group mt-3" id="selectEquipe" style="display:none;">
                        <label>Selecione a equipe:</label>
                        <select name="equipe" class="form-control">
                            <option value="">Selecione...</option>

                             <?php 
                                    $lista = $pdo->query("SELECT Numero, Nome_Equipe FROM equipe");
                                    $resul = $lista->fetchAll(PDO::FETCH_ASSOC);

                                    if($resul){
                                        foreach ($resul as $i){
                                            echo "<option value=".$i['Numero'].">".$i['Numero']." - ".$i['Nome_Equipe']."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </select>

                    </div>
                    <!-- SELECT QUE APARECE APENAS PARA LÍDER -->
                    <div class="form-group mt-3" id="selectEquipeLider" style="display:none;">
                        <label>Selecione a equipe que irá liderar:</label>
                        <select name="equipe_lidera" class="form-control">
                            <option value="">Selecione...</option>

                            <?php 
                                $lista = $pdo->query("SELECT Numero, Nome_Equipe FROM equipe");
                                $resul = $lista->fetchAll(PDO::FETCH_ASSOC);

                                if($resul){
                                    foreach ($resul as $i){
                                        echo "<option value='".$i['Numero']."'>".$i['Numero']." - ".$i['Nome_Equipe']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Salvar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>

            </form>

        </div>
    </div>
</div>



    <div class="modal fade" id="usuario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Dados do usuario.</h5>
                </div>

                <div class="modal-body text-center mb-1">

                    <form class="form container-fluid" action="profile_edit.php" method="post">

                        <div class="form-group row">
                            <label for="email" class="text-success">Email:</label>
                            <input type="text" name="email" id="user" readonly class="form-control-plaintext" value="<?php echo $dados['email']?>" class="form-control m-auto">
                        </div>

                        <div class="form-group row ">
                            <label for="Nome" class="text-success">Nome:</label>
                            <input type="text" name="nome" value="<?php echo htmlspecialchars(getSessionDados('nome', '')); ?>" class="form-control m-auto" required>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="text-success">Senha:</label>
                            <!-- Nunca preencher senha no campo por segurança -->
                            <input type="password" name="senha" class="form-control m-auto" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>

                    </form>



                </div>

            </div>
        </div>
    </div>



    <script>
        var Tipo_User = "<?php echo $dados['Tipo_Usuario'];?>";
        var idLider = 0;
        var equipe = "";

        if(Tipo_User == "colaborador"){
            equipe = "<?php 
                if(isset($equipe['fk_Equipe_Numero'])){
                    echo $equipe['fk_Equipe_Numero'];
                }else{
                    echo "";
                }?>";
        } else if(Tipo_User == "lider"){
            idLider = parseInt("<?php 
                if(isset($dados['ID_Lider']) && $dados['ID_Lider']){
                    echo $dados['ID_Lider'];
                }else{
                    echo '0';
                }
            ?>");
        }
        
        console.log("Tipo de usuário:", Tipo_User);
        console.log("ID Líder:", idLider);
        console.log("Equipe:", equipe);
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="script/calendario.js"></script>
    <script src="script/tarefas.js"></script>
    
</body>
</html>