<?php

session_start();

include_once './includes/config.php';
include_once './includes/functions.php';

if (isset($_SESSION['logKorisnik'])) {
    $logKorisnik = $_SESSION['logKorisnik'];
    $logKorisnikID = $_SESSION['logKorisnikID'];
    
    if (isset($_SESSION['logAdmin'])) 
        $logAdmin = TRUE;
    else
        $logAdmin = FALSE;
    
    if (isset($_SESSION['logRazrednik'])) 
        $logRazrednik = $_SESSION['logRazrednik'];
    else
        $logRazrednik = FALSE;
    
    if (isset($_SESSION['logStaratelj'])) 
        $logStaratelj = $_SESSION['logStaratelj'];
    else
        $logStaratelj = FALSE;
    
    $loggedin = TRUE;
} 
else {
    //$loggedin = FALSE;
    destroySession();
    header("Location: http://localhost/WebImenik/index.php");
    die();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Web imenik</title>
        <link href="./css/normalize.css" rel="stylesheet" type="text/css">
        <link href="./css/reset.css.css" rel="stylesheet" type="text/css">
        <link href="./css/styles.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        
        <nav>
<?php
if ($logAdmin) {
?>
            <a href="korisnici.php" id="korisnici">Korisnici</a>
            <a href="predmeti.php" id="predmeti">Predmeti</a>
            <a href="profesori.php" id="profesori">Profesori</a>
            <a href="ucenici.php" id="ucenici">Ucenici</a>
            <a href="razredi.php" id="razredi">Razredi</a>
            <?php
}
?>
            <a href="logout.php">Odlogiraj se</a>
        </nav>
        
        <div id="container">