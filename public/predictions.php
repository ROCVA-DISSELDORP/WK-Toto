<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$userId     = (int)$_SESSION['user_id'];
$savedCount = 0;
$error      = '';

// ============================================================
// Taak student – Fase 3: Voorspellingen opslaan
// Voer onderstaande stappen uit als het formulier verstuurd is
// ($_SERVER['REQUEST_METHOD'] === 'POST'):
//
// 1. Haal $_POST['predictions'] op – dit is een array met de structuur:
//    [ match_id => ['home' => '2', 'away' => '1'], ... ]
// 2. Loop er doorheen met foreach.
// 3. Valideer per paar:
//    - Zijn beide waarden ingevuld (is_numeric)?
//    - Vallen ze in het bereik 0 – 20?
//    - Is de match_id een positief geheel getal?
// 4. Roep savePrediction($pdo, $userId, $matchId, $home, $away) aan.
// 5. Tel het aantal opgeslagen voorspellingen op in $savedCount.
// ============================================================

// ============================================================
// Taak student – Data ophalen
// 1. Roep getUpcomingMatches($pdo) aan (implementeer eerst in functions.php).
// 2. Groepeer de wedstrijden op group_letter:
//    foreach ($matches as $m) { $matchesByGroup[$m['group_letter']][] = $m; }
// 3. Roep getUserPredictions($pdo, $userId) aan voor bestaande scores.
//
// Vervang de dummy-data hieronder zodra de functies werken.
// ============================================================
$userPredictions = []; // key: match_id → ['predicted_home' => x, 'predicted_away' => y]

