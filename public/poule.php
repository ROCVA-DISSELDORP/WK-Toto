<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Valideer en filter de poule-ID uit de URL (?id=...)
$pouleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$pouleId || $pouleId <= 0) {
    header('Location: dashboard.php');
    exit;
}

// ============================================================
// Taak student – Fase 3: Pouledata ophalen
//
// Stap 1 – Poule ophalen
// Zoek de poule op via $pouleId. Controleer ook of de ingelogde
// gebruiker ($_SESSION['user_id']) deelneemt aan deze poule.
// Zo niet – stuur door naar dashboard.php (toegangscontrole!).
//
// Stap 2 – Ranglijst ophalen
// Haal alle deelnemers op, gesorteerd op punten (DESC).
// Gebruik calculatePoints() uit functions.php of bereken de
// punten direct in een subquery.
//
// Vervang de dummy-data hieronder door je echte PDO-queries.
// ============================================================

// Dummy-data – verwijder dit zodra je de echte queries hebt geschreven:
$poule = [
    'id'          => (int)$pouleId,
    'name'        => 'Vrienden Poule',
    'invite_code' => 'a1b2c3d4',
];

$rankings = [
    ['user_id' => 1, 'username' => 'Alice', 'points' => 15],
    ['user_id' => 2, 'username' => 'Bob',   'points' => 12],
    ['user_id' => 3, 'username' => 'Carol', 'points' => 9],
]; // ← Vervang dit door de echte PDO-query

$pageTitle = $poule ? htmlspecialchars($poule['name']) : 'Poule';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <?php if (!$poule): ?>
        <div class="alert alert-error">Poule niet gevonden of je hebt geen toegang.</div>
        <a href="<?= BASE_URL ?>/public/dashboard.php" class="btn btn-secondary">Terug naar dashboard</a>
    <?php else: ?>
        <div class="poule-header">
            <h2><?= htmlspecialchars($poule['name']) ?></h2>
            <p class="invite-code">
                Uitnodigingscode: <strong><?= htmlspecialchars($poule['invite_code']) ?></strong>
                <small>(deel deze code met vrienden)</small>
            </p>
        </div>

        <section class="rankings">
            <h3>Ranglijst</h3>
            <?php if (empty($rankings)): ?>
                <p class="empty-state">Er zijn nog geen puntenuitslagen beschikbaar.</p>
            <?php else: ?>
                <table class="rankings-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Speler</th>
                            <th>Punten</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rankings as $rank => $player): ?>
                            <tr class="<?= ((int)$_SESSION['user_id'] === (int)$player['user_id']) ? 'row-highlight' : '' ?>">
                                <td><?= $rank + 1 ?></td>
                                <td><?= htmlspecialchars($player['username']) ?></td>
                                <td><?= (int)$player['points'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
