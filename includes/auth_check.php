<?php

// ============================================================
// auth_check.php – Sessiebewaking
// Taak student: Voeg bovenaan elke beveiligde pagina het volgende toe:
//   require_once __DIR__ . '/../includes/auth_check.php';
// Dit zorgt ervoor dat niet-ingelogde gebruikers worden doorgestuurd
// naar de loginpagina.
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

if (!checkLogin()) {
    header('Location: login.php');
    exit;
}