// Dummy-data – verwijder dit zodra je de echte queries hebt geschreven.
// De match-IDs komen overeen met de volgorde in database.sql (groep A = 1-6, B = 7-12, …)
$matchesByGroup = [
    'A' => [
        ['id'=>1,  'team_home'=>'Mexico',           'team_away'=>'Zuid-Afrika',          'city'=>'Mexico-Stad',   'match_date'=>'2026-06-11 20:00:00'],
        ['id'=>2,  'team_home'=>'Zuid-Korea',        'team_away'=>'Tsjechië',             'city'=>'Los Angeles',   'match_date'=>'2026-06-12 02:00:00'],
        ['id'=>3,  'team_home'=>'Mexico',            'team_away'=>'Zuid-Korea',            'city'=>'Mexico-Stad',   'match_date'=>'2026-06-18 20:00:00'],
        ['id'=>4,  'team_home'=>'Zuid-Afrika',       'team_away'=>'Tsjechië',             'city'=>'Dallas',        'match_date'=>'2026-06-18 23:00:00'],
        ['id'=>5,  'team_home'=>'Mexico',            'team_away'=>'Tsjechië',             'city'=>'Guadalajara',   'match_date'=>'2026-06-24 22:00:00'],
        ['id'=>6,  'team_home'=>'Zuid-Afrika',       'team_away'=>'Zuid-Korea',            'city'=>'Miami',         'match_date'=>'2026-06-24 22:00:00'],
    ],
    'B' => [
        ['id'=>7,  'team_home'=>'Canada',            'team_away'=>'Bosnië en Herzegovina','city'=>'Toronto',       'match_date'=>'2026-06-12 20:00:00'],
        ['id'=>8,  'team_home'=>'Zwitserland',       'team_away'=>'Qatar',                'city'=>'New York/NJ',   'match_date'=>'2026-06-12 23:00:00'],
        ['id'=>9,  'team_home'=>'Canada',            'team_away'=>'Qatar',                'city'=>'Vancouver',     'match_date'=>'2026-06-19 20:00:00'],
        ['id'=>10, 'team_home'=>'Zwitserland',       'team_away'=>'Bosnië en Herzegovina','city'=>'Seattle',       'match_date'=>'2026-06-19 23:00:00'],
        ['id'=>11, 'team_home'=>'Canada',            'team_away'=>'Zwitserland',          'city'=>'Toronto',       'match_date'=>'2026-06-25 18:00:00'],
        ['id'=>12, 'team_home'=>'Qatar',             'team_away'=>'Bosnië en Herzegovina','city'=>'Kansas City',   'match_date'=>'2026-06-25 18:00:00'],
    ],
    'C' => [
        ['id'=>13, 'team_home'=>'Brazilië',          'team_away'=>'Marokko',              'city'=>'New York/NJ',   'match_date'=>'2026-06-13 20:00:00'],
        ['id'=>14, 'team_home'=>'Haïti',             'team_away'=>'Schotland',            'city'=>'San Francisco', 'match_date'=>'2026-06-13 23:00:00'],
        ['id'=>15, 'team_home'=>'Brazilië',          'team_away'=>'Haïti',                'city'=>'Los Angeles',   'match_date'=>'2026-06-19 17:00:00'],
        ['id'=>16, 'team_home'=>'Marokko',           'team_away'=>'Schotland',            'city'=>'Houston',       'match_date'=>'2026-06-19 23:00:00'],
        ['id'=>17, 'team_home'=>'Brazilië',          'team_away'=>'Schotland',            'city'=>'Miami',         'match_date'=>'2026-06-25 22:00:00'],
        ['id'=>18, 'team_home'=>'Marokko',           'team_away'=>'Haïti',                'city'=>'Atlanta',       'match_date'=>'2026-06-25 22:00:00'],
    ],
    'D' => [
        ['id'=>19, 'team_home'=>'Verenigde Staten',  'team_away'=>'Paraguay',             'city'=>'Los Angeles',   'match_date'=>'2026-06-12 23:00:00'],
        ['id'=>20, 'team_home'=>'Australië',         'team_away'=>'Turkije',              'city'=>'San Francisco', 'match_date'=>'2026-06-13 02:00:00'],
        ['id'=>21, 'team_home'=>'Verenigde Staten',  'team_away'=>'Australië',            'city'=>'Los Angeles',   'match_date'=>'2026-06-20 20:00:00'],
        ['id'=>22, 'team_home'=>'Paraguay',          'team_away'=>'Turkije',              'city'=>'Dallas',        'match_date'=>'2026-06-20 23:00:00'],
        ['id'=>23, 'team_home'=>'Verenigde Staten',  'team_away'=>'Turkije',              'city'=>'New York/NJ',   'match_date'=>'2026-06-26 22:00:00'],
        ['id'=>24, 'team_home'=>'Paraguay',          'team_away'=>'Australië',            'city'=>'Atlanta',       'match_date'=>'2026-06-26 22:00:00'],
    ],
    'E' => [
        ['id'=>25, 'team_home'=>'Duitsland',         'team_away'=>'Curaçao',              'city'=>'Houston',       'match_date'=>'2026-06-14 20:00:00'],
        ['id'=>26, 'team_home'=>'Ivoorkust',         'team_away'=>'Ecuador',              'city'=>'Kansas City',   'match_date'=>'2026-06-14 23:00:00'],
        ['id'=>27, 'team_home'=>'Duitsland',         'team_away'=>'Ivoorkust',            'city'=>'Chicago',       'match_date'=>'2026-06-20 20:00:00'],
        ['id'=>28, 'team_home'=>'Curaçao',           'team_away'=>'Ecuador',              'city'=>'Seattle',       'match_date'=>'2026-06-20 23:00:00'],
        ['id'=>29, 'team_home'=>'Duitsland',         'team_away'=>'Ecuador',              'city'=>'Houston',       'match_date'=>'2026-06-26 18:00:00'],
        ['id'=>30, 'team_home'=>'Curaçao',           'team_away'=>'Ivoorkust',            'city'=>'Kansas City',   'match_date'=>'2026-06-26 18:00:00'],
    ],
    'F' => [
        ['id'=>31, 'team_home'=>'Nederland',         'team_away'=>'Japan',                'city'=>'Dallas',        'match_date'=>'2026-06-14 17:00:00'],
        ['id'=>32, 'team_home'=>'Tunesië',           'team_away'=>'Zweden',               'city'=>'Atlanta',       'match_date'=>'2026-06-14 23:00:00'],
        ['id'=>33, 'team_home'=>'Nederland',         'team_away'=>'Tunesië',              'city'=>'San Francisco', 'match_date'=>'2026-06-20 17:00:00'],
        ['id'=>34, 'team_home'=>'Japan',             'team_away'=>'Zweden',               'city'=>'Dallas',        'match_date'=>'2026-06-20 20:00:00'],
        ['id'=>35, 'team_home'=>'Nederland',         'team_away'=>'Zweden',               'city'=>'Dallas',        'match_date'=>'2026-06-26 22:00:00'],
        ['id'=>36, 'team_home'=>'Japan',             'team_away'=>'Tunesië',              'city'=>'Los Angeles',   'match_date'=>'2026-06-26 22:00:00'],
    ],
    'G' => [
        ['id'=>37, 'team_home'=>'België',            'team_away'=>'Egypte',               'city'=>'Seattle',       'match_date'=>'2026-06-15 20:00:00'],
        ['id'=>38, 'team_home'=>'Iran',              'team_away'=>'Nieuw-Zeeland',        'city'=>'Houston',       'match_date'=>'2026-06-15 23:00:00'],
        ['id'=>39, 'team_home'=>'België',            'team_away'=>'Iran',                 'city'=>'Atlanta',       'match_date'=>'2026-06-21 20:00:00'],
        ['id'=>40, 'team_home'=>'Egypte',            'team_away'=>'Nieuw-Zeeland',        'city'=>'Miami',         'match_date'=>'2026-06-21 23:00:00'],
        ['id'=>41, 'team_home'=>'België',            'team_away'=>'Nieuw-Zeeland',        'city'=>'Seattle',       'match_date'=>'2026-06-27 22:00:00'],
        ['id'=>42, 'team_home'=>'Egypte',            'team_away'=>'Iran',                 'city'=>'New York/NJ',   'match_date'=>'2026-06-27 22:00:00'],
    ],
    'H' => [
        ['id'=>43, 'team_home'=>'Spanje',            'team_away'=>'Kaapverdië',           'city'=>'Atlanta',       'match_date'=>'2026-06-15 17:00:00'],
        ['id'=>44, 'team_home'=>'Saoedi-Arabië',    'team_away'=>'Uruguay',              'city'=>'Dallas',        'match_date'=>'2026-06-15 23:00:00'],
        ['id'=>45, 'team_home'=>'Spanje',            'team_away'=>'Saoedi-Arabië',       'city'=>'Miami',         'match_date'=>'2026-06-21 17:00:00'],
        ['id'=>46, 'team_home'=>'Kaapverdië',        'team_away'=>'Uruguay',              'city'=>'Houston',       'match_date'=>'2026-06-21 23:00:00'],
        ['id'=>47, 'team_home'=>'Spanje',            'team_away'=>'Uruguay',              'city'=>'Kansas City',   'match_date'=>'2026-06-27 22:00:00'],
        ['id'=>48, 'team_home'=>'Kaapverdië',        'team_away'=>'Saoedi-Arabië',       'city'=>'Atlanta',       'match_date'=>'2026-06-27 22:00:00'],
    ],
    'I' => [
        ['id'=>49, 'team_home'=>'Frankrijk',         'team_away'=>'Senegal',              'city'=>'New York/NJ',   'match_date'=>'2026-06-16 20:00:00'],
        ['id'=>50, 'team_home'=>'Noorwegen',         'team_away'=>'Irak',                 'city'=>'Los Angeles',   'match_date'=>'2026-06-16 23:00:00'],
        ['id'=>51, 'team_home'=>'Frankrijk',         'team_away'=>'Noorwegen',            'city'=>'Seattle',       'match_date'=>'2026-06-22 20:00:00'],
        ['id'=>52, 'team_home'=>'Senegal',           'team_away'=>'Irak',                 'city'=>'Dallas',        'match_date'=>'2026-06-22 23:00:00'],
        ['id'=>53, 'team_home'=>'Frankrijk',         'team_away'=>'Irak',                 'city'=>'New York/NJ',   'match_date'=>'2026-06-28 22:00:00'],
        ['id'=>54, 'team_home'=>'Senegal',           'team_away'=>'Noorwegen',            'city'=>'Miami',         'match_date'=>'2026-06-28 22:00:00'],
    ],
    'J' => [
        ['id'=>55, 'team_home'=>'Argentinië',        'team_away'=>'Algerije',             'city'=>'Kansas City',   'match_date'=>'2026-06-16 17:00:00'],
        ['id'=>56, 'team_home'=>'Oostenrijk',        'team_away'=>'Jordanië',             'city'=>'Chicago',       'match_date'=>'2026-06-16 23:00:00'],
        ['id'=>57, 'team_home'=>'Argentinië',        'team_away'=>'Oostenrijk',           'city'=>'Los Angeles',   'match_date'=>'2026-06-22 17:00:00'],
        ['id'=>58, 'team_home'=>'Algerije',          'team_away'=>'Jordanië',             'city'=>'Atlanta',       'match_date'=>'2026-06-22 23:00:00'],
        ['id'=>59, 'team_home'=>'Argentinië',        'team_away'=>'Jordanië',             'city'=>'Seattle',       'match_date'=>'2026-06-28 18:00:00'],
        ['id'=>60, 'team_home'=>'Algerije',          'team_away'=>'Oostenrijk',           'city'=>'Kansas City',   'match_date'=>'2026-06-28 18:00:00'],
    ],
    'K' => [
        ['id'=>61, 'team_home'=>'Portugal',          'team_away'=>'DR Congo',             'city'=>'Houston',       'match_date'=>'2026-06-17 20:00:00'],
        ['id'=>62, 'team_home'=>'Oezbekistan',       'team_away'=>'Colombia',             'city'=>'Los Angeles',   'match_date'=>'2026-06-17 23:00:00'],
        ['id'=>63, 'team_home'=>'Portugal',          'team_away'=>'Oezbekistan',          'city'=>'Miami',         'match_date'=>'2026-06-23 17:00:00'],
        ['id'=>64, 'team_home'=>'Colombia',          'team_away'=>'DR Congo',             'city'=>'Dallas',        'match_date'=>'2026-06-23 20:00:00'],
        ['id'=>65, 'team_home'=>'Portugal',          'team_away'=>'Colombia',             'city'=>'San Francisco', 'match_date'=>'2026-06-29 22:00:00'],
        ['id'=>66, 'team_home'=>'Oezbekistan',       'team_away'=>'DR Congo',             'city'=>'Chicago',       'match_date'=>'2026-06-29 22:00:00'],
    ],
    'L' => [
        ['id'=>67, 'team_home'=>'Engeland',          'team_away'=>'Kroatië',              'city'=>'Dallas',        'match_date'=>'2026-06-17 17:00:00'],
        ['id'=>68, 'team_home'=>'Ghana',             'team_away'=>'Panama',               'city'=>'Atlanta',       'match_date'=>'2026-06-17 23:00:00'],
        ['id'=>69, 'team_home'=>'Engeland',          'team_away'=>'Ghana',                'city'=>'Seattle',       'match_date'=>'2026-06-23 20:00:00'],
        ['id'=>70, 'team_home'=>'Kroatië',           'team_away'=>'Panama',               'city'=>'Los Angeles',   'match_date'=>'2026-06-23 23:00:00'],
        ['id'=>71, 'team_home'=>'Engeland',          'team_away'=>'Panama',               'city'=>'New York/NJ',   'match_date'=>'2026-06-29 18:00:00'],
        ['id'=>72, 'team_home'=>'Kroatië',           'team_away'=>'Ghana',                'city'=>'Miami',         'match_date'=>'2026-06-29 18:00:00'],
    ],
]; // ← Vervang dit door de echte PDO-query via getUpcomingMatches()

