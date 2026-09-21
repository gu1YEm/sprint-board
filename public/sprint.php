<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$idParam = $_GET['id'] ?? null;

if ($idParam === null || !ctype_digit((string) $idParam)) {
    http_response_code(400);
    $pageTitle = 'Error 400';
    require __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-danger mt-4">Sol·licitud incorrecta: L\'identificador no és vàlid.</div>';
    echo '<a href="sprints.php" class="btn btn-secondary">Tornar a esprints</a>';
    require __DIR__ . '/../includes/footer.php';
    exit();
}

$id = (int) $idParam;

$data = loadData();
$sprintTrobat = null;

foreach ($data['sprints'] as $s) {
    if ((int) $s['id'] === $id) {
        $sprintTrobat = $s;
        break;
    }
}

if ($sprintTrobat === null) {
    http_response_code(404);
    $pageTitle = 'Error 404';
    require __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-warning mt-4">No s\'ha trobat l\'esprint sol·licitat.</div>';
    echo '<a href="sprints.php" class="btn btn-secondary">Tornar a esprints</a>';
    require __DIR__ . '/../includes/footer.php';
    exit();
}

$pageTitle = $sprintTrobat['name'];
require __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?= h($sprintTrobat['name']) ?></h1>
            <a href="sprints.php" class="btn btn-outline-secondary">Tornar a la llista</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-muted mb-3">Detalls de l'esprint</h5>
                
                <p><strong>Objectiu:</strong></p>
                <p class="border p-3 rounded bg-light"><?= nl2br(
                    h($sprintTrobat['goal']),
                ) ?></p>

                <div class="row mt-4">
                    <div class="col-sm-6">
                        <p><strong>Data d'inici:</strong> <?= h(
                            $sprintTrobat['start_date'],
                        ) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Data de finalització:</strong> <?= h(
                            $sprintTrobat['end_date'],
                        ) ?></p>
                    </div>
                </div>

                <div class="mt-2">
                    <strong>Estat:</strong> 
                    <?php if (($sprintTrobat['status'] ?? '') === 'active'): ?>
                        <span class="badge bg-success">Actiu</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactiu</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
