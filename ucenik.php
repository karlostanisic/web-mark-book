<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'header.php';

if (!$logAdmin && !$logRazrednik) {
    destroySession();
    header("Location: http://localhost/WebImenik/index.php");
    die();
}

if (isset($_POST['action'])) {
    
    
    
    
    
    
    
    
    
    
    $action = sanitizeString($_POST['action']);
    $ucenikID = sanitizeString($_POST['ucenikID']);
    $prezime = sanitizeString($_POST['prezime']);
    $ime = sanitizeString($_POST['ime']);
    $adresa = sanitizeString($_POST['adresa']);
    $grad = sanitizeString($_POST['grad']);
    $postBroj = sanitizeString($_POST['postBroj']);
    $tel = sanitizeString($_POST['tel']);
    $email = sanitizeString($_POST['email']);
    $staratelj1Ime = sanitizeString($_POST['staratelj1Ime']);
    $staratelj1Prezime = sanitizeString($_POST['staratelj1Prezime']);
    $staratelj2Ime = sanitizeString($_POST['staratelj2Ime']);
    $staratelj2Prezime = sanitizeString($_POST['staratelj2Prezime']);
    $razredID = sanitizeString($_POST['razred']);
    
    
    
    
    if (isset($_FILES['image']['name'])) {
        $saveto = "images/$ucenikID.jpg";
        move_uploaded_file($_FILES['image']['tmp_name'], $saveto);

        $typeok = TRUE;


        switch ($_FILES['image']['type']) {
            case "image/gif": $src = imagecreatefromgif($saveto); break;
            case "image/jpeg":
            case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
            case "image/png": $src = imagecreatefrompng($saveto); break;
            default: $typeok = FALSE; break;
        }

        if ($typeok) {
            list($w, $h) = getimagesize($saveto);

            $max = 200;
            $tw = $w;
            $th = $h;

            if ($w > $h && $max < $w) {
                $th = $max / $w * $h;
                $tw = $max;
            } elseif ($h > $w && $max < $h) {
                $tw = $max / $h * $w;
                $th = $max;
            } elseif ($max < $w) {
                $tw = $th = $max;
            }

            $tmp = imagecreatetruecolor($tw, $th);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
            imageconvolution($tmp, array(array(-1, -1, -1), array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
            imagejpeg($tmp, $saveto);
            imagedestroy($tmp);
            imagedestroy($src);
        }
    }
    
    
    
    
    switch ($action) {
        case "add": 
            queryMysql("INSERT INTO ucenici (prezime, ime, adresa, grad, postBroj, tel, email, staratelj1Ime, staratelj1Prezime, staratelj2Ime, staratelj2Prezime, razredID) "
                    . "VALUES('$prezime', '$ime', '$adresa', '$grad', '$postBroj', '$tel', '$email', '$staratelj1Ime', '$staratelj1Prezime', '$staratelj2Ime', '$staratelj2Prezime', $razredID)");
            
            break;
        case "edit":
            queryMysql("UPDATE ucenici SET "
                    . "prezime='$prezime', "
                    . "ime='$ime', "
                    . "adresa='$adresa', "
                    . "grad='$grad', "
                    . "postBroj='$postBroj', "
                    . "tel='$tel', "
                    . "email='$email', "
                    . "staratelj1Ime='$staratelj1Ime', "
                    . "staratelj1Prezime='$staratelj1Prezime', "
                    . "staratelj2Ime='$staratelj2Ime', "
                    . "staratelj2Prezime='$staratelj2Prezime', "
                    . "razredID=$razredID "
                    . "WHERE ucenikID='$ucenikID'"); break;
        case "delete": 
            queryMysql("DELETE FROM ucenici WHERE ucenikID='$ucenikID'");
            break;
    }
    
    
    
    
    
    
    
    
    
    
    if ($logRazrednik)
        header("Location: http://localhost/WebImenik/razrednik.php");
    else
        header("Location: http://localhost/WebImenik/ucenici.php");
    die();
}











    

if (isset($_GET['action'])) {
    if ($logRazrednik)
        $action = "edit";
    else
        $action = sanitizeString($_GET['action']);
    
    $ucenikID = $prezime = $ime = $adresa = $grad = $postBroj = $tel = $email = $staratelj1Ime = $staratelj1Prezime = $staratelj2Ime = $staratelj2Prezime = $razredID = "";
    
    if (isset($_GET['ucenikID'])) {
        $ucenikID = sanitizeString($_GET['ucenikID']);
        $ucenik = queryMysql("SELECT * FROM ucenici LEFT JOIN razredi "
                . "ON ucenici.razredID = razredi.razredID "
                . "WHERE ucenikID=$ucenikID");
        if ($ucenik->num_rows == 0) {
            die("Nema ucenika s tim ID.");
        }
        $ucenik = $ucenik->fetch_array(MYSQLI_ASSOC);
        
        $prezime = $ucenik['prezime'];
        $ime = $ucenik['ime'];
        $adresa = $ucenik['adresa'];
        $grad = $ucenik['grad'];
        $postBroj = $ucenik['postBroj'];
        $tel = $ucenik['tel'];
        $email = $ucenik['email'];
        $staratelj1Ime = $ucenik['staratelj1Ime'];
        $staratelj1Prezime = $ucenik['staratelj1Prezime'];
        $staratelj2Ime = $ucenik['staratelj2Ime'];
        $staratelj2Prezime = $ucenik['staratelj2Prezime'];
        $razredID = $ucenik['razredID'];
        
    }
    ?>

<div class="upute">
    <p>Delete, edit or add new student. Type the info, choose his class from 
        drop down menu or, if you want, upload an image of the student (all 
        images are resized on the server side, max width 200px).</p>
</div>

<form name="ucenik" id="ucenik" action="ucenik.php" method="POST" enctype='multipart/form-data' class="aligned-form">
    <fieldset>
        <legend>Ucenik</legend>
        
        <input type='hidden' name='action' id='action' value='<?php echo $action ?>'/>
        <input type='hidden' name='ucenikID' id='ucenikID' value='<?php echo $ucenikID ?>'/>
        
        <div>
            <label for='naziv' >Prezime: </label>
            <input type='text' name='prezime' id='prezime' value='<?php echo $prezime ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Ime: </label>
            <input type='text' name='ime' id='ime' value='<?php echo $ime ?>' maxlength="50" />
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
 
        <div>
            <label for='naziv' >Staratelj, ime: </label>
            <input type='text' name='staratelj1Ime' id='staratelj1Ime' value='<?php echo $staratelj1Ime ?>' maxlength="50" />
       </div>
 
        <div>
            <label for='naziv' >Prezime: </label>
            <input type='text' name='staratelj1Prezime' id='staratelj1Prezime' value='<?php echo $staratelj1Prezime ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='naziv' >Staratelj(2), ime: </label>
            <input type='text' name='staratelj2Ime' id='staratelj2Ime' value='<?php echo $staratelj2Ime ?>' maxlength="50" />
        </div>
 
        <div>
            <label for='naziv' >Prezime: </label>
            <input type='text' name='staratelj2Prezime' id='staratelj2Prezime' value='<?php echo $staratelj2Prezime ?>' maxlength="50" />
        </div>
        
        
        <div>
            <label for='razred' >Razred: </label>
<?php
echo "<select name='razred' id='razred'>";
echo "<option value='0'>--</option>";

$razredi = queryMysql("SELECT * FROM razredi ORDER BY naziv");
$broj = $razredi->num_rows;

for ($j = 0; $j < $broj; ++$j) {
    $razred = $razredi->fetch_array(MYSQLI_ASSOC);
    echo "<option value='" . $razred['razredID'] . "'";
    if ($razred['razredID'] == $razredID)
        echo " selected='selected'";
    echo ">" . $razred['naziv'] . "</option>";
}

?>
            </select>
        </div>
        
        <div>
            
            <label for="slikaProfila">Slika: </label><input type="file" name="image" id="slikaProfila" size="14"><br>
            <?php 
                if (file_exists("images/$ucenikID.jpg")) $slikaPath = "images/$ucenikID.jpg";
                        else $slikaPath = "images/dummy.jpg";
            ?>
            <img src="<?php echo $slikaPath ?>" style="margin: 5px 15% 0 16%;">
        </div>
        
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
    document.getElementById("ucenici").className += " current";
</script>

    </body>
</html>
