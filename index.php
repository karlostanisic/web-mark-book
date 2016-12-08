<?php

//echo password_hash('admin', PASSWORD_DEFAULT) . "++";

session_start();

include_once './includes/config.php';
include_once './includes/functions.php';

$error = $korisnickoIme = $lozinka = "";

if (isset($_POST['korisnickoime'])) {
    $korisnickoIme = sanitizeString($_POST['korisnickoime']);
    $lozinka = sanitizeString($_POST['lozinka']);
    
    if ($korisnickoIme == "" || $lozinka == "")
        $error = "Nisu uneseni svi podaci";
    else {
        $result = queryMysql("SELECT * FROM korisnici "
                . "WHERE korisnickoIme='$korisnickoIme'");
        
        if ($result->num_rows == 0) {
            $error = "Korisnicki podaci nisu ispravni";
        }
        else {
            $korisnik = $result->fetch_array(MYSQLI_ASSOC);

            $hash = $korisnik['lozinka'];
            
            if (password_verify($lozinka, $hash)) {
                $_SESSION['logKorisnik'] = $korisnik['korisnickoIme'];
                
                $_SESSION['logKorisnikID'] = $korisnik['korisnikID'];
                
                if ($korisnik['admin'] == 1) {
                    $_SESSION['logAdmin'] = TRUE;
                    header("Location: http://localhost/WebImenik/korisnici.php");
                    die();
                } elseif ($korisnik['razredID']) {
                    $_SESSION['logRazrednik'] = $korisnik['razredID'];
                    header("Location: http://localhost/WebImenik/razrednik.php");
                    die();
                } elseif ($korisnik['ucenikID']) {
                    $_SESSION['logStaratelj'] = $korisnik['ucenikID'];
                    header("Location: http://localhost/WebImenik/ocjene.php");
                    die();
                } else {
                    destroySession();
                    $error = "Nemate ovlasti";
                }
            } else {
                $error = "Korisnicki podaci nisu ispravni";
            }
        }        
    }
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
        <div id="container">
            <h2>Dobrodosli na Web imenik</h2>
            <div class="upute">
                <p>There are 3 levels of user authorization: administrator, head teacher and parent. 
                    Depending on the level of authorization, you have more or less options. 
                    To log in as an administrator use username: <strong>administrator</strong>, password: <strong>admin</strong>.</p>
            </div>
        <form id='login' action='index.php' method='post'>
        <fieldset >
        <legend>Login</legend>

        <div><span class='error'><?php echo $error ?></span></div>
        <div class='container'>
            <label for='korisnickoime' >Korisnicko ime:</label><br/>
            <input type='text' name='korisnickoime' id='korisnickoime' value='<?php echo $korisnickoIme ?>' maxlength="50" /><br/>
        </div>
        <div class='container'>
            <label for='lozinka' >Lozinka:</label><br/>
            <input type='password' name='lozinka' id='lozinka' maxlength="50" /><br/>
        </div>

        <div class='container'>
            <input type='submit' name='submit' value='Logiraj se' />
        </div>
        </fieldset>
        </form>
        </div>
    </body>
</html>
