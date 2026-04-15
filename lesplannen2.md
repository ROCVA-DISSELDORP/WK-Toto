# WK-Toto – Code Conventies & Lesplannen (versie 2 – generieke snippets)

> **Verschil met versie 1:** De code-snippets in de demo's zijn bewust generiek gehouden.
> Studenten zien het _patroon_, maar moeten zelf de juiste tabelnamen, kolomnamen en
> variabelenamen uit het project invullen.

---

## Sectie 0 – Code Conventies (voor AI-continuïteit)

> **Doel van deze sectie:** Een andere AI of ontwikkelaar kan het project op dezelfde manier voortzetten.

### Taal & Lokalisatie
- Alle commentaar, variabelenamen, foutmeldingen en UI-tekst zijn in **het Nederlands**
- Databasekolomnamen en SQL-keywords zijn in **het Engels**
- Bestandsnamen zijn in **het Engels**, kebab-case (`poule_create.php`)

### PHP Conventies
| Onderdeel | Conventie | Voorbeeld |
|---|---|---|
| Functies | `camelCase` | `registerUser()`, `checkLogin()` |
| Variabelen | `$camelCase` | `$pouleName`, `$userId` |
| Parameters | type-hints verplicht | `function foo(PDO $pdo, int $id)` |
| Return types | verplicht waar mogelijk | `: bool`, `: int`, `: array` |
| Includes | altijd `require_once` + `__DIR__` | `require_once __DIR__ . '/../config/db.php'` |
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

Toon het patroon om een bestand in te laden. Leg uit waar `__DIR__` voor staat en waarom het pad relatief is:

```php
// Patroon: bovenaan elke pagina
require_once __DIR__ . '/pad/naar/bestand.php';
```

Toon daarna het patroon voor een conditie op basis van een functie-aanroep:

```php
<?php if (eenFunctie()): ?>
    <!-- HTML voor situatie A -->
<?php else: ?>
    <!-- HTML voor situatie B -->
<?php endif; ?>
```

Vraag studenten: _"Welke functie gebruik je hier voor de login-check? En welke links horen in situatie A en B?"_

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

Toon het algemene patroon voor een SELECT-query via PDO. Gebruik een **fictieve** tabelnaam zodat studenten zelf de vertaalslag maken:

```php
// Patroon: data ophalen uit een tabel
$stmt = $pdo->prepare('SELECT * FROM jouw_tabel ORDER BY een_kolom ASC');
$stmt->execute();
$rijen = $stmt->fetchAll();

// Patroon: door het resultaat loopen
foreach ($rijen as $rij) {
    echo $rij['kolom_a'] . ' - ' . $rij['kolom_b'];
}
```

Vraag studenten: _"Welke tabel en kolommen gebruik je voor de wedstrijden? Zoek het op in `database.sql`."_

Toon ook kort de tabelstructuur in PHPMyAdmin: welke tabellen bestaan er en hoe zijn ze aan elkaar gekoppeld.

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

Toon de drie PHP-functies die studenten nodig hebben, zonder de context van het project:

```php
// Een waarde omzetten naar een veilige hash
$hash = password_hash($eenWaarde, PASSWORD_DEFAULT);

// Controleren of een waarde overeenkomt met een hash
$klopt = password_verify($ingevoerdeWaarde, $opgeslagenHash);

// Na succesvolle login: sessie vernieuwen en data opslaan
session_regenerate_id(true);
$_SESSION['sleutel'] = $waarde;
```

Toon daarna het patroon voor een INSERT met foutafhandeling:

```php
// Patroon: iets opslaan en omgaan met dubbele waarden
try {
    $stmt = $pdo->prepare('INSERT INTO tabel (kolom1, kolom2) VALUES (?, ?)');
    $stmt->execute([$waarde1, $waarde2]);
    return true;
} catch (PDOException $e) {
    return false;
}
```

Vraag studenten: _"Welke tabel, kolommen en waarden gebruik je voor de registratie? Welke kolom kan een duplicate-fout geven?"_

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

Toon hoe je een willekeurige string genereert. Gebruik een neutrale variabelenaam:

```php
// Patroon: willekeurige string van N tekens
$code = bin2hex(random_bytes(N));
// Resultaat: een string van 2*N tekens, bijv. "a3f7c2b1" bij N=4
```

Toon het patroon voor twee opeenvolgende INSERTs waarbij je de ID van de eerste nodig hebt voor de tweede:

