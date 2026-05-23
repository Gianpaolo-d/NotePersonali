<?php
/*
================================================================================
 LOGOUT (IT / EN)
--------------------------------------------------------------------------------
DESCRIZIONE (IT)
- Distrugge la sessione corrente.
- Reindirizza l’utente alla pagina di login.

DESCRIPTION (EN)
- Destroys the current session.
- Redirects the user to the login page.
================================================================================
*/

session_start();

session_destroy();
header("Location: login.php");

?>