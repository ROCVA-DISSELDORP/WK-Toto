<?php
// functions.php is al geladen door de pagina die deze partial insluit,
// maar require_once zorgt ervoor dat dit nooit dubbel geladen wordt.
require_once __DIR__ . '/../includes/functions.php';
?>
<nav class="navbar">
    <div class="navbar-brand">
        <a href="<?= BASE_URL ?>/public/index.php">&#9917; WK-Toto</a>
    </div>

    <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-hidden="true">
    <label for="nav-toggle" class="nav-toggle-label" aria-label="Menu openen">
        <span></span><span></span><span></span>
    </label>

    <ul class="navbar-menu">
        <?php if (checkLogin()): ?>
            <!-- Taak student: deze links worden getoond als de gebruiker INGELOGD is -->
            <li><a href="<?= BASE_URL ?>/public/dashboard.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/public/predictions.php">Voorspellingen</a></li>
            <li><a href="<?= BASE_URL ?>/public/poule_create.php">Poule Aanmaken</a></li>
            <li><a href="<?= BASE_URL ?>/public/poule_join.php">Deelnemen</a></li>
            <li><a href="<?= BASE_URL ?>/public/logout.php" class="nav-logout">Uitloggen</a></li>
        <?php else: ?>
            <!-- Taak student: deze links worden getoond als de gebruiker NIET ingelogd is -->
            <li><a href="<?= BASE_URL ?>/public/index.php">Home</a></li>
            <li><a href="<?= BASE_URL ?>/public/login.php">Login</a></li>
            <li><a href="<?= BASE_URL ?>/public/register.php">Registreren</a></li>
        <?php endif; ?>
    </ul>
</nav>
