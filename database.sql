-- ============================================================
-- WK-Toto Database Schema
-- Importeer dit bestand via PHPMyAdmin of de MySQL CLI:
--   mysql -u root -p < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `wk_toto`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `wk_toto`;

-- ------------------------------------------------------------
-- Tabel: users
-- Slaat accountgegevens op van alle geregistreerde gebruikers.
-- Let op: wachtwoorden worden NOOIT als plaintext opgeslagen.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)     NOT NULL,
    `email`         VARCHAR(255)    NOT NULL,
    `password_hash` VARCHAR(255)    NOT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_username` (`username`),
    UNIQUE KEY `uq_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: poules
-- Een poule heeft een unieke uitnodigingscode (invite_code)
-- en een admin-gebruiker (admin_id).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `poules` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)  NOT NULL,
    `invite_code` VARCHAR(8)    NOT NULL,
    `admin_id`    INT UNSIGNED  NOT NULL,
    `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_invite_code` (`invite_code`),
    CONSTRAINT `fk_poules_admin`
        FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: poule_participants  (veel-op-veel: users <-> poules)
-- Een gebruiker kan deelnemen aan meerdere poules,
-- een poule kan meerdere deelnemers hebben.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `poule_participants` (
    `user_id`   INT UNSIGNED NOT NULL,
    `poule_id`  INT UNSIGNED NOT NULL,
    `joined_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `poule_id`),
    CONSTRAINT `fk_pp_user`
        FOREIGN KEY (`user_id`)  REFERENCES `users`  (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pp_poule`
        FOREIGN KEY (`poule_id`) REFERENCES `poules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: matches  (WK 2026 groepsfasewedstrijden)
-- group_letter : de poulegroep (A t/m L)
-- city         : speelstad in de VS, Mexico of Canada
-- score_home/away zijn NULL zolang de wedstrijd nog niet is ingevoerd.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `matches` (
    `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `group_letter` CHAR(1)          NOT NULL,
    `team_home`    VARCHAR(100)     NOT NULL,
    `team_away`    VARCHAR(100)     NOT NULL,
    `city`         VARCHAR(100)     NOT NULL DEFAULT '',
    `score_home`   TINYINT UNSIGNED DEFAULT NULL,
    `score_away`   TINYINT UNSIGNED DEFAULT NULL,
    `match_date`   DATETIME         NOT NULL,
    `is_finished`  TINYINT(1)       NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_group_letter` (`group_letter`),
    KEY `idx_match_date`   (`match_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: predictions  (voorspellingen – één per gebruiker per wedstrijd)
-- Voorspellingen zijn globaal: een gebruiker geeft per wedstrijd
-- één uitslag op, die in alle poules waar hij/zij in zit geldt.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `predictions` (
    `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `user_id`        INT UNSIGNED     NOT NULL,
    `match_id`       INT UNSIGNED     NOT NULL,
    `predicted_home` TINYINT UNSIGNED NOT NULL,
    `predicted_away` TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_prediction` (`user_id`, `match_id`),
    CONSTRAINT `fk_pred_user`
        FOREIGN KEY (`user_id`)  REFERENCES `users`   (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pred_match`
        FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- WK 2026 – Groepsfase wedstrijden (72 wedstrijden, 12 groepen)
-- Speeldata: 11 juni t/m 29 juni 2026
-- Tijden zijn lokale VS/Mexico/Canada-tijd (CEST -6 tot -8 uur)
-- Matchday 1: jun 11-17 | Matchday 2: jun 18-23 | Matchday 3: jun 24-29
-- ------------------------------------------------------------
INSERT INTO `matches` (`group_letter`, `team_home`, `team_away`, `city`, `match_date`) VALUES
-- Groep A: Mexico, Zuid-Afrika, Zuid-Korea, Tsjechië
('A', 'Mexico',           'Zuid-Afrika',          'Mexico-Stad',   '2026-06-11 20:00:00'),
('A', 'Zuid-Korea',       'Tsjechië',             'Los Angeles',   '2026-06-12 02:00:00'),
('A', 'Mexico',           'Zuid-Korea',            'Mexico-Stad',   '2026-06-18 20:00:00'),
('A', 'Zuid-Afrika',      'Tsjechië',             'Dallas',        '2026-06-18 23:00:00'),
('A', 'Mexico',           'Tsjechië',             'Guadalajara',   '2026-06-24 22:00:00'),
('A', 'Zuid-Afrika',      'Zuid-Korea',            'Miami',         '2026-06-24 22:00:00'),
-- Groep B: Canada, Zwitserland, Qatar, Bosnië en Herzegovina
('B', 'Canada',           'Bosnië en Herzegovina', 'Toronto',       '2026-06-12 20:00:00'),
('B', 'Zwitserland',      'Qatar',                 'New York/NJ',   '2026-06-12 23:00:00'),
('B', 'Canada',           'Qatar',                 'Vancouver',     '2026-06-19 20:00:00'),
('B', 'Zwitserland',      'Bosnië en Herzegovina', 'Seattle',       '2026-06-19 23:00:00'),
('B', 'Canada',           'Zwitserland',           'Toronto',       '2026-06-25 18:00:00'),
('B', 'Qatar',            'Bosnië en Herzegovina', 'Kansas City',   '2026-06-25 18:00:00'),
-- Groep C: Brazilië, Marokko, Haïti, Schotland
('C', 'Brazilië',         'Marokko',               'New York/NJ',   '2026-06-13 20:00:00'),
('C', 'Haïti',            'Schotland',             'San Francisco', '2026-06-13 23:00:00'),
('C', 'Brazilië',         'Haïti',                 'Los Angeles',   '2026-06-19 17:00:00'),
('C', 'Marokko',          'Schotland',             'Houston',       '2026-06-19 23:00:00'),
('C', 'Brazilië',         'Schotland',             'Miami',         '2026-06-25 22:00:00'),
('C', 'Marokko',          'Haïti',                 'Atlanta',       '2026-06-25 22:00:00'),
-- Groep D: Verenigde Staten, Paraguay, Australië, Turkije
('D', 'Verenigde Staten', 'Paraguay',              'Los Angeles',   '2026-06-12 23:00:00'),
('D', 'Australië',        'Turkije',               'San Francisco', '2026-06-13 02:00:00'),
('D', 'Verenigde Staten', 'Australië',             'Los Angeles',   '2026-06-20 20:00:00'),
('D', 'Paraguay',         'Turkije',               'Dallas',        '2026-06-20 23:00:00'),
('D', 'Verenigde Staten', 'Turkije',               'New York/NJ',   '2026-06-26 22:00:00'),
('D', 'Paraguay',         'Australië',             'Atlanta',       '2026-06-26 22:00:00'),
-- Groep E: Duitsland, Curaçao, Ivoorkust, Ecuador
('E', 'Duitsland',        'Curaçao',               'Houston',       '2026-06-14 20:00:00'),
('E', 'Ivoorkust',        'Ecuador',               'Kansas City',   '2026-06-14 23:00:00'),
('E', 'Duitsland',        'Ivoorkust',             'Chicago',       '2026-06-20 20:00:00'),
('E', 'Curaçao',          'Ecuador',               'Seattle',       '2026-06-20 23:00:00'),
('E', 'Duitsland',        'Ecuador',               'Houston',       '2026-06-26 18:00:00'),
('E', 'Curaçao',          'Ivoorkust',             'Kansas City',   '2026-06-26 18:00:00'),
-- Groep F: Nederland, Japan, Tunesië, Zweden
('F', 'Nederland',        'Japan',                 'Dallas',        '2026-06-14 17:00:00'),
('F', 'Tunesië',          'Zweden',                'Atlanta',       '2026-06-14 23:00:00'),
('F', 'Nederland',        'Tunesië',               'San Francisco', '2026-06-20 17:00:00'),
('F', 'Japan',            'Zweden',                'Dallas',        '2026-06-20 20:00:00'),
('F', 'Nederland',        'Zweden',                'Dallas',        '2026-06-26 22:00:00'),
('F', 'Japan',            'Tunesië',               'Los Angeles',   '2026-06-26 22:00:00'),
-- Groep G: België, Egypte, Iran, Nieuw-Zeeland
('G', 'België',           'Egypte',                'Seattle',       '2026-06-15 20:00:00'),
('G', 'Iran',             'Nieuw-Zeeland',         'Houston',       '2026-06-15 23:00:00'),
('G', 'België',           'Iran',                  'Atlanta',       '2026-06-21 20:00:00'),
('G', 'Egypte',           'Nieuw-Zeeland',         'Miami',         '2026-06-21 23:00:00'),
('G', 'België',           'Nieuw-Zeeland',         'Seattle',       '2026-06-27 22:00:00'),
('G', 'Egypte',           'Iran',                  'New York/NJ',   '2026-06-27 22:00:00'),
-- Groep H: Spanje, Kaapverdië, Saoedi-Arabië, Uruguay
('H', 'Spanje',           'Kaapverdië',            'Atlanta',       '2026-06-15 17:00:00'),
('H', 'Saoedi-Arabië',   'Uruguay',               'Dallas',        '2026-06-15 23:00:00'),
('H', 'Spanje',           'Saoedi-Arabië',        'Miami',         '2026-06-21 17:00:00'),
('H', 'Kaapverdië',       'Uruguay',               'Houston',       '2026-06-21 23:00:00'),
('H', 'Spanje',           'Uruguay',               'Kansas City',   '2026-06-27 22:00:00'),
('H', 'Kaapverdië',       'Saoedi-Arabië',        'Atlanta',       '2026-06-27 22:00:00'),
-- Groep I: Frankrijk, Senegal, Noorwegen, Irak
('I', 'Frankrijk',        'Senegal',               'New York/NJ',   '2026-06-16 20:00:00'),
('I', 'Noorwegen',        'Irak',                  'Los Angeles',   '2026-06-16 23:00:00'),
('I', 'Frankrijk',        'Noorwegen',             'Seattle',       '2026-06-22 20:00:00'),
('I', 'Senegal',          'Irak',                  'Dallas',        '2026-06-22 23:00:00'),
('I', 'Frankrijk',        'Irak',                  'New York/NJ',   '2026-06-28 22:00:00'),
('I', 'Senegal',          'Noorwegen',             'Miami',         '2026-06-28 22:00:00'),
-- Groep J: Argentinië, Algerije, Oostenrijk, Jordanië
('J', 'Argentinië',       'Algerije',              'Kansas City',   '2026-06-16 17:00:00'),
('J', 'Oostenrijk',       'Jordanië',              'Chicago',       '2026-06-16 23:00:00'),
('J', 'Argentinië',       'Oostenrijk',            'Los Angeles',   '2026-06-22 17:00:00'),
('J', 'Algerije',         'Jordanië',              'Atlanta',       '2026-06-22 23:00:00'),
('J', 'Argentinië',       'Jordanië',              'Seattle',       '2026-06-28 18:00:00'),
('J', 'Algerije',         'Oostenrijk',            'Kansas City',   '2026-06-28 18:00:00'),
-- Groep K: Portugal, Oezbekistan, Colombia, DR Congo
('K', 'Portugal',         'DR Congo',              'Houston',       '2026-06-17 20:00:00'),
('K', 'Oezbekistan',      'Colombia',              'Los Angeles',   '2026-06-17 23:00:00'),
('K', 'Portugal',         'Oezbekistan',           'Miami',         '2026-06-23 17:00:00'),
('K', 'Colombia',         'DR Congo',              'Dallas',        '2026-06-23 20:00:00'),
('K', 'Portugal',         'Colombia',              'San Francisco', '2026-06-29 22:00:00'),
('K', 'Oezbekistan',      'DR Congo',              'Chicago',       '2026-06-29 22:00:00'),
-- Groep L: Engeland, Kroatië, Ghana, Panama
('L', 'Engeland',         'Kroatië',               'Dallas',        '2026-06-17 17:00:00'),
('L', 'Ghana',            'Panama',                'Atlanta',       '2026-06-17 23:00:00'),
('L', 'Engeland',         'Ghana',                 'Seattle',       '2026-06-23 20:00:00'),
('L', 'Kroatië',          'Panama',                'Los Angeles',   '2026-06-23 23:00:00'),
('L', 'Engeland',         'Panama',                'New York/NJ',   '2026-06-29 18:00:00'),
('L', 'Kroatië',          'Ghana',                 'Miami',         '2026-06-29 18:00:00');

-- ------------------------------------------------------------
-- Voorbeelddata gebruikers (optioneel – verwijder of pas aan)
-- Wachtwoord voor beide testgebruikers is: Welkom123
-- (gegenereerd met: password_hash('Welkom123', PASSWORD_DEFAULT))
-- ------------------------------------------------------------
-- INSERT INTO `users` (`username`, `email`, `password_hash`) VALUES
-- ('alice', 'alice@example.com', '$2y$12$examplehashalice'),
-- ('bob',   'bob@example.com',   '$2y$12$examplehashbob');
