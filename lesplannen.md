# WK-Toto – Code Conventies & Lesplannen

## Sectie 0 – Code Conventies (voor AI-continuïteit)

> **Doel van deze sectie:** Een andere AI of ontwikkelaar kan het project op dezelfde manier voortzetten.

### Taal & Lokalisatie
- Alle commentaar, variabelenamen, foutmeldingen en UI-tekst zijn in **het Nederlands**
- Databasekolomnamen en SQL-keywords zijn in **het Engels**
- Bestandsnamen zijn in **het Engels**, kebab-case (poule_create.php)

### PHP Conventies
| Onderdeel | Conventie | Voorbeeld |
|---|---|---|
| Functies | `camelCase` | `registerUser()`, `checkLogin()` |
| Variabelen | `$camelCase` | `$pouleName`, `$userId` |
| Parameters | type-hints verplicht | `function foo(PDO $pdo, int $id)` |
| Return types | verplicht waar mogelijk | `: bool`, `: int`, `: array` |
| Includes | altijd `require_once` + `__DIR__` | `require_once __DIR__ . db.php'` |
| Sessiecheck | altijd vóór sessiegebruik | `if (session_status() === PHP_SESSION_NONE)` |
| Output escaping | altijd `htmlspecialchars()` | `<?= htmlspecialchars($var) ?>` |
| Short echo | toegestaan voor output | `<?= ... ?>` |

### Beveiliging (verplicht in elke DB-aanroep)
- **Prepared statements** via PDO – nooit string-interpolatie in SQL
- **`password_hash(PASSWORD_DEFAULT)`** voor opslaan, **`password_verify()`** voor controleren
- **`session_regenerate_id(true)`** na elke succesvolle login
- **`filter_input()`** of `(int)` casting bij GET/POST-parameters die integers verwachten
- Foutmeldingen aan eindgebruiker zijn altijd **vaag** (geen technische details)

### Mappenstructuur (rollen)
```
/config      → Alleen configuratie (PDO, constanten)
/includes    → Alleen PHP-logica, geen HTML
/partials    → Alleen HTML-fragmenten, minimale PHP
/public      → Alleen publiek toegankelijke pagina's + assets
```

### Database Conventies
- Engine: `InnoDB`, charset: `utf8mb4_unicode_ci`
- PK: `INT UNSIGNED AUTO_INCREMENT`, naam altijd `id`
- FK-namen: `fk_<tabel>_<kolom>` (bijv. `fk_poules_admin`)
- Tijdstempels: `TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP`
- Booleans: `TINYINT(1)`, standaard `0`
- Nullable kolommen: expliciet `DEFAULT NULL`

### CSS Conventies
- CSS-variabelen in `:root`, prefix `--color-`, `--radius`, `--shadow`, `--transition`
- BEM-geïnspireerd: `.block`, `.block__element`, `.block--modifier` (bijv. `.match-card--saved`)
- Utility-classes: `.btn`, `.btn-primary`, `.btn-sm`, `.btn-full`, `.btn-lg`
- Responsiveness via één `@media (max-width: 640px)` blok onderaan

---

## Sectie 1 – De 7 Lesplannen

---

### Les 1 – Projectstructuur & Het Partials Systeem

**Duur:** 90 minuten  
**Fase in het project:** Fase 1

#### Leerdoelen
Na deze les kan de student:
- Weten hoe het vak is opgebouwd en wat ze deze periode gaan maken
- De mappenstructuur begrijpen en weten welk bestand waarvoor dient
- `require_once` gebruiken om bestanden te koppelen
- De navigatiebalk dynamisch maken op basis van de sessiestatus

#### Demo (±20 min)

Toon live hoe een pagina is opgebouwd uit losse onderdelen. Laat zien hoe je een bestand inlaadt:

```php
// Bovenaan elke pagina in /public
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';

// ... pagina-inhoud ...

require_once __DIR__ . '/../partials/footer.php';
```

Toon daarna hoe de navbar reageert op de sessie:

```php
// In partials/navbar.php
<?php if (checkLogin()): ?>
    <li><a href="...">Dashboard</a></li>
    <li><a href="...">Uitloggen</a></li>
<?php else: ?>
    <li><a href="...">Login</a></li>
    <li><a href="...">Registreren</a></li>
<?php endif; ?>
```

