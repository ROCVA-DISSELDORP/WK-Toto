<?php
// Sessie starten als die nog niet actief is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// BASE_URL is gedefinieerd in config/db.php (ingesloten vóór deze partial)
?><!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | WK-Toto' : 'WK-Toto' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
