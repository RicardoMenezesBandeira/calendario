<?php

 

define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_gestao');
define('DB_USER', 'root');
define('DB_PASS', '');


function getPDO() {
    try {
        $pdo = new PDO(
            "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST,
            DB_USER,
            DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        return $pdo;
    } catch (PDOException $e) {
        die(json_encode(["erro" => "Erro de conexão: " . $e->getMessage()]));
    }
}
?>