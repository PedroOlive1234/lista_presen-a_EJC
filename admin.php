<?php
include 'db.php'; // conexão com o banco

$data = $_GET['data'] ?? date('Y-m-d');

// Buscar presenças do dia
$stmt = $pdo->prepare("
    SELECT p.id as presenca_id, u.nome, p.hora
    FROM presencas p
    JOIN participantes u ON p.usuario_id = u.id
    WHERE p.data = ?
    ORDER BY p.hora ASC
");
$stmt->execute([$data]);
$presencas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar ranking geral
$stmt = $pdo->query("
    SELECT u.nome, COUNT(p.id) AS total_presencas
    FROM participantes u
    LEFT JOIN presencas p ON u.id = p.usuario_id
    GROUP BY u.id
    ORDER BY total_presencas DESC, u.nome ASC
");
$ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Lista de Presenças</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand">Painel de Presenças</span>
    </div>
</nav>

<div class="container mt-4">

    <?php if (isset($_GET['mensagem'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_GET['tipo']) ?> text-center">
            <?= htmlspecialchars($_GET['mensagem']) ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Lista de Presenças - <?= htmlspecialchars($data) ?></h3>
        <form class="d-flex" method="GET">
            <input type="date" name="data" class="form-control me-2" value="<?= $data ?>">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
    </div>

    <div class="mb-3 d-flex gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdicionar">Adicionar Participante</button>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluirTodos">Excluir Todas as Presenças da Data</button>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCadastrarPessoa">
    Cadastrar Nova Pessoa
</button>
    </div>

    <?php if (!empty($presencas)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>Nome</th>
                        <th>Hora</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presencas as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td><?= htmlspecialchars($p['hora']) ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalExcluirPresenca<?= $p['presenca_id'] ?>">Excluir</button>

                                <div class="modal fade" id="modalExcluirPresenca<?= $p['presenca_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" action="excluir_presenca.php" class="modal-content">
                                            <input type="hidden" name="presenca_id" value="<?= $p['presenca_id'] ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Excluir Presença</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Deseja excluir a presença de <strong><?= htmlspecialchars($p['nome']) ?></strong> neste dia?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Nenhuma presença registrada para esta data.</div>
    <?php endif; ?>

    <!-- Ranking de Participação -->
    <div class="mt-5">
        <h4 class="text-primary">Ranking de Participação</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Total de Presenças</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ranking as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($r['nome']) ?></td>
                            <td><?= $r['total_presencas'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Adicionar Participante -->
<div class="modal fade" id="modalAdicionar" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="adicionar_participante.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Presença via Código</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Registrar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Excluir Todas -->
<div class="modal fade" id="modalExcluirTodos" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="excluir_todas_presencas.php" class="modal-content">
            <input type="hidden" name="data" value="<?= $data ?>">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Todas as Presenças</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir <strong>todas as presenças do dia <?= $data ?></strong>?
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Excluir Todas</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cadastrar Pessoa -->
<div class="modal fade" id="modalCadastrarPessoa" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="cadastrar_participante.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Nova Pessoa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
