<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'header.php';

if (!$logAdmin) {
    destroySession();
    header("Location: http://localhost/WebImenik/index.php");
    die();
}

if (isset($_POST['action'])) {
    $action = sanitizeString($_POST['action']);
    $profesorID = sanitizeString($_POST['profesorID']);
    $prezime = sanitizeString($_POST['prezime']);
    $ime = sanitizeString($_POST['ime']);
    $titula = sanitizeString($_POST['titula']);
    $adresa = sanitizeString($_POST['adresa']);
    $grad = sanitizeString($_POST['grad']);
    $postBroj = sanitizeString($_POST['postBroj']);
    $tel = sanitizeString($_POST['tel']);
    $email = sanitizeString($_POST['email']);
    
    $odabraniPredmeti = $_POST['predmet'];
    
   
    
//    if ($action == "add" || $action == "edit") {
//        $predmeti = queryMysql("SELECT * FROM predmeti");
//        $broj = $predmeti->num_rows;
//        
//        for ($j = 0; $j < $broj; ++$j) {
//            $predmet = $predmeti->fetch_array(MYSQLI_ASSOC);
//            if (isset($_POST['predmet' . $predmet['predmetID']])) {
//                $profesorPredaje = queryMysql("SELECT * FROM profesori_predmeti WHERE profesorID='$profesorID' AND predmetID='" . $predmet['predmetID'] . "'");
//                if ($profesorPredaje->num_rows == 0)
//                    queryMysql ("INSERT INTO profesori_predmeti (profesorID, predmetID) VALUES('$profesorID', '" . $predmet['predmetID'] . "')");
//            } else {
//                queryMysql("DELETE FROM profesori_predmeti WHERE profesorID='$profesorID' AND predmetID='" . $predmet['predmetID'] . "'");
//            }
//        }
//    }
    
    switch ($action) {
        case "add": 
            queryMysql("INSERT INTO profesori (prezime, ime, titula, adresa, grad, postBroj, tel, email) "
                    . "VALUES('$prezime', '$ime', '$titula', '$adresa', '$grad', '$postBroj', '$tel', '$email')");
//            $maxID = queryMysql("SELECT profesorID FROM profesori ORDER BY profesorID DESC LIMIT 1");
//            $maxID = $maxID->fetch_array(MYSQLI_ASSOC);
            $profesorID = queryMysql("SELECT profesorID FROM profesori ORDER BY profesorID DESC LIMIT 1")->fetch_array(MYSQLI_ASSOC)['profesorID'];
            break;
        case "edit":
            queryMysql("UPDATE profesori SET "
                    . "prezime='$prezime', "
                    . "ime='$ime', "
                    . "titula='$titula', "
                    . "adresa='$adresa', "
                    . "grad='$grad', "
                    . "postBroj='$postBroj', "
                    . "tel='$tel', "
                    . "email='$email' "
                    . "WHERE profesorID='$profesorID'"); break;
        case "delete": 
            queryMysql("DELETE FROM profesori WHERE profesorID='$profesorID'"); 
            //queryMysql("DELETE FROM profesori_predmeti WHERE profesorID='$profesorID'");
            break;
    }
    
//    if (!(empty($odabraniPredmeti))) {
        if ($action == "add" || $action == "edit") {
            
            queryMysql("DELETE FROM profesori_predmeti WHERE profesorID=$profesorID");
            
            $broj = count($odabraniPredmeti);
            for ($j = 0; $j < $broj; ++$j) {
//                $profesorPredaje = queryMysql("SELECT * FROM profesori_predmeti "
//                        . "WHERE profesorID=$profesorID AND predmetID=" . $odabraniPredmeti[$j]);
//                if ($profesorPredaje->num_rows == 0)
                    queryMysql ("INSERT INTO profesori_predmeti (profesorID, predmetID) VALUES($profesorID, $odabraniPredmeti[$j])");
            }
        }
//    }
    
    
    
    header("Location: http://localhost/WebImenik/profesori.php");
    die();
}
    

