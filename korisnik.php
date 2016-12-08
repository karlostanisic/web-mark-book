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
    $korisnikID = sanitizeString($_POST['korisnikID']);
    $korisnickoIme = sanitizeString($_POST['korisnickoIme']);
    $lozinka = sanitizeString($_POST['lozinka']);
    $ucenikID = sanitizeString($_POST['ucenikID']);
    $razredID = sanitizeString($_POST['razredID']);
    if (isset($_POST['admin'])) 
        $admin = 1;
    else
        $admin = 0;
    
    $hash = password_hash($lozinka, PASSWORD_DEFAULT);
    
    switch ($action) {
        case "add":
            queryMysql("INSERT INTO korisnici (korisnickoIme, lozinka, ucenikID, razredID, admin) "
                    . "VALUES('$korisnickoIme', '$hash', $ucenikID, $razredID, $admin)");
            
            break;
        case "edit":
            queryMysql("UPDATE korisnici SET "
                    . "korisnickoIme='$korisnickoIme', "
                    . "ucenikID=$ucenikID, "
                    . "razredID=$razredID, "
                    . "admin=$admin "
                   . "WHERE korisnikID=$korisnikID");
            if ($lozinka !== "")
                queryMysql("UPDATE korisnici SET lozinka='$hash' WHERE korisnikID=$korisnikID"); 
            break;
        case "delete": 
            queryMysql("DELETE FROM korisnici WHERE korisnikID='$korisnikID'");
            break;
    }
    header("Location: http://localhost/WebImenik/korisnici.php");
    die();
}
    

