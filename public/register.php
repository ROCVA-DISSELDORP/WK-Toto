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

$error   = '';
$success = '';

// ============================================================
// Taak student – Fase 2: Registratie verwerken
// Voer onderstaande stappen uit als het formulier verstuurd is
// ($_SERVER['REQUEST_METHOD'] === 'POST'):
//
// 1. Haal de velden op uit $_POST en trim spaties:
//    username, email, password, confirm_password.
// 2. Valideer de invoer:
//    - Zijn alle velden ingevuld?
//    - Is het e-mailadres geldig? (gebruik filter_var + FILTER_VALIDATE_EMAIL)
//    - Is het wachtwoord minimaal 8 tekens lang?
//    - Komen 'password' en 'confirm_password' overeen?
// 3. Roep registerUser($pdo, $username, $email, $password) aan
//    (implementeer deze functie eerst in includes/functions.php).
// 4. Stel $success in bij succes, of $error bij een dubbele gebruikersnaam/email.
// ============================================================

$pageTitle = 'Registreren';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <div class="form-wrapper">
        <h2>Account aanmaken</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
                <a href="<?= BASE_URL ?>/public/login.php">Login hier</a>.
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Gebruikersnaam</label>
                <input type="text" id="username" name="username" required autocomplete="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" required autocomplete="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Wachtwoord <small>(minimaal 8 tekens)</small></label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Bevestig wachtwoord</label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Registreren</button>
        </form>

        <p class="form-footer">Al een account? <a href="<?= BASE_URL ?>/public/login.php">Inloggen</a></p>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
