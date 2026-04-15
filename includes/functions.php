<?php

// ============================================================
// functions.php – De "motor" van de applicatie
// Taak student: Implementeer elke functie op de aangegeven plek.
// Gebruik altijd Prepared Statements (PDO) om SQL-injecties te voorkomen!
// ============================================================

/**
 * Controleer of de huidige gebruiker een actieve sessie heeft.
 *
 * @return bool True als ingelogd, anders false.
 */
function checkLogin(): bool
{
    
}

/**
 * Registreer een nieuwe gebruiker in de database.
 *
 * Taak student:
 * 1. Hash het wachtwoord met password_hash() (gebruik PASSWORD_DEFAULT).
 * 2. Controleer of de gebruikersnaam of het e-mail al bestaat (UNIQUE key).
 * 3. Voeg de gebruiker in via een prepared statement.
 *
 * @param PDO    $pdo      De PDO-databaseverbinding.
 * @param string $username De gewenste gebruikersnaam.
 * @param string $email    Het e-mailadres van de gebruiker.
 * @param string $password Het wachtwoord in plaintext (nog hashen!).
 * @return bool True bij succes, false als gebruikersnaam/e-mail al bestaat.
 */
function registerUser(PDO $pdo, string $username, string $email, string $password): bool
{
    // Schrijf hier je code
}

/**
 * Verifieer de inloggegevens en start een sessie bij succes.
 *
 * Taak student:
 * 1. Zoek de gebruiker op via de gebruikersnaam.
 * 2. Gebruik password_verify() om het wachtwoord te controleren.
 * 3. Sla user_id en username op in de sessie.
 * Hint: gebruik session_regenerate_id(true) na een succesvolle login
 *       om session fixation-aanvallen te voorkomen.
 *
 * @param PDO    $pdo      De PDO-databaseverbinding.
 * @param string $username De gebruikersnaam.
 * @param string $password Het wachtwoord in plaintext.
 * @return bool True bij succesvolle login, anders false.
 */
function loginUser(PDO $pdo, string $username, string $password): bool
{
    // Schrijf hier je code
}

/**
 * Maak een nieuwe poule aan en voeg de maker toe als deelnemer.
 *
 * Taak student:
 * 1. Genereer een unieke invite code: bin2hex(random_bytes(4))
 * 2. Sla de poule op (naam, invite_code, admin_id).
 * 3. Voeg de aanmaker direct toe aan poule_participants.
 *
 * @param PDO    $pdo       De PDO-databaseverbinding.
 * @param int    $userId    De ID van de ingelogde gebruiker (wordt admin).
 * @param string $pouleName De naam van de nieuwe poule.
 * @return int|false De ID van de nieuwe poule bij succes, false bij een fout.
 */
function createPoule(PDO $pdo, int $userId, string $pouleName)
{
    // Schrijf hier je code
}

/**
 * Voeg een gebruiker toe aan een bestaande poule via de invite code.
 *
 * Taak student:
 * 1. Zoek de poule op via de invite_code.
 * 2. Controleer of de gebruiker al deelneemt (voorkom dubbele invoer).
 * 3. Voeg de gebruiker toe aan poule_participants.
 *
 * @param PDO    $pdo        De PDO-databaseverbinding.
 * @param int    $userId     De ID van de ingelogde gebruiker.
 * @param string $inviteCode De uitnodigingscode van de poule.
 * @return bool True als deelname geslaagd is, false als de code ongeldig is
 *              of als de gebruiker al deelneemt.
 */
function joinPoule(PDO $pdo, int $userId, string $inviteCode): bool
{
    // Schrijf hier je code
}

