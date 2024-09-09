<?php
// Start session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect back to the previous page
if (isset($_SERVER['HTTP_REFERER'])) {
    $previous_page = $_SERVER['HTTP_REFERER'];
    header("Location: $previous_page");
} else {
    // If no previous page, redirect to a default page
    header("Location: index.php");
}
exit;

//guten Tag Frau duc. Ich hoffe Sie haben von all dem schlechten code noch keine Kopfschmerzen desshalb hier eine erwas kurze datei