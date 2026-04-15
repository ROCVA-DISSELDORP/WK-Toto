<?php

// ============================================================
// Taak student:
// Pas de gegevens hieronder aan naar jouw eigen database-instellingen.
// Zet 'display_errors' op 0 en log fouten in productie.
// ============================================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_URL', '/wk-toto'); // Pas aan als je project in een andere submap staat

$host    = 'localhost';
$dbname  = 'wk_toto';
$dbuser  = 'root';
$dbpass  = '';        // XAMPP standaard: leeg wachtwoord
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Gooi exceptions bij fouten
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Haal rijen op als associatieve arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                     // Gebruik echte prepared statements
];

try {
    $pdo = new PDO($dsn, $dbuser, $dbpass, $options);
} catch (PDOException $e) {
    // Toon geen technische details aan de eindgebruiker
    error_log('Databaseverbinding mislukt: ' . $e->getMessage());
    die('Er is een probleem met de databaseverbinding. Controleer je instellingen in config/db.php.');
}