/**
 * Bereken het totaal aantal punten voor een gebruiker binnen een poule.
 *
 * Puntensysteem:
 * - 1 punt voor de juiste winnaar/gelijkspel (uitslag-trend correct).
 * - 3 punten voor de exact correcte uitslag.
 *
 * Taak student:
 * 1. Haal alle afgeronde wedstrijden op (is_finished = 1).
 * 2. Vergelijk de voorspelling van de gebruiker met de werkelijke uitslag.
 * 3. Tel de punten bij elkaar op en geef het totaal terug.
 *
 * @param PDO $pdo     De PDO-databaseverbinding.
 * @param int $userId  De ID van de gebruiker.
 * @param int $pouleId De ID van de poule.
 * @return int Het totaal aantal behaalde punten.
 */
function calculatePoints(PDO $pdo, int $userId, int $pouleId): int
{
    // Schrijf hier je code
    return 0;
}

/**
 * Haal alle nog niet afgesloten wedstrijden op, gesorteerd op datum.
 *
 * Taak student:
 * 1. Query de tabel 'matches' op is_finished = 0.
 * 2. Sorteer op match_date ASC.
 * 3. Geef een array van wedstrijdrijen terug.
 *    Elke rij bevat: id, group_letter, team_home, team_away, city, match_date.
 *
 * Hint: om de resultaten per groep te tonen in voorspellingen.php
 *       kun je een hulpfunctie of een foreach gebruiken om het resultaat
 *       te groeperen op group_letter:
 *       foreach ($matches as $match) {
 *           $grouped[$match['group_letter']][] = $match;
 *       }
 *
 * @param PDO $pdo De PDO-databaseverbinding.
 * @return array Alle openstaande wedstrijden als associatieve array.
 */
function getUpcomingMatches(PDO $pdo): array
{
    // Schrijf hier je code
    return [];
}

/**
 * Haal alle voorspellingen van een gebruiker op, geïndexeerd op match_id.
 *
 * Taak student:
 * 1. Query de tabel 'predictions' op user_id = $userId.
 * 2. Geef het resultaat terug als een associatieve array
 *    waarbij de sleutel de match_id is:
 *    [ match_id => ['predicted_home' => x, 'predicted_away' => y], ... ]
 *
 * Hint: gebruik PDO::FETCH_ASSOC en bouw de array op in een foreach,
 *       of gebruik fetchAll(PDO::FETCH_UNIQUE) als extra uitdaging.
 *
 * @param PDO $pdo    De PDO-databaseverbinding.
 * @param int $userId De ID van de ingelogde gebruiker.
 * @return array Voorspellingen geïndexeerd op match_id.
 */
function getUserPredictions(PDO $pdo, int $userId): array
{
    // Schrijf hier je code
    return [];
}

/**
 * Sla een voorspelling op of werk een bestaande bij (upsert).
 *
 * Taak student:
 * 1. Controleer of er al een rij bestaat voor (user_id, match_id).
 * 2a. Als die bestaat: voer een UPDATE uit.
 * 2b. Als die niet bestaat: voer een INSERT uit.
 * Tip: MySQL ondersteunt INSERT ... ON DUPLICATE KEY UPDATE,
 *      dit is efficiënter dan eerst een SELECT doen.
 *      Voorbeeld:
 *      INSERT INTO predictions (user_id, match_id, predicted_home, predicted_away)
 *      VALUES (?, ?, ?, ?)
 *      ON DUPLICATE KEY UPDATE predicted_home = VALUES(predicted_home),
 *                               predicted_away = VALUES(predicted_away)
 *
 * @param PDO $pdo           De PDO-databaseverbinding.
 * @param int $userId        De ID van de ingelogde gebruiker.
 * @param int $matchId       De ID van de wedstrijd.
 * @param int $predictedHome Voorspeld aantal doelpunten thuisteam (0-20).
 * @param int $predictedAway Voorspeld aantal doelpunten uitteam (0-20).
 * @return bool True bij succes, false bij een databasefout.
 */
function savePrediction(PDO $pdo, int $userId, int $matchId, int $predictedHome, int $predictedAway): bool
{
    // Schrijf hier je code
    return false;
}
