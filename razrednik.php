<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'header.php';


if (!($logAdmin || $logRazrednik)) {
    destroySession();
    header("Location: http://localhost/WebImenik/index.php");
    die();
}

if ($logAdmin && isset($_GET['razredID'])) 
    $razredID = sanitizeString($_GET['razredID']);
    elseif ($logRazrednik) {
        $razredID = $logRazrednik;
    }
 elseif (!$_SERVER['REQUEST_METHOD'] == 'POST') {
    destroySession();
    header("Location: http://localhost/WebImenik/index.php");
    die();
 }


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $razredID = sanitizeString($_POST['razredID']);
    
//    $razrediPredmetiProfesori = queryMysql("SELECT ID FROM razredi_predmeti_profesori WHERE razredID = $razredID");
//    $broj = $razrediPredmetiProfesori->num_rows;
//    for ($j = 0; $j < $broj; ++$j){
//        $razredPredmetProfesorID = $razrediPredmetiProfesori->fetch_array(MYSQLI_ASSOC);
//        queryMysql("DELETE FROM razredi_predmeti_rubrike WHERE razredPredmetProfesorID = " . $razredPredmetProfesorID['ID']);
//    }
    
            

    
    
    if (isset($_POST['predmet'])) {
        $odabraniPredmeti = $_POST['predmet'];
        foreach ($odabraniPredmeti as $key => $value) {
            $odabraniPredmeti[$key] = sanitizeString($value);
        }
    } else {
        $odabraniPredmeti = array();
    }
    
    if (isset($_POST['predmetProfesor'])){
        $predmetiProfesori = $_POST['predmetProfesor'];
        foreach ($predmetiProfesori as $key => $value) {
            if($value == 0) {
                $predmetiProfesori[$key] = "NULL";
            } else {
                $predmetiProfesori[$key] = sanitizeString($predmetiProfesori[$key]);
            }    
        }
    } else {
        $predmetiProfesori = array();
    }
    
    if (isset($_POST['predmetRubrika'])) {
        $rubrike = $_POST['predmetRubrika'];
        for($i = 0; $i < count($rubrike); $i++){
            for($j = 0; $j < count($rubrike[$i]); $j++){
                    $rubrike[$i][$j] = sanitizeString($rubrike[$i][$j]);
            }
        }
    } else {
        $rubrike = array();
    }
    
    
    
//   print_r($odabraniPredmeti);
//    print_r($predmetiProfesori);
//    print_r($rubrike);
    
    //queryMysql("DELETE FROM razredi_predmeti_rubrike WHERE razredID = $razredID");
    queryMysql("DELETE FROM razredi_predmeti WHERE razredID = $razredID");
    
//    $broj = ;
    
//    for ($j = 0; $j < $broj; ++$j) {
    foreach ($odabraniPredmeti as $key => $value) {



//        if (!(isset($predmetiProfesori[$j])) || $predmetiProfesori[$j] == 0) $predmetiProfesori[$j] = "NULL";
//        if (!(isset($odabraniPredmeti[$j])) || $odabraniPredmeti[$j] == 0) $odabraniPredmeti[$j] = "NULL";
       // if (isset($odabraniPredmeti[$j])) {
        queryMysql("INSERT INTO razredi_predmeti (razredID, predmetID, profesorID, rubrika0, rubrika1, rubrika2, rubrika3, rubrika4) "
            . "VALUES ($razredID, " . $odabraniPredmeti[$key] . ", " . $predmetiProfesori[$key] . ", '" . $rubrike[$key][0] . "', '" . $rubrike[$key][1] . "', '" . $rubrike[$key][2] . "', '" . $rubrike[$key][3] . "', '" . $rubrike[$key][4] . "')");
        //}
//        $razredPredmetProfesorID = queryMysql("SELECT ID FROM razredi_predmeti_profesori ORDER BY ID DESC LIMIT 1")->fetch_array(MYSQLI_ASSOC)['ID'];
//        for ($i = 0; $i < 5; ++$i){
//            if($rubrike[$j][$i] !== ""){
//                $rubrika = sanitizeString($rubrike[$j][$i]);
//                queryMysql("INSERT INTO razredi_predmeti_rubrike (razredPredmetProfesorID, rubrika)"
//                        . "VALUES ($razredPredmetProfesorID, '$rubrika')");
//            }
//        }
    }
