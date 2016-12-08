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
    $predmetID = sanitizeString($_POST['predmetID']);
    $naziv = sanitizeString($_POST['naziv']);
    switch ($action) {
        case "add": 
            queryMysql("INSERT INTO predmeti (naziv) VALUES('$naziv')"); break;
        case "edit":
            queryMysql("UPDATE predmeti SET naziv='$naziv' WHERE predmetID='$predmetID'"); break;
        case "delete": 
            queryMysql("DELETE FROM predmeti WHERE predmetID='$predmetID'"); break;
    }
    header("Location: http://localhost/WebImenik/predmeti.php");
    die();
}
    

if (isset($_GET['action'])) {
    $action = sanitizeString($_GET['action']);
    
    $predmetID = $naziv = "";
    
    if (isset($_GET['predmetID'])) {
        $predmetID = sanitizeString($_GET['predmetID']);
        $predmet = queryMysql("SELECT * FROM predmeti WHERE predmetID=$predmetID");
        if ($predmet->num_rows == 0) {
            die("Nema predmeta s tim ID.");
        }
        $predmet = $predmet->fetch_array(MYSQLI_ASSOC);
        $naziv = $predmet['naziv'];
    }
    ?>


<div class="upute">
    <p>Delete, edit or add new subject.</p>
</div>


<form name="predmet" id="predmet" action="predmet.php" method="POST">
    <fieldset>
        <legend>Predmet</legend>
        
        <input type='hidden' name='action' id='action' value='<?php echo $action ?>'/>
        <input type='hidden' name='predmetID' id='predmetID' value='<?php echo $predmetID ?>'/>
        
        <div>
            <label for='naziv' >Naziv: </label><br/>
            <input type='text' name='naziv' id='naziv' value='<?php echo $naziv ?>' maxlength="50" /><br/>
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
    document.getElementById("predmeti").className += " current";
</script>
    </body>
</html><?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

