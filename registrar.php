<?php
include 'db.php';

$codigo = $_POST['codigo'];
$data = date('Y-m-d');
$hora = date('H:i:s');
$ip = $_SERVER['REMOTE_ADDR'];

$mensagem = '';
$tipo = 'danger'; // padrão de erro

// Verifica se o cookie já marcou presença hoje
if (isset($_COOKIE['presenca_registrada']) && $_COOKIE['presenca_registrada'] === $data) {
    $mensagem = "Este dispositivo já registrou presença hoje!";
} else {
    // Verifica se o código existe
    $stmt = $pdo->prepare("SELECT id, nome FROM participantes WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Verifica se o usuário já registrou hoje (agora sem verificar o IP)
        $stmt = $pdo->prepare("SELECT * FROM presencas WHERE usuario_id = ? AND data = ?");
        $stmt->execute([$usuario['id'], $data]);

        if ($stmt->rowCount() == 0) {
            // Registrar presença
            $stmt = $pdo->prepare("INSERT INTO presencas (usuario_id, data, hora, ip) VALUES (?, ?, ?, ?)");
            $stmt->execute([$usuario['id'], $data, $hora, $ip]);

            // Salvar cookie até meia-noite
            $expira = strtotime('tomorrow');
            setcookie('presenca_registrada', $data, $expira);

            $mensagem = "Presença registrada com sucesso para <strong>" . htmlspecialchars($usuario['nome']) . "</strong>.";
            $tipo = 'success';
        } else {
            $mensagem = "Você já registrou presença hoje!";
        }
    } else {
        $mensagem = "Código inválido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container">
        <div class="alert alert-<?= $tipo ?> text-center shadow p-4">
            <?= $mensagem ?>
            <div class="mt-3">
                <a href="index.php" class="btn btn-outline-primary">Voltar</a>
            </div>
        </div>
    </div>
</body>
</html>