$pageTitle = 'Voorspellingen';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <div class="predictions-header">
        <h2>&#127942; Mijn Voorspellingen</h2>
        <p class="predictions-subtitle">
            Vul voor iedere wedstrijd jouw verwachte eindstand in vóór aftrap.
            <strong>3 punten</strong> voor een exacte uitslag, <strong>1 punt</strong> voor de juiste winnaar.
        </p>
    </div>

    <?php if ($savedCount > 0): ?>
        <div class="alert alert-success">
            &#10003; <?= $savedCount ?> voorspelling<?= $savedCount > 1 ? 'en' : '' ?> opgeslagen!
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($matchesByGroup)): ?>
        <p class="empty-state">Er zijn geen openstaande wedstrijden om te voorspellen.</p>
    <?php else: ?>
        <form method="POST" action="predictions.php">

            <?php foreach ($matchesByGroup as $groupLetter => $matches): ?>
                <section class="predictions-group">
                    <div class="group-header">
                        <span class="group-badge">Groep <?= htmlspecialchars($groupLetter) ?></span>
                        <span class="group-match-count"><?= count($matches) ?> wedstrijd<?= count($matches) !== 1 ? 'en' : '' ?></span>
                    </div>

                    <?php foreach ($matches as $match): ?>
                        <?php
                        $matchId   = (int)$match['id'];
                        $savedHome = $userPredictions[$matchId]['predicted_home'] ?? '';
                        $savedAway = $userPredictions[$matchId]['predicted_away'] ?? '';
                        $hasPred   = ($savedHome !== '');
                        $dateObj   = new DateTime($match['match_date']);
                        ?>
                        <div class="match-card <?= $hasPred ? 'match-card--saved' : '' ?>">
                            <div class="match-meta">
                                <span class="match-date"><?= $dateObj->format('d M H:i') ?></span>
                                <span class="match-city">&#128205; <?= htmlspecialchars($match['city']) ?></span>
                                <?php if ($hasPred): ?>
                                    <span class="saved-badge">&#10003; Opgeslagen</span>
                                <?php endif; ?>
                            </div>
                            <div class="match-teams">
                                <span class="team-name team-home"><?= htmlspecialchars($match['team_home']) ?></span>
                                <div class="score-inputs">
                                    <input
                                        type="number"
                                        name="predictions[<?= $matchId ?>][home]"
                                        min="0"
                                        max="20"
                                        placeholder="–"
                                        value="<?= htmlspecialchars((string)$savedHome) ?>"
                                        class="score-input"
                                        aria-label="Doelpunten <?= htmlspecialchars($match['team_home']) ?>">
                                    <span class="score-sep">–</span>
                                    <input
                                        type="number"
                                        name="predictions[<?= $matchId ?>][away]"
                                        min="0"
                                        max="20"
                                        placeholder="–"
                                        value="<?= htmlspecialchars((string)$savedAway) ?>"
                                        class="score-input"
                                        aria-label="Doelpunten <?= htmlspecialchars($match['team_away']) ?>">
                                </div>
                                <span class="team-name team-away"><?= htmlspecialchars($match['team_away']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>

            <div class="predictions-submit">
                <button type="submit" class="btn btn-primary btn-lg">
                    &#128190; Alle voorspellingen opslaan
                </button>
            </div>

        </form>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
