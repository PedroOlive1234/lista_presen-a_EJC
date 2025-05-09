<?php
include 'db.php';

// Função para gerar código numérico de 4 dígitos
function gerarCodigo($length = 4) {
    $codigo = '';
    for ($i = 0; $i < $length; $i++) {
        $codigo .= rand(0, 9); // Código numérico
    }
    return $codigo;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);

    if ($nome) {
        do {
            $codigo = gerarCodigo(4); // Gerar código de 4 dígitos
            // Verificar se o código já existe no banco
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM participantes WHERE codigo = ?");
            $stmt->execute([$codigo]);
            $existe = $stmt->fetchColumn();
        } while ($existe > 0); // Repetir até encontrar um código único

        // Inserir o novo participante com o código gerado
        $stmt = $pdo->prepare("INSERT INTO participantes (nome, codigo) VALUES (?, ?)");
        $stmt->execute([$nome, $codigo]);

        // Exibir mensagem de sucesso e incluir o código gerado
        header("Location: admin.php?mensagem=Pessoa cadastrada com sucesso! O código gerado é: $codigo&tipo=success");
        exit;
    } else {
        // Caso o nome esteja vazio ou inválido
        header("Location: admin.php?mensagem=Nome inválido.&tipo=danger");
        exit;
    }
}
?>
