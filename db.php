<?php
$host = 'www.phsoftware.com.br';
$db = 'phsoft84_EJC';
$user = 'phsoft84_pedrolive';
$pass = 'Z-C*Z5fFr6[6';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>