# Project: WK-Toto Poule Bouwen

Welkom bij de backend-opdracht voor het bouwen van een WK-Toto systeem. In dit project ga je een functionele website maken waar gebruikers accounts kunnen aanmaken en eigen poules (groepen) kunnen beheren.

---

## Installatie

1. Importeer `database.sql` in je lokale MySQL-database via **PHPMyAdmin** of de MySQL-CLI:
   ```bash
   mysql -u root -p < database.sql
   ```
2. Open `config/db.php` en pas de databasegegevens aan (hostnaam, databasenaam, gebruiker, wachtwoord).
3. Controleer de `BASE_URL` in `config/db.php`. Staat je project in de map `/wk-toto` op XAMPP? Dan is de standaardwaarde correct.
4. Open je browser en navigeer naar `http://localhost/wk-toto/public/index.php`.

---

## Mappenstructuur

```
/wk-toto
├── /config
│   └── db.php              ← Databaseverbinding en BASE_URL
├── /includes
│   ├── functions.php       ← JOUW TAAK: implementeer de functies
│   └── auth_check.php      ← Sessiebewaking
├── /partials
│   ├── header.php          ← <head>, CSS-links, start <body>
│   ├── navbar.php          ← Navigatiebalk (dynamisch op basis van login)
│   └── footer.php          ← Sluit <body> en <html>
├── /public
│   ├── /css
│   │   └── style.css
│   ├── /js
│   │   └── main.js
│   ├── index.php           ← Landingspagina
│   ├── register.php        ← Registratieformulier
│   ├── login.php           ← Loginformulier
│   ├── logout.php          ← Sessie beëindigen
│   ├── dashboard.php       ← Overzicht van jouw poules
│   ├── poule.php           ← Detailpagina + ranglijst van een poule
│   ├── poule_create.php    ← Nieuwe poule aanmaken
│   └── poule_join.php      ← Deelnemen aan poule via code
├── database.sql            ← Database-schema
└── README.md               ← Dit bestand
```

---

## Jouw opdracht

Je krijgt een "skelet": de structuur en pagina's zijn al aangemaakt, maar de logica ontbreekt. Vul de functies in `includes/functions.php` in.

### Fase 1 – De Basis (Header/Footer)

Alle pagina's in `/public` laden al de juiste partials via `require_once`. Bekijk hoe dit werkt en controleer of de navigatiebalk correct verandert:

- **Niet ingelogd?** → Toon: *Home*, *Login*, *Registreren*
- **Wel ingelogd?** → Toon: *Dashboard*, *Poule Aanmaken*, *Deelnemen*, *Uitloggen*

De logica hiervoor staat in `partials/navbar.php`. Begrijp hoe `checkLogin()` wordt gebruikt.

---

### Fase 2 – Login Systeem

Open `includes/functions.php` en implementeer:

#### `registerUser(PDO $pdo, string $username, string $email, string $password): bool`
1. Hash het wachtwoord met `password_hash($password, PASSWORD_DEFAULT)`.
2. Sla de gebruiker op in de tabel `users`.
3. Vang een `PDOException` op als de gebruikersnaam/e-mail al bestaat (UNIQUE constraint) en geef `false` terug.

#### `loginUser(PDO $pdo, string $username, string $password): bool`
1. Zoek de gebruiker op via `username`.
2. Controleer het wachtwoord met `password_verify($password, $row['password_hash'])`.
3. Sla bij succes `$_SESSION['user_id']` en `$_SESSION['username']` op.
4. Roep `session_regenerate_id(true)` aan om session fixation te voorkomen.

---

### Fase 3 – Poule Beheer

#### `createPoule(PDO $pdo, int $userId, string $pouleName): int|false`
1. Genereer een unieke uitnodigingscode: `bin2hex(random_bytes(4))` (geeft 8 tekens).
2. Sla de poule op in tabel `poules`.
3. Voeg de aanmaker direct toe als deelnemer in `poule_participants`.
4. Geef de nieuwe poule-ID terug.

#### `joinPoule(PDO $pdo, int $userId, string $inviteCode): bool`
1. Zoek de poule op via `invite_code`.
2. Controleer of de gebruiker al deelneemt (voorkom dubbele rijen).
3. Voeg de gebruiker toe aan `poule_participants`.

#### `calculatePoints(PDO $pdo, int $userId, int $pouleId): int`
1. Haal alle **afgeronde** wedstrijden op (`is_finished = 1`).
2. Vergelijk de voorspelling van de gebruiker met de werkelijke uitslag.
3. Puntensysteem:
   - **1 punt** – Juiste winnaar/gelijkspel (trend correct).
   - **3 punten** – Exacte uitslag correct.

---

## Veiligheidseisen

| Eis | Hoe |
|-----|-----|
| SQL-injecties voorkomen | Gebruik altijd **Prepared Statements** via PDO |
| Wachtwoorden hashen | Gebruik `password_hash()` + `password_verify()` |
| XSS voorkomen | Gebruik `htmlspecialchars()` bij elke output naar HTML |
| Formulieren valideren | Controleer lege invoer en formaten server-side |
| Sessies beveiligen | Gebruik `session_regenerate_id(true)` na login |

---

## Bonus

- Voeg een profielfoto toe aan de `users`-tabel (sla het bestandspad op, niet het bestand zelf in de database).
- Maak een beheerderspagina `admin.php` waar uitslagen van wedstrijden ingevoerd kunnen worden.
- Voeg een voorspellingspagina toe waar gebruikers per wedstrijd een score kunnen invoeren vóór aftrap.

---

## Handige tips

```php
// Unieke uitnodigingscode genereren
$code = bin2hex(random_bytes(4)); // bijv. "a3f7c2b1"

// Foutmeldingen tonen tijdens ontwikkeling (alleen lokaal!)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Controleer het laatste PDO-statement voor debugging
var_dump($stmt->errorInfo());
```