```php
// Stap 1: eerste rij invoegen
$stmt = $pdo->prepare('INSERT INTO tabel_a (kolom1, kolom2) VALUES (?, ?)');
$stmt->execute([$waarde1, $waarde2]);
$nieuweId = (int)$pdo->lastInsertId();

// Stap 2: tweede rij invoegen met de ID van stap 1
$stmt = $pdo->prepare('INSERT INTO tabel_b (kolom_a, kolom_b) VALUES (?, ?)');
$stmt->execute([$andereWaarde, $nieuweId]);
```

Toon ook het patroon om te controleren of een rij al bestaat:

```php
// Patroon: bestaat deze combinatie al?
$stmt = $pdo->prepare('SELECT 1 FROM tabel WHERE kolom_a = ? AND kolom_b = ?');
$stmt->execute([$waarde1, $waarde2]);
if ($stmt->fetch()) {
    // bestaat al → doe niets of geef false terug
}
```

Vraag studenten: _"Welke tabellen, kolommen en waarden gebruik je voor het aanmaken van een poule? En welke twee waarden controleer je bij deelname?"_

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

Toon het patroon van een JOIN zonder projectspecifieke namen. Bouw hem op in PHPMyAdmin met fictieve tabellen, dan pas in PHP:

```php
// Patroon: gegevens uit twee tabellen combineren
$stmt = $pdo->prepare(
    'SELECT a.kolom1, a.kolom2, b.kolom3
     FROM tabel_a a
     INNER JOIN tabel_b b ON b.foreign_key = a.id
     WHERE a.filter_kolom = ?
     ORDER BY a.sorteer_kolom DESC'
);
$stmt->execute([$filterWaarde]);
$resultaat = $stmt->fetchAll();
```

Toon daarna het patroon voor een puntentelling met een vergelijking:

```php
// Patroon: punten optellen op basis van een vergelijking
$punten = 0;
foreach ($rijen as $rij) {
    if ($rij['voorspeld_a'] === $rij['werkelijk_a']
        && $rij['voorspeld_b'] === $rij['werkelijk_b']) {
        $punten += EXACT_GOED;   // vervang door het juiste aantal
    } elseif (/* schrijf hier de winnaar-conditie */) {
        $punten += TREND_GOED;   // vervang door het juiste aantal
    }
}
```

Vraag studenten: _"Welke tabellen join je voor het dashboard? En wat is de winnaar-conditie voor 1 punt?"_

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

Toon het patroon om een platte array te groeperen op een kolom:

```php
// Patroon: resultaat groeperen op een waarde
$gegroepeerd = [];
foreach ($platteLijst as $item) {
    $gegroepeerd[$item['groepeer_kolom']][] = $item;
}
```

Toon het patroon om een array te indexeren op een sleutelkolom (voor snel opzoeken):

```php
// Patroon: array indexeren op een sleutel
$geindexeerd = [];
foreach ($rijen as $rij) {
    $geindexeerd[$rij['sleutel_kolom']] = $rij;
}
// Opzoeken: $geindexeerd[$eenId]['gewenste_kolom']
```

Toon het patroon voor een upsert (invoegen of bijwerken):

```php
// Patroon: INSERT, maar bijwerken als de rij al bestaat
$stmt = $pdo->prepare(
    'INSERT INTO tabel (kolom1, kolom2, kolom3)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE kolom3 = VALUES(kolom3)'
);
$stmt->execute([$waarde1, $waarde2, $waarde3]);
```

Vraag studenten: _"Welke kolom is de sleutel in jouw `predictions`-tabel? Welke kolom(men) update je bij een duplicate?"_

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

Toon het patroon voor een UPDATE-query (voor de bonusopdracht):

```php
// Patroon: een rij bijwerken op basis van een ID
$stmt = $pdo->prepare(
    'UPDATE tabel SET kolom1 = ?, kolom2 = ? WHERE id = ?'
);
$stmt->execute([$nieuweWaarde1, $nieuweWaarde2, $rij_id]);
```

Vraag studenten: _"Welke tabel en kolommen update je om een uitslag in te voeren? Welke extra kolom moet ook op `1` gezet worden?"_

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
Les 7  →  Integratietest & bonusopdracht
```

Elke les bouwt direct voort op de vorige: je kunt pas inloggen (Les 3) als de database werkt (Les 2), je kunt pas een poule aanmaken (Les 4) als je ingelogd kunt zijn, en de ranglijst (Les 5) heeft voorspellingen nodig (Les 6 geeft de input). Les 7 sluit de cirkel.
