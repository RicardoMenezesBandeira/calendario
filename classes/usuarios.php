<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/autorizacao.php';

class Usuario 
{
    private $pdo;
    public $msgError = "";

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    /**
     * CRIAR: Apenas gerenteistradores podem cadastrar novos usuários
     */
    public function cadastrar ($nome, $email, $senha, $tipouser, $equipe=[])
    {
        try {
            // Validar permissão
            $auth = new Autorizacao();
            if (!$auth->isgerente()) {
                $this->msgError = "Sem autorização. Apenas gerentes podem cadastrar usuários.";
                return false;
            }

            // Validar entrada
            if (empty($nome) || empty($email) || empty($senha)) {
                $this->msgError = "Nome, email e senha são obrigatórios.";
                return false;
            }

            if (strlen($senha) < 6) {
                $this->msgError = "Senha deve ter no mínimo 6 caracteres.";
                return false;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->msgError = "Email inválido.";
                return false;
            }

            // Verifica se email já existe
            /*
            $sql = $this->pdo->prepare("SELECT ID_Usuario FROM usuario WHERE email = :e");
            $sql->bindValue(":e", $email);
            $sql->execute();
            
            if ($sql->rowCount() > 0) {
                $this->msgError = "Email já cadastrado.";
                return false;
            }*/

            // Insere novo usuário
            $sql = $this->pdo->prepare(
                "INSERT INTO usuario (nome, email, senha, Tipo_Usuario) 
                 VALUES (:n, :e, :s, :t)"
            );
            $sql->bindValue(":n", $nome);
            $sql->bindValue(":e", $email);
            $sql->bindValue(":s", md5($senha));
            $sql->bindValue(":t", $tipouser);
            $sql->execute();

            $userId = $this->pdo->lastInsertId();

            // Cria registro nas tabelas de perfis conforme tipo
            $this->criarPerfil($tipouser, $userId, $equipe);
            
            return $userId; // Retorna ID do usuário criado
            
        } catch (PDOException $e) {
            $this->msgError = "Erro ao cadastrar: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Cria registro na tabela de perfis específica
     */
    private function criarPerfil($tipouser, $userId, $equipe = null)
    {
        switch ($tipouser) {
            case "colaborador":
                // Validar se equipe foi fornecida e é válida
                if (!$equipe || $equipe === []) {
                    $this->msgError = "Equipe é obrigatória para colaboradores.";
                    throw new Exception("Equipe inválida");
                }
                
                // Converter para int se recebido como string
                $equipeId = intval($equipe);
                
                // Validar se equipe existe no banco
                $checkEquipe = $this->pdo->prepare("SELECT Numero FROM equipe WHERE Numero = :eq");
                $checkEquipe->bindValue(":eq", $equipeId, PDO::PARAM_INT);
                $checkEquipe->execute();
                
                if ($checkEquipe->rowCount() == 0) {
                    $this->msgError = "Equipe selecionada não existe.";
                    throw new Exception("Equipe não encontrada: " . $equipeId);
                }
                
                $sql = $this->pdo->prepare(
                    "INSERT INTO colaboradores (fk_Equipe_Numero, fk_Usuario_ID_Usuario) 
                     VALUES (:t, :i)"
                );
                $sql->bindValue(":t", $equipeId, PDO::PARAM_INT);
                $sql->bindValue(":i", $userId, PDO::PARAM_INT);
                $sql->execute();
                break;

            case "lider":
                $sql = $this->pdo->prepare(
                    "INSERT INTO lider (fk_Usuario_ID_Usuario) VALUES (:i)"
                );
                $sql->bindValue(":i", $userId);
                $sql->execute();
                break;

            case "gerente":
                $sql = $this->pdo->prepare(
                    "INSERT INTO gerente (fk_Usuario_ID_Usuario) VALUES (:i)"
                );
                $sql->bindValue(":i", $userId);
                $sql->execute();
                break;
        }
    }
    public function logar ($nome, $senha)
    {
        $sql = $this->pdo->prepare(
            "SELECT ID_Usuario FROM usuario WHERE nome = :n AND senha = :s"
        );
        $sql->bindValue(":n", $nome);
        $sql->bindValue(":s", md5($senha));
        $sql->execute();
        
        if ($sql->rowCount() > 0) {
            $dado = $sql->fetch();
            // Usar sessão centralizada
            if (!function_exists('setSessionDados')) {
                require_once __DIR__ . '/../config/session.php';
            }
            setSessionDados('ID_Usuario', $dado['ID_Usuario']);
            // Não guardar senha em cookie (inseguro)
            setcookie("login", $nome); // Apenas login, sem senha
            return true;
        } else {
            return false;
        }
    }

    public function editar ($id, $nome, $email, $senha)
    {
        try {
            // Validar permissão: permite que o próprio usuário atualize seu perfil ou gerenteistradores atualizem qualquer perfil
            $auth = new Autorizacao();
            $usuarioAtualId = $auth->getIdUsuario();

            if (!($usuarioAtualId == $id || $auth->isgerente())) {
                $this->msgError = "Sem autorização. Apenas gerentes podem atualizar outros usuários.";
                return false;
            }

            // Validar entrada
            if (empty($id) || empty($nome) || empty($email) || empty($senha)) {
                $this->msgError = "ID, nome, email e senha são obrigatórios.";
                return false;
            }

            if (strlen($senha) < 6) {
                $this->msgError = "Senha deve ter no mínimo 6 caracteres.";
                return false;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->msgError = "Email inválido.";
                return false;
            }

            // Verifica se usuário existe
            $check = $this->pdo->prepare("SELECT ID_Usuario FROM usuario WHERE ID_Usuario = :id");
            $check->bindValue(":id", $id, PDO::PARAM_INT);
            $check->execute();

            if ($check->rowCount() == 0) {
                $this->msgError = "Usuário não encontrado.";
                return false;
            }

            // Verifica se email já está em uso por outro usuário
            $checkEmail = $this->pdo->prepare(
                "SELECT ID_Usuario FROM usuario WHERE email = :e AND ID_Usuario != :id"
            );
            $checkEmail->bindValue(":e", $email);
            $checkEmail->bindValue(":id", $id, PDO::PARAM_INT);
            $checkEmail->execute();

            if ($checkEmail->rowCount() > 0) {
                $this->msgError = "Email já está em uso por outro usuário.";
                return false;
            }

            // Atualizar usuário
            $sql = "UPDATE usuario SET nome = :n, senha = :s, email = :e WHERE ID_Usuario = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->bindValue(":n", $nome);
            $stmt->bindValue(":s", md5($senha));
            $stmt->bindValue(":e", $email);
            $result = $stmt->execute();

            return $result && $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->msgError = "Erro ao atualizar: " . $e->getMessage();
            return false;
        }
    }


}

?>