if (isset($_GET['action'])) {
    $action = sanitizeString($_GET['action']);
    
    $korisnikID = $korisnickoIme = $ucenikID = $razredID = $admin = $lozinka = "";
    
    if (isset($_GET['razredID']))
            $razredID = sanitizeString ($_GET['razredID']);
    
    if (isset($_GET['ucenikID']))
            $ucenikID = sanitizeString ($_GET['ucenikID']);
    
    if ($action == "add") {
        $lozinka = generatePassword();
        if ($razredID !== "") {
            $razrednik = queryMysql("SELECT profesori.ime, profesori.prezime "
                    . "FROM razredi INNER JOIN profesori ON razredi.razrednikID = profesori.profesorID "
                    . "WHERE razredi.razredID = $razredID");
            if ($razrednik->num_rows > 0) {
                $razrednik = $razrednik->fetch_array(MYSQLI_ASSOC);
                $korisnickoIme = strtolower($razrednik['ime'] . "." . $razrednik['prezime']);
                $korisnickoIme = preg_replace('/\s+/', '', $korisnickoIme);
            }
        }
        if ($ucenikID !== "") {
            $ucenik = queryMysql("SELECT ime, prezime "
                    . "FROM ucenici "
                    . "WHERE ucenikID = $ucenikID");
            if ($ucenik->num_rows > 0) {
                $ucenik = $ucenik->fetch_array(MYSQLI_ASSOC);
                $korisnickoIme = $ucenik['ime'] . "." . $ucenik['prezime'];
                $korisnickoIme = preg_replace('/\s+/', '', $korisnickoIme);
                $korisnickoIme = strtolower($korisnickoIme);
            }
        }
    }
        
    
    
    if (isset($_GET['korisnikID'])) {
        $korisnikID = sanitizeString($_GET['korisnikID']);
        
        $korisnik = queryMysql("SELECT korisnici.korisnickoIme, razredi.razredID, razredi.naziv, ucenici.ucenikID, ucenici.ime, ucenici.prezime, korisnici.admin "
                . "FROM korisnici LEFT JOIN razredi ON korisnici.razredID = razredi.razredID "
                . "LEFT JOIN ucenici ON korisnici.ucenikID = ucenici.ucenikID "
                . "WHERE korisnici.korisnikID=$korisnikID");
        if ($korisnik->num_rows == 0) {
            die("Nema korisnika s tim ID.");
        }
        $korisnik = $korisnik->fetch_array(MYSQLI_ASSOC);
        
        $korisnickoIme = $korisnik['korisnickoIme'];
        $razredID = $korisnik['razredID'];
        $nazivRazreda = $korisnik['naziv'];
        $ucenikID = $korisnik['ucenikID'];
        $ucenikIme = $korisnik['ime'];
        $ucenikPrezime = $korisnik['prezime'];
        $admin = $korisnik['admin'];
        
    }
    ?>

<div class="upute">
    <p>On this page, depending on the action you chose, you can edit, delete or add informations about user. If you chose to
    add new user, password is automatically generated. Depending on what authorisation you want to give to the user, you choose pupil's
    name for parents, class name for head teachers from the drop down menus or tick Admin for administrator account.</p>
</div>

<form name="korisnik" id="korisnik" action="korisnik.php" method="POST" class="aligned-form">
    <fieldset>
        <legend>Korisnik</legend>
        
        <input type='hidden' name='action' id='action' value='<?php echo $action ?>'/>
        <input type='hidden' name='korisnikID' id='korisnikID' value='<?php echo $korisnikID ?>'/>
        
        <div>
            <label for='naziv' >Korisnicko ime:</label>
            <input type='text' name='korisnickoIme' id='korisnickoIme' value='<?php echo $korisnickoIme ?>' maxlength="50" onBlur='checkUser(this)'/>
            
        </div>
        <div>
        <div id='info'></div>
        </div>
        
        <div>
            <label for="lozinka">Lozinka:</label>
            <input type="text" name="lozinka" id="lozinka" value='<?php echo $lozinka ?>' maxlength="50" />
        </div>
        
        <div>
            <label for='ucenikID' >Ucenik:</label>
            <select name="ucenikID" id="ucenikID">
                <option value="0">--</option>
<?php

$ucenici = queryMysql("SELECT * FROM ucenici ORDER BY prezime, ime");
$broj = $ucenici->num_rows;

for ($j = 0; $j < $broj; ++$j) {
    $ucenik = $ucenici->fetch_array(MYSQLI_ASSOC);
    echo "<option value='" . $ucenik['ucenikID'] . "'";
    if ($ucenik['ucenikID'] == $ucenikID)
        echo " selected='selected'";
    echo ">" . $ucenik['prezime'] . " " . $ucenik['ime'] . "</option>";
}

?>
            </select>
        </div>
        
        <div>
            <label for='razredID' >Razred:</label>
            <select name="razredID" id="razredID">
                <option value="0">--</option>
<?php

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
            <label for="admin">Admin:</label>
<?php

            echo "<input type='checkbox' name='admin' id='admin'";
            if ($admin == 1) echo " checked='checked'";
            echo ">";

?>
            
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

<script type="text/javascript">
    function O(obj)
            {
              if (typeof obj == 'object') return obj
              else return document.getElementById(obj)
            }

    function checkUser(korisnickoIme) {
        if (korisnickoIme.value === '') {
           O('info').innerHTML = '';
           return;
        }

        params = "korisnickoIme=" + korisnickoIme.value;
        request = new ajaxRequest();
        request.open("POST", "provjeri-korisnika.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.setRequestHeader("Connection", "close");
        request.setRequestHeader("Content-length", params.length);

        request.onreadystatechange = function() {
            if (this.readyState == 4)
                if (this.status == 200)
                    if (this.responseText != null){

                        O('info').innerHTML = this.responseText;

                    }
        }
        request.send(params);    
    }

    function ajaxRequest() {
        try { var request = new XMLHttpRequest() }
        catch(e1) {
            try {request = new ActiveXObject("Msxml2.XMLHTTP")}
            catch(e2) {
                try {request = new ActiveXObject("Microsoft.XMLHTTP")}
                catch(e3) {
                    request = false;
                }
            }
        }
        return request;
    }
</script>


<?php
    
}

?>

<script>
    document.getElementById("korisnici").className += " current";
</script>

    </body>
</html>
