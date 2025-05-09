<?php
include 'db.php';

$data = $_POST['data'] ?? null;

if ($data) {
    $stmt = $pdo->prepare("DELETE FROM presencas WHERE data = ?");
    $stmt->execute([$data]);
}

header("Location: admin.php?data=" . urlencode($data));
exit;