//    for ($j = 0; $j < $broj; ++$j) {
//        $predmet = $predmeti->fetch_array(MYSQLI_ASSOC);
//        if (isset($_POST['predmet[' . $j . ']']) && sanitizeString($_POST['predmet[' . $j . ']']) == ) {
//            
//        }
//    }
}

$nazivRazreda = queryMysql("SELECT naziv FROM razredi WHERE razredID = $razredID");
$nazivRazreda = $nazivRazreda->fetch_array(MYSQLI_ASSOC);
$nazivRazreda = $nazivRazreda['naziv'];

$ucenici = queryMysql("SELECT ucenikID, ime, prezime, zadnjiUpis FROM ucenici WHERE razredID = $razredID ORDER BY prezime, ime");
$broj = $ucenici->num_rows;


?>

<h2><?php echo $nazivRazreda ?> razred</h2>

<div class="upute">
    <p>This page is for head teachers of classes. Here, they can edit 
        informations about their students (‘Uredi podatke’), update their marks 
        (‘Upisi ocjene’) or manage subjects that are taught to their class by 
        clicking ‘Uredi predmete’ button. 
    <p>In this form you can choose subject’s teacher from drop down menus and 
        input different subject’s categories, like oral or written exams, 
        understanding, student’s activity etc.</p>
</div>

<table border="1">
    <tr>
        <th>Prezime</th>
        <th>Ime</th>
        <th>Podaci</th>
        <th>Ocjene</th>
        <th>Zadnji unos</th>
    </tr>
<?php

for ($j = 0; $j < $broj; ++$j) {
    $ucenik = $ucenici->fetch_array(MYSQLI_ASSOC);
    echo "<tr>";
    echo "<td>" . $ucenik['prezime'] . "</td>";
    echo "<td>" . $ucenik['ime'] . "</td>";
    echo "<td><a href='ucenik.php?action=edit&prevPage=razrednik&ucenikID=" . $ucenik['ucenikID'] . "'>Uredi podatke</a></td>";
    echo "<td><a href='upis-ocjena.php?ucenikID=" . $ucenik['ucenikID'] . "'>Upisi ocjene</a></td>";
    echo "<td>" . $ucenik['zadnjiUpis'] . "</td>";
    echo "</tr>";
}

?>
</table>

<button type="button" onclick="toogleVisibility('predmetiedit', this)">Uredi predmete</button>

<form id="predmetiedit" name="predmetiedit" action="razrednik.php" method="post" style="display: none;">
    <input type="hidden" name="razredID" id="razredID" value="<?php echo $razredID ?>">
    <table>
        <tr>
            <th>
                Predmet
            </th>
            <th>
                Profesor
            </th>
            <th>
                Rubrike
            </th>
        </tr>
<?php
//$predmeti = queryMysql("SELECT predmeti.predmetID, predmeti.naziv, razredi_predmeti_profesori.predmetID as predmetIDIde, razredi_predmeti_profesori.profesorID, profesori.prezime, profesori.ime "
//        . "FROM predmeti LEFT JOIN razredi_predmeti_profesori ON predmeti.predmetID = razredi_predmeti_profesori.predmetID "
//        . "LEFT JOIN profesori ON razredi_predmeti_profesori.profesorID = profesori.profesorID "
//        . "WHERE razredi_predmeti_profesori.razredID IS NULL OR razredi_predmeti_profesori.razredID = $razredID "
//        . "ORDER BY predmeti.predmetID");

//$predmeti = queryMysql("SELECT predmeti.predmetID, predmeti.naziv, razredi_predmeti.predmetID as predmetIDIde, "
//        . "razredi_predmeti.profesorID, razredi_predmeti.rubrika0, razredi_predmeti.rubrika1, razredi_predmeti.rubrika2, "
//        . "razredi_predmeti.rubrika3, razredi_predmeti.rubrika4 "
//        . "FROM predmeti LEFT JOIN razredi_predmeti ON predmeti.predmetID = razredi_predmeti.predmetID "
//        . "WHERE razredi_predmeti.razredID IS NULL OR razredi_predmeti.razredID = $razredID "
//        . "ORDER BY predmeti.predmetID");

