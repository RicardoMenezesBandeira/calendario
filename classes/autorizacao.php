<?php
/**
 * Classe para gerenciar permissões e autorização
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

class Autorizacao
{
    private $pdo;
    private $tipoUsuario;
    private $idUsuario;

    public function __construct()
    {
        // Não chamar session_start() aqui - já centralizado em config/session.php
        $this->pdo = getPDO();
        
        // Obter dados da sessão (já validada)
        $this->idUsuario = getIdUsuario();
        
        if ($this->idUsuario) {
            // Buscar tipo de usuário
            $sql = $this->pdo->prepare("SELECT Tipo_Usuario FROM usuario WHERE ID_Usuario = :id");
            $sql->bindValue(":id", $this->idUsuario, PDO::PARAM_INT);
            $sql->execute();
            $usuario = $sql->fetch(PDO::FETCH_ASSOC);
            
            $this->tipoUsuario = $usuario ? $usuario['Tipo_Usuario'] : null;
        }
    }

    /**
     * Verifica se o usuário é gerenteistrador
     */
    public function isgerente()
    {
        // No novo domínio, o gerenteistrador foi renomeado para 'gerente'
        return $this->tipoUsuario === 'gerente';
    }

    /**
     * Verifica se o usuário é Lider
     */
    public function isLider()
    {
        return $this->tipoUsuario === 'lider';
    }

    /**
     * Verifica se o usuário é aluno
     */
    public function isAluno()
    {
        // Aluno -> Colaborador
        return $this->tipoUsuario === 'colaborador';
    }

    /**
     * Verifica se o usuário pode CRIAR (apenas gerentes)
     */
    public function podecriar()
    {
        return $this->isgerente();
    }

    /**
     * Verifica se o usuário pode ATUALIZAR (apenas gerentes)
     */
    public function podeAtualizar()
    {
        return $this->isgerente();
    }

    /**
     * Verifica se o usuário pode DELETAR (apenas gerentes)
     */
    public function podeDeletar()
    {
        return $this->isgerente();
    }

    /**
     * Valida permissão ou envia erro
     */
    public static function validargerente($operacao = "acessar")
    {
        $auth = new self();
        
        if (!$auth->isgerente()) {
            http_response_code(403);
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Sem autorização para ' . $operacao . '. Apenas gerentes podem realizar esta ação.'
            ]);
            exit;
        }
    }

    /**
     * Retorna o tipo de usuário
     */
    public function getTipoUsuario()
    {
        return $this->tipoUsuario;
    }

    /**
     * Retorna o ID do usuário
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
}
?>
