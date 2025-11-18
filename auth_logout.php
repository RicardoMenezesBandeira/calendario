<?php
require_once 'config/session.php';

// Usar função centralizada de finalização
finalizarSessao();

// Redirecionar para login
header('Location: auth_login.php');
exit;
?>
