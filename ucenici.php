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
    <p>On this page, depending on the action you chose, you can edit, delete or 
        add informations about students. You can also edit student’s marks by 
        clicking ‘Ocjene’ or add user for that you want to give wrights to see 
        the marks of the given student, usually parent.</p>
</div>

<?php
echo "<a href='ucenik.php?action=add' class='novi'>Novi ucenik</a>";

$ucenici = queryMysql("SELECT * FROM ucenici LEFT JOIN razredi "
        . "ON ucenici.razredID = razredi.razredID "
        . "ORDER BY prezime, ime, razredi.naziv");
$broj = $ucenici->num_rows;

echo "<table border='1'><tr>"
    . "<th>Prezime</th>"
    . "<th>Ime</th>"
    . "<th>Razred</th>"
    . "<th>Telefon</th>"
    . "<th>Email</th>"
    . "<th>edit</th>"
    . "<th>delete</th>"
    . "<th>ocjene</th>"
    . "<th>korisnik</th></tr>";

for ($j = 0; $j < $broj; ++$j) {
    $ucenik = $ucenici->fetch_array(MYSQLI_ASSOC);
    echo "<tr>";
    echo "<td>" . $ucenik['prezime'] . "</td>";
    echo "<td>" . $ucenik['ime'] . "</td>";
    echo "<td>" . $ucenik['naziv'] . "</td>";
    echo "<td>" . $ucenik['tel'] . "</td>";
    echo "<td>" . $ucenik['email'] . "</td>";
    echo "<td><a href='ucenik.php?action=edit&ucenikID=" . $ucenik['ucenikID'] . "'>E</a></td>";
    echo "<td><a href='ucenik.php?action=delete&ucenikID=" . $ucenik['ucenikID'] . "'>X</a></td>";
    echo "<td><a href='ocjene.php?ucenikID=" . $ucenik['ucenikID'] . "'>Ocjene</a></td>";
    echo "<td><a href='korisnik.php?action=add&ucenikID=" . $ucenik['ucenikID'] . "'>U</a></td>";
    echo "</tr>";
}

echo "</table>";

?>

<script>
    document.getElementById("ucenici").className += " current";
</script>

    </body>
</html>