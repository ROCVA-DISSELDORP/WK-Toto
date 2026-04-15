<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Home';
// koppel de header en navbar
?>

<main class="container">
    <section class="hero">
        <h1>Welkom bij WK-Toto &#9917;</h1>
        <p class="hero-subtitle">
            Voorspel de uitslagen van het WK voetbal en meet je kracht met vrienden in een eigen poule!
        </p>
        <?php if (!checkLogin()): ?>
            <div class="hero-buttons">
                <a href="<?= BASE_URL ?>/public/register.php" class="btn btn-primary">Account aanmaken</a>
                <a href="<?= BASE_URL ?>/public/login.php" class="btn btn-secondary">Inloggen</a>
            </div>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/public/dashboard.php" class="btn btn-primary">Naar mijn dashboard</a>
        <?php endif; ?>
    </section>

    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">&#127942;</div>
            <h3>Maak een Poule</h3>
            <p>Maak een privépoule aan en nodig vrienden uit met een unieke uitnodigingscode.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#9917;</div>
            <h3>Voorspel Uitslagen</h3>
            <p>Geef voor iedere WK-wedstrijd jouw voorspelling in vóór aftrap.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128202;</div>
            <h3>Ranglijst</h3>
            <p>Zie wie de beste voorspeller is in jouw poule na elke speelronde.</p>
        </div>
    </section>
</main>

<?php

// koppel de footer

?>

