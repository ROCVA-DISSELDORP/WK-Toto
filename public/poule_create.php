<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$error = '';

// ============================================================
// Taak student – Fase 3: Poule aanmaken
// Voer onderstaande stappen uit als het formulier verstuurd is
// ($_SERVER['REQUEST_METHOD'] === 'POST'):
//
// 1. Haal 'poule_name' op uit $_POST en trim spaties.
// 2. Valideer: is het veld ingevuld en maximaal 100 tekens?
//    Stel $error in als dat niet het geval is.
// 3. Roep createPoule($pdo, $_SESSION['user_id'], $pouleName) aan
//    (implementeer deze functie eerst in includes/functions.php).
// 4. Bij succes: stuur de gebruiker door naar de detailpagina:
//    header('Location: poule.php?id=' . $pouleId);
// 5. Bij mislukking: stel $error in.
// ============================================================

$pageTitle = 'Poule Aanmaken';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <div class="form-wrapper">
        <h2>Nieuwe Poule Aanmaken</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="poule_create.php">
            <div class="form-group">
                <label for="poule_name">Naam van de poule</label>
                <input type="text" id="poule_name" name="poule_name" required maxlength="100"
                       placeholder="bijv. Kantoorpoule 2026"
                       value="<?= htmlspecialchars($_POST['poule_name'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Poule aanmaken</button>
        </form>

        <p class="form-footer"><a href="<?= BASE_URL ?>/public/dashboard.php">&larr; Terug naar dashboard</a></p>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
