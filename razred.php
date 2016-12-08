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
    $razredID = sanitizeString($_POST['razredID']);
    $naziv = sanitizeString($_POST['naziv']);
    $razrednikID = sanitizeString($_POST['razrednik']);
    switch ($action) {
        case "add": 
            queryMysql("INSERT INTO razredi (naziv, razrednikID) VALUES('$naziv', $razrednikID)"); break;
        case "edit":
            queryMysql("UPDATE razredi SET naziv='$naziv', razrednikID=$razrednikID WHERE razredID=$razredID"); break;
        case "delete": 
            queryMysql("DELETE FROM razredi WHERE razredID=$razredID"); break;
    }
    header("Location: http://localhost/WebImenik/razredi.php");
    die();
}
    

if (isset($_GET['action'])) {
    $action = sanitizeString($_GET['action']);
    
    $razredID = $naziv = $razrednikID = "";
    
    if (isset($_GET['razredID'])) {
        $razredID = sanitizeString($_GET['razredID']);
        $razred = queryMysql("SELECT razredi.naziv, profesori.profesorID, profesori.ime, profesori.prezime "
                . "FROM razredi LEFT JOIN profesori ON razredi.razrednikID = profesori.profesorID "
                . "WHERE razredi.razredID = $razredID "
                . "ORDER BY razredi.naziv");
        if ($razred->num_rows == 0) {
            die("Nema razreda s tim ID.");
        }
        $razred = $razred->fetch_array(MYSQLI_ASSOC);
        $naziv = $razred['naziv'];
        $razrednikID = $razred['profesorID'];
    }
    ?>


<div class="upute">
    <p>On this page you can choose class’ name and head teacher from the drop down menu.</p>
</div>

<form name="razred" id="razred" action="razred.php" method="POST">
    <fieldset>
        <legend>Razred</legend>
        
        <input type='hidden' name='action' id='action' value='<?php echo $action ?>'/>
        <input type='hidden' name='razredID' id='razredID' value='<?php echo $razredID ?>'/>
        
        <div>
            <label for='naziv' >Naziv: </label><br/>
            <input type='text' name='naziv' id='naziv' value='<?php echo $naziv ?>' maxlength="50" /><br/>
        </div>
        
        <div>
            <label for='razrednik' >Razrednik: </label><br/>
            <select name="razrednik" id="razrednik">
                <option value="0">--</option>
<?php

$profesori = queryMysql("SELECT * FROM profesori ORDER BY prezime, ime");
$broj = $profesori->num_rows;

for ($j = 0; $j < $broj; ++$j) {
    $profesor = $profesori->fetch_array(MYSQLI_ASSOC);
    echo "<option value='" . $profesor['profesorID'] . "'";
    if ($profesor['profesorID'] == $razrednikID)
        echo " selected='selected'";
    echo ">" . $profesor['prezime'] . " " . $profesor['ime'] . "</option>";
}

?>
            </select>
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
    document.getElementById("razredi").className += " current";
</script>

    </body>
</html>