$predmeti = queryMysql("SELECT predmeti.predmetID, predmeti.naziv, banana.predmetID as predmetIDIde, "
        . "banana.profesorID, banana.rubrika0, banana.rubrika1, banana.rubrika2, "
        . "banana.rubrika3, banana.rubrika4 "
        . "FROM predmeti LEFT JOIN (SELECT * FROM razredi_predmeti WHERE razredi_predmeti.razredID = $razredID) as banana "
        . "ON predmeti.predmetID = banana.predmetID "
        . "ORDER BY predmeti.naziv");
$broj = $predmeti->num_rows;

//SELECT predmeti.naziv, predmeti.predmetID, banana.predmetID, banana.profesorID, banana.razredID FROM predmeti LEFT JOIN (SELECT * FROM razredi_predmeti WHERE razredi_predmeti.razredID = 2) as banana ON predmeti.predmetID = banana.predmetID WHERE banana.razredID = 2 OR banana.razredID is null 

for ($j = 0; $j < $broj; ++$j){
    $predmet = $predmeti->fetch_array(MYSQLI_ASSOC);
    
    echo "<tr><td>\r\n";
    echo "<input type='checkbox' name='predmet[" . $j . "]' id='predmet" . $predmet['predmetID'] . "' value='" . $predmet['predmetID'] . "'";
    if ($predmet['predmetIDIde'])        echo " checked='checked'";
    echo ">";
    echo "<label for='predmet" . $predmet['predmetID'] . "' > " . $predmet['naziv'] . "</label></td>\r\n";
    echo "<td>\r\n";
    
    $profesori = queryMysql("SELECT profesori.profesorID, profesori.ime, profesori.prezime "
            . "FROM profesori_predmeti INNER JOIN profesori ON profesori.profesorID = profesori_predmeti.profesorID "
            . "WHERE profesori_predmeti.predmetID = " . $predmet['predmetID'] . " "
            . "ORDER BY prezime, ime");
    echo "<select name='predmetProfesor[" . $j . "]' id='predmetProfesor" . $predmet['predmetID'] . "'>\r\n";
    echo "<option value='0'>--</option>\r\n";
    $brojProfesora = $profesori->num_rows;
    for ($i = 0; $i < $brojProfesora; ++$i) {
        $profesor = $profesori->fetch_array(MYSQLI_ASSOC);
        echo "<option value='" . $profesor['profesorID'] . "'";
        if ($profesor['profesorID'] == $predmet['profesorID']) echo " selected='selected'";
        echo ">" . $profesor['prezime'] . " " . $profesor['ime'] . "</option>\r\n";
    }
    echo "</select>\r\n</td>\r\n";
    
    echo "<td>\r\n";
//    $rubrike = queryMysql("SELECT razredi_predmeti_rubrike.rubrika "
//            . "FROM razredi_predmeti_profesori INNER JOIN razredi_predmeti_rubrike "
//            . "ON razredi_predmeti_profesori.id = razredi_predmeti_rubrike.razredPredmetProfesorID "
//            . "WHERE razredi_predmeti_profesori.razredID = $razredID AND razredi_predmeti_profesori.predmetID = " . $predmet['predmetID']);
//    $brojRubrika = $rubrike->num_rows;
    for ($i = 0; $i < 5; ++$i){
//        $rubrika = $rubrike->fetch_array(MYSQLI_ASSOC);
        echo "<input type='text' name='predmetRubrika[" . $j . "][$i]' id='predmetRubrika$j$i' value='" . $predmet['rubrika' . $i] . "' maxlength='50'><br>\r\n";
    }
//    for ($i = $brojRubrika; $i < 5; ++$i){
//        echo "<input type='text' name='predmetRubrika[" . $j . "][$i]' id='predmetRubrika$i' value='' maxlength='50'><br>\r\n";
//    }
    echo "</td></tr>";
}

?>
    </table>
    <input type='submit' name='Submit' value='OK' />
</form>


<script type="text/javascript">

function toogleVisibility(elementID, button) {
    var el = document.getElementById(elementID);
    if (el.style.display === "none") {
        el.style.display = "";
        button.innerHTML = "Sakrij predmete";
    } else {
        el.style.display = "none";
        button.innerHTML = "Uredi predmete";
    }
}

</script>

<script>
    document.getElementById("razredi").className += " current";
</script>

</body>
</html>