<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Taak student – Fase 1: Doorsturen als al ingelogd
// Controleer met checkLogin() of de gebruiker al een actieve
// sessie heeft. Stuur hem/haar dan door naar dashboard.php.
// ============================================================

$error = '';

// ============================================================
// Taak student – Fase 2: Login verwerken
// Voer onderstaande stappen uit als het formulier verstuurd is
// ($_SERVER['REQUEST_METHOD'] === 'POST'):
//
// 1. Haal 'username' en 'password' op uit $_POST.
// 2. Controleer of beide velden ingevuld zijn; stel anders $error in.
// 3. Roep loginUser($pdo, $username, $password) aan
//    (implementeer deze functie eerst in includes/functions.php).
// 4. Bij succes: stuur de gebruiker door naar dashboard.php.
// 5. Bij mislukking: stel $error in.
//    Let op: vermijd meldingen zoals "gebruiker bestaat niet" –
//    gebruik een neutrale tekst om gebruikersnamen niet prijs te geven.
// ============================================================

$pageTitle = 'Inloggen';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <div class="form-wrapper">
        <h2>Inloggen</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Gebruikersnaam</label>
                <input type="text" id="username" name="username" required autocomplete="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Inloggen</button>
        </form>

        <p class="form-footer">Nog geen account? <a href="<?= BASE_URL ?>/public/register.php">Registreren</a></p>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
