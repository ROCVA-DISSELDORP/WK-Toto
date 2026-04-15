<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Taak student – Uitloggen implementeren
// 1. Verwijder alle sessievariabelen met session_unset().
// 2. Vernietig de sessie volledig met session_destroy().
// 3. Stuur de gebruiker door naar de homepage (index.php)
//    met behulp van header('Location: ...').
// Vergeet exit niet na de header()-aanroep!
// ============================================================
