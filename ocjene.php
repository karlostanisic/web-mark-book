<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'header.php';

if (!($logAdmin || $logRazrednik || $logStaratelj)) {
    destroySession();
    header("Location: http://localhost/WebImenik/index.php");
    die();
}

if ($logStaratelj){
    $ucenikID = $logStaratelj;
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset ($_GET['ucenikID'])) {
    $ucenikID = $_GET['ucenikID'];
} else {
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
<div style="float: right; width: 25%">
    <h2><?php echo "$imeUcenika $prezimeUcenika"; ?></h2>
    <img src="<?php echo $slikaPath ?>">
</div>


<table class="ocjene">
    <tr>
        <th>
            &nbsp;
        </th>
        <th>
            &nbsp;
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
    echo "<tr><td colspan='12' class='predmet'>" . $predmet['naziv'] . "</td></tr>\r\n";
    echo "<tr><td rowspan='5' class='komentar'>Komentari profesora</td>";
    for ($i = 0; $i < 5; ++$i) {
        if ($predmet['rubrika' . $i] == "") $predmet['rubrika' . $i] = "&nbsp;";
        echo "<td class='rubrika'>" . $predmet['rubrika' . $i] . "</td>\r\n";
        for ($k = 0; $k < 10; ++$k) {
            echo "<td class='ocjena'>";
            if (isset($poljeOcjena[$predmet['predmetID']][$i][$k])) {
                echo $poljeOcjena[$predmet['predmetID']][$i][$k];
            } else {
                echo "&nbsp;";
            }
            echo "</td>\r\n";
        }
        if ($i < 4) echo "</tr><tr>";
    }
    echo "</tr>";
}
?>
</table>

</body>
</html>