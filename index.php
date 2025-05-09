<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Presença</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4 text-primary">Registrar Presença</h3>
        <form action="registrar.php" method="post">
            <div class="mb-3">
                <label for="codigo" class="form-label">Digite seu código</label>
                <input type="text" name="codigo" id="codigo" class="form-control" required placeholder="Ex: 1234">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Registrar Presença</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
