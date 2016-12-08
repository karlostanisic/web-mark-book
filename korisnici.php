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
    <p>This is users page where you can see info on all users and you can edit/delete existing users 
        (click on E or X for appropriate user) or add new user (click on the button "Novi korisnik").</p>
</div>

<?php
echo "<a href='korisnik.php?action=add' class='novi'>Novi korisnik</a>";

$korisnici = queryMysql("SELECT korisnici.korisnikID, korisnici.korisnickoIme, razredi.naziv, ucenici.ime, ucenici.prezime, korisnici.admin "
                . "FROM korisnici LEFT JOIN razredi ON korisnici.razredID = razredi.razredID "
                . "LEFT JOIN ucenici ON korisnici.ucenikID = ucenici.ucenikID "
                . "ORDER BY admin DESC, razredi.naziv DESC, ucenici.prezime, ucenici.ime");
$broj = $korisnici->num_rows;

echo "<table border='1'><tr>"
        . "<th>Korisnicko ime</th>"
        . "<th>Admin</th>"
        . "<th>Razrednik</th>"
        . "<th>Skrbnik</th>"
        . "<th>edit</th>"
        . "<th>delete</th></tr>";

for ($j = 0; $j < $broj; ++$j) {
    $korisnik = $korisnici->fetch_array(MYSQLI_ASSOC);
    echo "<tr>";
    echo "<td>" . $korisnik['korisnickoIme'] . "</td>";
    echo "<td>" . $korisnik['admin'] . "</td>";
    echo "<td>" . $korisnik['naziv'] . "</td>";
    echo "<td>" . $korisnik['prezime'] . " " . $korisnik['ime'] . "</td>";
    if ($korisnik['admin'] == 0) {
        echo "<td><a href='korisnik.php?action=edit&korisnikID=" . $korisnik['korisnikID'] . "'>E</a></td>";
        echo "<td><a href='korisnik.php?action=delete&korisnikID=" . $korisnik['korisnikID'] . "'>X</a></td>";
    } else {
        echo "<td>&nbsp;</td><td>&nbsp;</td>";
    }
//    echo "<td><a href='korisnik.php?action=edit&korisnikID=" . $korisnik['korisnikID'] . "'>E</a></td>";
//    echo "<td><a href='korisnik.php?action=delete&korisnikID=" . $korisnik['korisnikID'] . "'>X</a></td>";
    echo "</tr>";
}

echo "</table>";

?>
<script>
    document.getElementById("korisnici").className += " current";
</script>

    </body>
</html>