#### Praktijk (±70 min)
1. Verken de projectmap en open elk bestand — stel vast wat elk bestand doet
2. Koppel `header.php`, `navbar.php` en `footer.php` in alle pagina's in `/public`
3. Maak de navigatiebalk dynamisch:
   - Niet ingelogd → toon Home, Login, Registreren
   - Ingelogd → toon Dashboard, Voorspellingen, Uitloggen
4. Test in de browser: werkt de basis lay-out?

---

### Les 2 – Database & PDO Verbinding

**Duur:** 90 minuten  
**Fase in het project:** Fase 1 / Opmaat naar Fase 2

#### Leerdoelen
Na deze les kan de student:
- De database importeren en de tabellen herkennen in PHPMyAdmin
- `config/db.php` aanpassen met de juiste gegevens
- Een eenvoudige SELECT-query uitvoeren via PDO en het resultaat tonen

#### Demo (±20 min)

Loop kort door `config/db.php` en wijs aan wat studenten moeten aanpassen. Toon daarna hoe je een query uitvoert:

```php
// Data ophalen met een prepared statement
$stmt = $pdo->prepare('SELECT * FROM matches ORDER BY match_date ASC');
$stmt->execute();
$matches = $stmt->fetchAll(); // geeft een array van rijen terug

// Resultaat tonen
foreach ($matches as $match) {
    echo $match['team_home'] . ' vs ' . $match['team_away'];
}
```

Toon ook kort de tabelstructuur in PHPMyAdmin: welke tabellen bestaan er en hoe zijn ze aan elkaar gekoppeld (`users` → `poules` → `poule_participants`).

#### Praktijk (±70 min)
1. Pas `config/db.php` aan met je eigen XAMPP-gegevens
2. Importeer `database.sql` via PHPMyAdmin
3. Schrijf een testscript `public/test_db.php` dat:
   - De verbinding test
   - Alle wedstrijden uit `matches` ophaalt en in een `<ul>` toont
4. Verwijder `test_db.php` zodra het werkt
5. Bekijk in PHPMyAdmin de tabellen en verken de ingevoerde wedstrijddata

---

### Les 3 – Authenticatie: Registreren & Inloggen

**Duur:** 100 minuten  
**Fase in het project:** Fase 2

#### Leerdoelen
Na deze les kan de student:
- `registerUser()` implementeren met wachtwoord-hashing
- `loginUser()` implementeren met sessiestart
- `auth_check.php` gebruiken om pagina's te beveiligen
- `logout.php` volledig afwerken

#### Demo (±25 min)

Toon de drie sleutelfuncties die studenten nodig hebben:

```php
// Wachtwoord hashen bij registratie
$hash = password_hash($password, PASSWORD_DEFAULT);

// Wachtwoord controleren bij login
if (password_verify($password, $hash)) {
    // wachtwoord klopt
}

// Sessie starten en gebruiker opslaan
session_start();
session_regenerate_id(true); // voorkomt session hijacking
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
```

Toon ook hoe je een INSERT doet met een prepared statement en hoe je een PDOException opvangt bij een dubbele gebruikersnaam:

```php
try {
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
    $stmt->execute([$username, $email, $hash]);
    return true;
} catch (PDOException $e) {
    return false; // gebruikersnaam of e-mail bestaat al
}
```

#### Praktijk (±75 min)
1. Implementeer `registerUser($pdo, $username, $email, $password)` in `functions.php`
2. Test via `register.php` in de browser — controleer in PHPMyAdmin of de rij er staat
3. Implementeer `loginUser($pdo, $username, $password)` in `functions.php`
4. Test via `login.php` — controleer of je doorgestuurd wordt naar `dashboard.php`
5. Implementeer `logout.php` volledig
6. Zorg dat `dashboard.php` beveiligd is via `auth_check.php`

---

### Les 4 – Poule Beheer: Aanmaken & Deelnemen

**Duur:** 90 minuten  
**Fase in het project:** Fase 3a

#### Leerdoelen
Na deze les kan de student:
- `createPoule()` implementeren met een unieke uitnodigingscode
- `joinPoule()` implementeren met controle op dubbele deelname
- Twee INSERTs na elkaar uitvoeren (poule aanmaken + deelnemer toevoegen)

#### Demo (±20 min)

Toon hoe je een willekeurige invite code genereert en twee tabellen vult:

