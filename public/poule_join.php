<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$error   = '';
$success = '';

// ============================================================
// Taak student – Fase 3: Deelnemen aan poule
// Voer onderstaande stappen uit als het formulier verstuurd is
// ($_SERVER['REQUEST_METHOD'] === 'POST'):
//
// 1. Haal 'invite_code' op uit $_POST en trim spaties.
// 2. Controleer of het veld niet leeg is; stel anders $error in.
// 3. Roep joinPoule($pdo, $_SESSION['user_id'], $inviteCode) aan
//    (implementeer deze functie eerst in includes/functions.php).
// 4. Bij succes: stel $success in met een bevestigingsbericht.
// 5. Bij mislukking (ongeldige code of al deelnemer): stel $error in.
// ============================================================

$pageTitle = 'Deelnemen aan Poule';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <div class="form-wrapper">
        <h2>Deelnemen aan een Poule</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
                <a href="<?= BASE_URL ?>/public/dashboard.php">Naar dashboard</a>.
            </div>
        <?php endif; ?>

        <form method="POST" action="poule_join.php">
            <div class="form-group">
                <label for="invite_code">Uitnodigingscode</label>
                <input type="text" id="invite_code" name="invite_code" required
                       placeholder="bijv. a1b2c3d4"
                       value="<?= htmlspecialchars($_POST['invite_code'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Deelnemen</button>
        </form>

        <p class="form-footer"><a href="<?= BASE_URL ?>/public/dashboard.php">&larr; Terug naar dashboard</a></p>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