if (isset($_GET['action'])) {
    $action = sanitizeString($_GET['action']);
    
    $profesorID = $prezime = $ime = $titula = $adresa = $grad = $postBroj = $tel = $email = "";
    
    if (isset($_GET['profesorID'])) {
        $profesorID = sanitizeString($_GET['profesorID']);
        $profesor = queryMysql("SELECT * FROM profesori WHERE profesorID=$profesorID");
        if ($profesor->num_rows == 0) {
            die("Nema profesora s tim ID.");
        }
        $profesor = $profesor->fetch_array(MYSQLI_ASSOC);
        $prezime = sanitizeString($profesor['prezime']);
        
        $ime = sanitizeString($profesor['ime']);
        $titula = sanitizeString($profesor['titula']);
        $adresa = sanitizeString($profesor['adresa']);
        $grad = sanitizeString($profesor['grad']);
        $postBroj = sanitizeString($profesor['postBroj']);
        $tel = sanitizeString($profesor['tel']);
        $email = sanitizeString($profesor['email']);
        
        $profesorPredmeti = queryMysql("SELECT * FROM profesori_predmeti WHERE profesorID=$profesorID");
        $broj = $profesorPredmeti->num_rows;
        for ($j = 0; $j < $broj; ++$j) {
            $profesorPredmet = $profesorPredmeti->fetch_array(MYSQLI_ASSOC);
            $popisPredmeta[$profesorPredmet['predmetID']] = TRUE;
        }
    }
    ?>

<div class="upute">
    <p>On this page, depending on the action you chose, you can edit, delete or add informations about teachers. At the bottom of the form you are given a list of all school’s subjects so you can choose which subjects are taught by that teacher.</p>
</div>

<form name="profesor" id="profesor" action="profesor.php" method="POST" class="aligned-form">
    <fieldset>
        <legend>Profesor</legend>
        
        <input type='hidden' name='action' id='action' value='<?php echo $action ?>'/>
        <input type='hidden' name='profesorID' id='profesorID' value='<?php echo $profesorID ?>'/>
        
        <div>
            <label for='naziv' >Prezime: </label>
            <input type='text' name='prezime' id='prezime' value='<?php echo $prezime ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Ime: </label>
            <input type='text' name='ime' id='ime' value='<?php echo $ime ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Titula: </label>
            <input type='text' name='titula' id='titula' value='<?php echo $titula ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Adresa: </label>
            <input type='text' name='adresa' id='adresa' value='<?php echo $adresa ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Grad: </label>
            <input type='text' name='grad' id='grad' value='<?php echo $grad ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Postanski br.: </label>
            <input type='text' name='postBroj' id='postBroj' value='<?php echo $postBroj ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Telefon: </label>
            <input type='text' name='tel' id='tel' value='<?php echo $tel ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Email: </label>
            <input type='text' name='email' id='email' value='<?php echo $email ?>' maxlength="50" />
        </div>
        
        <fieldset>
            <legend>Predmeti</legend>
        
<?php

$predmeti = queryMysql("SELECT * FROM predmeti");
$broj = $predmeti->num_rows;

for ($j = 0; $j < $broj; ++$j) {
    $predmet = $predmeti->fetch_array(MYSQLI_ASSOC);
    echo "<div>";
    echo "<label for='" . $predmet['predmetID'] . "' >" . $predmet['naziv'] . "</label>";
    echo "<input type='checkbox' name='predmet[]' id='" . $predmet['predmetID'] . "' value='" . $predmet['predmetID'] . "'";
    if (isset($popisPredmeta[$predmet['predmetID']]))  
        echo " checked='checked'";
    echo ">";
    
    echo "</div>";
}

?>
        </fieldset>
        
        <div>
<?php
    switch ($action) {
        case "add":
            $submitButton = "Dodaj";
            break;
        case "edit":
            $submitButton = "Promijeni"; 
            break;
        case "delete": 
            $submitButton = "Ukloni";
            break;
        default:
            $submitButton = "U redu";
    }
?>
            <input type='submit' name='Submit' value='<?php echo $submitButton ?>' />
            <a href="javascript:history.go(-1)" class='novi'>ODUSTANI</a>
        </div>
    </fieldset>
</form>


<?php
    
}

?>

<script>
    document.getElementById("profesori").className += " current";
</script>

    </body>
</html>