```php
// Unieke code genereren (8 tekens)
$inviteCode = bin2hex(random_bytes(4)); // bijv. "a3f7c2b1"

// Poule opslaan
$stmt = $pdo->prepare('INSERT INTO poules (name, invite_code, admin_id) VALUES (?, ?, ?)');
$stmt->execute([$pouleName, $inviteCode, $userId]);
$pouleId = (int)$pdo->lastInsertId();

// Aanmaker direct toevoegen als deelnemer
$stmt = $pdo->prepare('INSERT INTO poule_participants (user_id, poule_id) VALUES (?, ?)');
$stmt->execute([$userId, $pouleId]);
```

Toon ook hoe je via een invite code een poule opzoekt en controleert of iemand al deelneemt:

```php
// Poule zoeken
$stmt = $pdo->prepare('SELECT id FROM poules WHERE invite_code = ?');
$stmt->execute([$inviteCode]);
$poule = $stmt->fetch();

// Controleer of gebruiker al deelneemt
$stmt = $pdo->prepare('SELECT 1 FROM poule_participants WHERE user_id = ? AND poule_id = ?');
$stmt->execute([$userId, $poule['id']]);
if ($stmt->fetch()) {
    return false; // al deelnemer
}
```

#### Praktijk (±70 min)
1. Implementeer `createPoule($pdo, $userId, $pouleName)` in `functions.php`
2. Implementeer `joinPoule($pdo, $userId, $inviteCode)` in `functions.php`
3. Test de volledige flow: poule aanmaken → invite code noteren → tweede account aanmaken → deelnemen via de code

---

### Les 5 – Relaties Opvragen: Dashboard & Ranglijst

**Duur:** 100 minuten  
**Fase in het project:** Fase 3b

#### Leerdoelen
Na deze les kan de student:
- Een JOIN-query schrijven om gegevens uit meerdere tabellen te combineren
- De dummy-data in `dashboard.php` vervangen door een echte query
- `calculatePoints()` implementeren en de ranglijst opbouwen

#### Demo (±25 min)

Toon een JOIN-query stap voor stap — bouw hem eerst in PHPMyAdmin, daarna in PHP:

```php
// Alle poules ophalen waar de ingelogde gebruiker deelnemer van is
$stmt = $pdo->prepare(
    'SELECT p.id, p.name, p.invite_code
     FROM poules p
     INNER JOIN poule_participants pp ON pp.poule_id = p.id
     WHERE pp.user_id = ?
     ORDER BY p.created_at DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$myPoules = $stmt->fetchAll();
```

Toon daarna het puntensysteem en hoe je het vergelijkt:

```php
// Puntentelling per afgeronde wedstrijd
foreach ($results as $row) {
    if ($row['predicted_home'] === $row['score_home']
        && $row['predicted_away'] === $row['score_away']) {
        $points += 3; // exacte uitslag
    } elseif (/* juiste winnaar of gelijkspel */) {
        $points += 1;
    }
}
```

#### Praktijk (±75 min)

**Dashboard:**
1. Schrijf de JOIN-query in `dashboard.php` en vervang de dummy-data
2. Test: log in en controleer of jouw poules verschijnen

**Ranglijst:**
3. Implementeer `calculatePoints($pdo, $userId, $pouleId)` in `functions.php`
4. Schrijf de ranglijstquery in `poule.php`: haal alle deelnemers op, bereken hun punten en sorteer op punten
5. Test: voeg via PHPMyAdmin een uitslag in (`is_finished = 1`, `score_home`, `score_away`) en controleer de ranglijst

---

### Les 6 – Voorspellingen Invoeren & Verwerken

**Duur:** 100 minuten  
**Fase in het project:** Fase 3c

#### Leerdoelen
Na deze les kan de student:
- `getUpcomingMatches()` implementeren en het resultaat groeperen per groepsletter
- `getUserPredictions()` implementeren zodat bestaande scores al ingevuld staan
- `savePrediction()` implementeren met een upsert
- De POST-verwerking in `predictions.php` afwerken

#### Demo (±25 min)

Toon hoe je wedstrijden ophaalt en groepeert:

```php
// Alle nog niet gespeelde wedstrijden ophalen
$stmt = $pdo->prepare('SELECT * FROM matches WHERE is_finished = 0 ORDER BY match_date ASC');
$stmt->execute();

// Groeperen per groepsletter
$matchesByGroup = [];
foreach ($stmt->fetchAll() as $match) {
    $matchesByGroup[$match['group_letter']][] = $match;
}
```

