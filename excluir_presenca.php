<?php
include 'db.php';

$presenca_id = $_POST['presenca_id'] ?? null;

if ($presenca_id) {
    $stmt = $pdo->prepare("DELETE FROM presencas WHERE id = ?");
    $stmt->execute([$presenca_id]);
}

header("Location: admin.php");
exit;
