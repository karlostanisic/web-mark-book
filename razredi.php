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

?>

<div class="upute">
    <p>On this page you manage classes' names. Delete, edit or add new class. You can also add user that is responsible for maintaining particular class (usually that is class’ head teacher) by clicking ‘U’ or you can go to class’ page were you can maintain class info in much more details (click ‘DETALJI’).</p>
</div>

<?php

echo "<a href='razred.php?action=add' class='novi'>Novi razred</a>";

$razredi = queryMysql("SELECT razredi.razredID, razredi.naziv, profesori.profesorID, profesori.prezime, profesori.ime "
        . "FROM razredi LEFT JOIN profesori "
        . "ON razredi.razrednikID = profesori.profesorID "
        . "ORDER BY naziv");
$broj = $razredi->num_rows;

echo "<table border='1'><tr>"
        . "<th>Naziv</th>"
        . "<th>Razrednik</th>"
        . "<th>edit</th>"
        . "<th>delete</th>"
        . "<th>korisnik</th>"
        . "<th>razrednicki kutak</th></tr>";

for ($j = 0; $j < $broj; ++$j) {
    $razred = $razredi->fetch_array(MYSQLI_ASSOC);
    echo "<tr>";
    echo "<td>" . $razred['naziv'] . "</td>";
    echo "<td>" . $razred['prezime'] . " " . $razred['ime'] . "</td>";
    echo "<td><a href='razred.php?action=edit&razredID=" . $razred['razredID'] . "'>E</a></td>";
    echo "<td><a href='razred.php?action=delete&razredID=" . $razred['razredID'] . "'>X</a></td>";
    echo "<td><a href='korisnik.php?action=add&razredID=" . $razred['razredID'] . "'>U</a></td>";
    echo "<td><a href='razrednik.php?razredID=" . $razred['razredID'] . "'>DETALJI</a></td>";
    echo "</tr>";
}

echo "</table>";

?>

<script>
    document.getElementById("razredi").className += " current";
</script>


    </body>
</html>