Toon hoe je bestaande voorspellingen inlaadt (geïndexeerd zodat je snel kunt opzoeken):

```php
$stmt = $pdo->prepare('SELECT match_id, predicted_home, predicted_away FROM predictions WHERE user_id = ?');
$stmt->execute([$userId]);

$userPredictions = [];
foreach ($stmt->fetchAll() as $row) {
    $userPredictions[$row['match_id']] = $row;
}
```

Toon de upsert om een voorspelling op te slaan of bij te werken:

```php
$stmt = $pdo->prepare(
    'INSERT INTO predictions (user_id, match_id, predicted_home, predicted_away)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE predicted_home = VALUES(predicted_home),
                              predicted_away = VALUES(predicted_away)'
);
$stmt->execute([$userId, $matchId, $home, $away]);
```

#### Praktijk (±75 min)
1. Implementeer `getUpcomingMatches($pdo)` in `functions.php`
2. Groepeer het resultaat per `group_letter` in `predictions.php` en vervang de dummy-data
3. Implementeer `getUserPredictions($pdo, $userId)` in `functions.php`
4. Zorg dat bestaande scores al in de invoervelden staan (pre-fill)
5. Implementeer de POST-verwerking in `predictions.php` — loop door `$_POST['predictions']` en roep `savePrediction()` aan
6. Implementeer `savePrediction()` in `functions.php` met de upsert-query
7. Test: vul scores in, sla op, herlaad de pagina — staan de scores er nog?

---

### Les 7 – Afronden, Testen & Bonusopdracht

**Duur:** 120 minuten  
**Fase in het project:** Afronding + Bonus

#### Leerdoelen
Na deze les kan de student:
- De volledige applicatie van begin tot eind testen met twee accounts
- Ontbrekende functionaliteit zelfstandig opsporen en oplossen
- (Bonus) een admin-pagina bouwen voor het invoeren van uitslagen

#### Demo (±15 min)

Toon ter afsluiting kort hoe een admin-pagina eruit zou zien: een formulier dat een uitslag opslaat en `is_finished` op `1` zet:

```php
// Uitslag opslaan (alleen voor admins)
$stmt = $pdo->prepare(
    'UPDATE matches SET score_home = ?, score_away = ?, is_finished = 1 WHERE id = ?'
);
$stmt->execute([$scoreHome, $scoreAway, $matchId]);
```

#### Praktijk (±105 min)

**Integratie-test:**
1. Maak twee accounts aan
2. Account 1 maakt een poule en vult voorspellingen in
3. Account 2 joint de poule via de invite code en vult andere voorspellingen in
4. Voer via PHPMyAdmin een uitslag in: zet `is_finished = 1` en vul `score_home` en `score_away` in
5. Controleer of de ranglijst de juiste punten toont voor beide accounts

**Bonusopdracht (voor wie klaar is):**
- Bouw `admin.php`:
  - Beveiligd via `auth_check.php`
  - Voeg een kolom `is_admin TINYINT(1) DEFAULT 0` toe aan de `users`-tabel
  - Controleer bovenaan `admin.php` of `$_SESSION['user_id']` een admin is, anders redirect
  - Formulier: kies een wedstrijd, vul de uitslagen in, sla op met de UPDATE-query hierboven
  - Controleer daarna of de ranglijst automatisch klopt via `calculatePoints()`

---

## Rode Draad per Fase

```
Les 1  →  Structuur begrijpen & partials koppelen
Les 2  →  Database opzetten & PDO leren
Les 3  →  Authenticatie (register + login + logout)
Les 4  →  Poule aanmaken & deelnemen
Les 5  →  Data opvragen met JOIN (dashboard + ranglijst)
Les 6  →  Voorspellingen invoeren & opslaan
Les 7  →  Integratietest, beveiliging & presentatie
```

Elke les bouwt direct voort op de vorige: je kunt pas inloggen (Les 3) als de database werkt (Les 2), je kunt pas een poule aanmaken (Les 4) als je ingelogd kunt zijn, en de ranglijst (Les 5) heeft voorspellingen nodig (Les 6 geeft de input). Les 7 sluit de cirkel.