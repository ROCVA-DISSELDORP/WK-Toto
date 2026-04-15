<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

// ============================================================
// Taak student – Fase 3: Poules ophalen uit de database
// Haal alle poules op waarbij de ingelogde gebruiker deelnemer is.
// Gebruik $_SESSION['user_id'] en combineer de tabellen 'poules'
// en 'poule_participants' via een JOIN-query met een prepared statement.
//
// Vervang de dummy-data hieronder door je echte PDO-query.
// De array-structuur die de HTML verwacht:
// [
//     ['id' => 1, 'name' => 'Vrienden Poule', 'invite_code' => 'abc12345'],
//     ...
// ]
// ============================================================
$myPoules = [
    ['id' => 1, 'name' => 'Vrienden Poule',   'invite_code' => 'a1b2c3d4'],
    ['id' => 2, 'name' => 'Kantoorpoule 2026', 'invite_code' => 'e5f6a7b8'],
    ['id' => 3, 'name' => 'Familie toto',      'invite_code' => 'c9d0e1f2'],
]; // ← Vervang dit door de echte PDO-query

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <div class="dashboard-header">
        <h2>Welkom, <?= htmlspecialchars($_SESSION['username'] ?? 'Speler') ?>!</h2>
    </div>

    <section class="dashboard-actions">
        <a href="<?= BASE_URL ?>/public/poule_create.php" class="btn btn-primary">+ Poule aanmaken</a>
        <a href="<?= BASE_URL ?>/public/poule_join.php"   class="btn btn-secondary">Deelnemen via code</a>
    </section>

    <section class="poule-list">
        <h3>Mijn Poules</h3>
        <?php if (empty($myPoules)): ?>
            <p class="empty-state">
                Je neemt nog niet deel aan een poule.<br>
                Maak er een aan of join via een uitnodigingscode!
            </p>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($myPoules as $poule): ?>
                    <div class="card">
                        <h4><?= htmlspecialchars($poule['name']) ?></h4>
                        <p class="card-code">
                            Code: <strong><?= htmlspecialchars($poule['invite_code']) ?></strong>
                        </p>
                        <a href="<?= BASE_URL ?>/public/poule.php?id=<?= (int)$poule['id'] ?>"
                           class="btn btn-secondary btn-sm">Bekijken</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
