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
        
        $this->idUsuario = getIdUsuario();
        
        if ($this->idUsuario) {
            $sql = $this->pdo->prepare("SELECT Tipo_Usuario FROM usuario WHERE ID_Usuario = :id");
            $sql->bindValue(":id", $this->idUsuario, PDO::PARAM_INT);
            $sql->execute();
            $usuario = $sql->fetch(PDO::FETCH_ASSOC);
            
            $this->tipoUsuario = $usuario ? $usuario['Tipo_Usuario'] : null;
        }
    }

    /**
     * Verifica se o usuário é gerente
     */
    public function isgerente()
    {
        return $this->tipoUsuario === 'gerente';
    }

    /**
     * Verifica se o usuário é Lider
     */
    public function isLider()
    {
        return $this->tipoUsuario === 'lider';
    }

    public function isColaborador()
    {
        return $this->tipoUsuario === 'colaborador';
    }

    public function podecriar()
    {
        return $this->isgerente();
    }

  
    public function podeAtualizar()
    {
        return $this->isgerente();
    }

   
    public function podeDeletar()
    {
        return $this->isgerente();
    }

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

  
    public function getTipoUsuario()
    {
        return $this->tipoUsuario;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
}
?>
