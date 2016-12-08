<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'header.php';

$problem = FALSE;

if (!($logAdmin || $logRazrednik)) {
    $problem = TRUE;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['ucenikID'])) {
        $ucenikID = sanitizeString($_GET['ucenikID']);
    } else {
        $problem = TRUE;
    }
}
    

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ucenikID'])) {
        $ucenikID = sanitizeString($_POST['ucenikID']);
        $razredID = sanitizeString($_POST['razredID']);
        
        queryMysql("DELETE FROM ocjene WHERE ucenikID = $ucenikID");
        
        if (isset($_POST['ocjena'])) {
            $ocjena = $_POST['ocjena'];
        } else {
            $ocjena = array();
        }
        
        
        foreach ($ocjena as $predmetID => $value) {
            for ($rubrika = 0; $rubrika < 5; ++$rubrika) {
                for ($mjesec = 0; $mjesec < 10; ++$mjesec) {
                    if (isset($ocjena[$predmetID][$rubrika][$mjesec]) && $ocjena[$predmetID][$rubrika][$mjesec] !== "") {
                        queryMysql("INSERT INTO ocjene (ucenikID, predmetID, rubrikaBr, mjesec, ocjena) "
                                . "VALUES ($ucenikID, $predmetID, $rubrika, $mjesec, '" . sanitizeString($ocjena[$predmetID][$rubrika][$mjesec]) . "')");
                    }
                }
            }
        }
        
        queryMysql("UPDATE ucenici SET zadnjiUpis=now() WHERE ucenikID=$ucenikID");
        
        if ($logRazrednik)
            header("Location: http://localhost/WebImenik/razrednik.php");
        else
            header("Location: http://localhost/WebImenik/razrednik.php?razredID=" . $razredID);
        die();
        
        
        
        
        
    } else {
        $problem = TRUE;
    }
}

if (!isset($ucenikID)) {
    $problem = TRUE;
}

//echo "<br>" . $problem;

if ($problem) {
    destroySession();
    header("Location: http://localhost/WebImenik/index.php");
    die();
}

$ocjene = queryMysql("SELECT ocjene.predmetID, predmeti.naziv, ocjene.rubrikaBr, ocjene.mjesec, ocjene.ocjena "
        . "FROM ocjene LEFT JOIN predmeti ON ocjene.predmetID = predmeti.predmetID "
        . "WHERE ocjene.ucenikID = $ucenikID");

$poljeOcjena = array();

$broj = $ocjene->num_rows;
for ($j = 0; $j < $broj; ++$j) {
    $ocjena = $ocjene->fetch_array(MYSQLI_ASSOC);
    $poljeOcjena[$ocjena['predmetID']][$ocjena['rubrikaBr']][$ocjena['mjesec']] = $ocjena['ocjena'];
}

$razred = queryMysql("SELECT razredID, ime, prezime FROM ucenici WHERE ucenikID = $ucenikID")->fetch_array(MYSQLI_ASSOC);
$razredID = $razred['razredID'];
$imeUcenika = $razred['ime'];
$prezimeUcenika = $razred['prezime'];

if (file_exists("images/$ucenikID.jpg")) $slikaPath = "images/$ucenikID.jpg";
else $slikaPath = "images/dummy.jpg";


?>

<div style="float: right;">
    <h2><?php echo "$imeUcenika $prezimeUcenika"; ?></h2>
    <img src="<?php echo $slikaPath ?>">
</div>

<form name="ocjene" action="upis-ocjena.php" method="POST">
    <input type="hidden" name="ucenikID" id="ucenikID" value="<?php echo $ucenikID ?>">
    <input type="hidden" name="razredID" id="razredID" value="<?php echo $razredID ?>">

    <table border="1" class="ocjene tablicaocjena">
    <tr>
        <th>
            Predmet
        </th>
        <th>
            Rubrike
        </th>
        <th>
            IX
        </th>
        <th>
            X
        </th>
        <th>
            XI
        </th>
        <th>
            XII
        </th>
        <th>
            I
        </th>
        <th>
            II
        </th>
        <th>
            III
        </th>
        <th>
            IV
        </th>
        <th>
            V
        </th>
        <th>
            VI
        </th>
    </tr>
<?php



$predmeti = queryMysql("SELECT predmeti.predmetID, predmeti.naziv, razredi_predmeti.rubrika0, razredi_predmeti.rubrika1, razredi_predmeti.rubrika2, razredi_predmeti.rubrika3, razredi_predmeti.rubrika4 "
        . "FROM predmeti INNER JOIN razredi_predmeti ON predmeti.predmetID = razredi_predmeti.predmetID "
        . "WHERE razredi_predmeti.razredID = $razredID "
        . "ORDER BY predmeti.naziv");
$broj = $predmeti->num_rows;
for ($j = 0; $j < $broj; ++$j){
    $predmet = $predmeti->fetch_array(MYSQLI_ASSOC);
    //echo "<tr><td rowspan='5'>" . $predmet['naziv'] . "</td>\r\n";
    echo "<tr><td colspan='12' class='predmet'>" . $predmet['naziv'] . "</td></tr>\r\n";
    echo "<tr><td rowspan='5' class='komentar'>Komentari profesora</td>";
    for ($i = 0; $i < 5; ++$i) {
        echo "<td class='rubrika'>" . $predmet['rubrika' . $i] . "</td>\r\n";
        for ($k = 0; $k < 10; ++$k) {
            echo "<td class='ocjena'>";
            echo "<input type='text' name='ocjena[" . $predmet['predmetID'] . "][" . $i . "][" . $k . "]' "
                    . "id='ocjena[" . $predmet['predmetID'] . "][" . $i . "][" . $k . "]' "
                    . "value='";
            if (isset($poljeOcjena[$predmet['predmetID']][$i][$k])) {
                echo $poljeOcjena[$predmet['predmetID']][$i][$k];
            }
            echo "'>";
            echo "</td>\r\n";
        }
        if ($i < 4) echo "</tr><tr>";
    }
    echo "</tr>";
}
?>
</table>
    <input type="submit" value="OK">
    <a href="javascript:history.go(-1)" class='novi'>ODUSTANI</a>
</form>

<script>
    document.getElementById("razredi").className += " current";
</script>

</body>
</html>