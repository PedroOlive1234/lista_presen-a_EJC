<?php
include 'db.php';

$codigo = $_POST['codigo'] ?? '';
$data = date('Y-m-d');
$hora = date('H:i:s');
$ip = $_SERVER['REMOTE_ADDR'];

if ($codigo) {
    // Buscar participante pelo código
    $stmt = $pdo->prepare("SELECT id, nome FROM participantes WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Verificar se já registrou presença hoje
        $stmt = $pdo->prepare("SELECT * FROM presencas WHERE usuario_id = ? AND data = ?");
        $stmt->execute([$usuario['id'], $data]);

        if ($stmt->rowCount() == 0) {
            // Registrar presença
            $stmt = $pdo->prepare("INSERT INTO presencas (usuario_id, data, hora, ip) VALUES (?, ?, ?, ?)");
            $stmt->execute([$usuario['id'], $data, $hora, $ip]);
            // Adicionar mensagem de sucesso
            $mensagem = "Presença registrada para <strong>" . htmlspecialchars($usuario['nome']) . "</strong>.";
            $tipo = 'success';
        } else {
            // Adicionar mensagem de erro se a presença já foi registrada
            $mensagem = "A presença de <strong>" . htmlspecialchars($usuario['nome']) . "</strong> já foi registrada hoje.";
            $tipo = 'warning';
        }
    } else {
        // Caso o código não seja encontrado
        $mensagem = "Código inválido.";
        $tipo = 'danger';
    }
} else {
    $mensagem = "Por favor, insira um código válido.";
    $tipo = 'danger';
}

// Redireciona para o admin.php com a mensagem
header("Location: admin.php?data=$data&mensagem=" . urlencode($mensagem) . "&tipo=$tipo");
exit;
