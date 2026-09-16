<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$data = loadData();
$errors = [];
$values = [
    'name' => '',
    'goal' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name'] = trim((string) ($_POST['name'] ?? ''));
    $values['goal'] = trim((string) ($_POST['goal'] ?? ''));
    $values['start_date'] = trim((string) ($_POST['start_date'] ?? ''));
    $values['end_date'] = trim((string) ($_POST['end_date'] ?? ''));
    $values['status'] = trim((string) ($_POST['status'] ?? ''));


    if (!validCsrf()) {
        $errors[] = 'La sessió no és vàlida.';
    }
    if ($values['name'] === '') {
        $errors[] = 'El nom és obligatori.';
    }
    if ($values['goal'] === '') {
        $errors[] = 'L’objectiu és obligatori.';
    }
    if ($values['start_date'] === '') {
        $errors[] = 'La data d’inici és obligatòria.';
    }
    if ($values['end_date'] === '') {
        $errors[] = 'La data de finalització és obligatòria.';
    }
    if ($values['status'] === '') {
        $errors[] = 'L’estat és obligatori.';
    }
    

    if (!$errors) {
        $data['sprints'][] = [
            'id' => $data['next_ids']['sprints']++,
            'name' => $values['name'],
            'goal' => $values['goal'],
            'start_date' => $values['start_date'],
            'end_date' => $values['end_date'],
            'status' => $values['status']
        ];

        if (saveData($data)) {
            redirect('sprints.php');
        }

        $errors[] = 'No s’ha pogut guardar l’esprint. Intenta-ho de nou.';
    }
}

$pageTitle = 'Nou sprint';
require __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <h1 class="mb-4">Nou sprint</h1>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endforeach; ?>

        <form method="post" class="card card-body shadow-sm">
            <input type="hidden" name="csrf" value="<?= h(csrfToken()) ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Títol</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= h($values['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="goal" class="form-label">Objectiu</label>
                <textarea id="goal" name="goal" class="form-control" rows="4" required><?= h($values['goal']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Data d'inici</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= h($values['start_date']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">Data de finalització</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= h($values['end_date']) ?>">
            </div>

            <div>
            <div class="mb-3">
                <label for="status" class="form-label">Estat</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="">Selecciona un estat</option>
                    <option value="active" <?= $values['status'] === 'active' ? 'selected' : '' ?>>Actiu</option>
                    <option value="inactive" <?= $values['status'] === 'inactive' ? 'selected' : '' ?>>Inactiu</option>
                </select>
        </div>
            <button type="submit" class="btn btn-primary w-100">Crear esprint</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>