<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$data = loadData();
$sprint = activeSprint($data['sprints'] ?? []);
$errors = [];
$values = ['title' => '', 'description' => ''];

if (!$sprint) {
    $errors[] = 'No hi ha cap esprint actiu per a crear tasques.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['title'] = trim((string) ($_POST['title'] ?? ''));
    $values['description'] = trim((string) ($_POST['description'] ?? ''));

    if (!validCsrf()) {
        $errors[] = 'La sessió no és vàlida.';
    }

    $titleLength = mb_strlen($values['title']);

    if ($values['title'] === '') {
        $errors[] = 'El títol és obligatori.';
    } elseif ($titleLength < 3) {
        $errors[] = 'El títol és massa curt (mínim 3 caràcters).';
    } elseif ($titleLength > 50) {
        $errors[] = 'El títol és massa llarg (màxim 50 caràcters).';
    }

    if ($values['description'] === '') {
        $errors[] = 'La descripció és obligatòria.';
    }

    if (!$sprint) {
        $errors[] =
            'No es pot crear la tasca perquè no hi ha cap esprint actiu.';
    }

    if (!$errors) {
        $data['tasks'][] = [
            'id' => $data['next_ids']['tasks']++,
            'title' => $values['title'],
            'description' => $values['description'],
            'status' => 'todo',
            'user_id' => $_SESSION['user_id'],
            'sprint_id' => $sprint['id'],
            'team_id' => null,
        ];

        if (saveData($data)) {
            redirect('board.php');
        }

        $errors[] = 'No s’ha pogut guardar la tasca. Intenta-ho de nou.';
    }
}

$pageTitle = 'Nova tasca';
require __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <h1 class="mb-4">Nova tasca</h1>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endforeach; ?>

        <form method="post" class="card card-body shadow-sm">
            <input type="hidden" name="csrf" value="<?= h(csrfToken()) ?>">

            <div class="mb-3">
                <label for="title" class="form-label">Títol</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control" 
                    value="<?= h($values['title']) ?>" 
                    required 
                    minlength="3" 
                    maxlength="100"
                >
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripció</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control" 
                    rows="4" 
                    required
                ><?= h($values['description']) ?></textarea>
            </div>

            <button class="btn btn-primary w-100" <?= !$sprint
                ? 'disabled'
                : '' ?>>Crear tasca</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
