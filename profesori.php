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
    <p>On this page you manage school’s teachers. Delete, edit or add new teacher.</p>
</div>

<?php

echo "<a href='profesor.php?action=add' class='novi'>Novi profesor</a>";

$profesori = queryMysql("SELECT * FROM profesori ORDER BY prezime, ime");
$broj = $profesori->num_rows;

echo "<table border='1'><tr>" 
        . "<th>Prezime</th>"
        . "<th>Ime</th>"
        . "<th>Titula</th>"
        . "<th>Telefon</th>"
        . "<th>Email</th>"
        . "<th>edit</th>"
        . "<th>delete</th></tr>";

for ($j = 0; $j < $broj; ++$j) {
    $profesor = $profesori->fetch_array(MYSQLI_ASSOC);
    echo "<tr>";
    echo "<td>" . $profesor['prezime'] . "</td>";
    echo "<td>" . $profesor['ime'] . "</td>";
    echo "<td>" . $profesor['titula'] . "</td>";
    echo "<td>" . $profesor['tel'] . "</td>";
    echo "<td>" . $profesor['email'] . "</td>";
    echo "<td><a href='profesor.php?action=edit&profesorID=" . $profesor['profesorID'] . "'>E</a></td>";
    echo "<td><a href='profesor.php?action=delete&profesorID=" . $profesor['profesorID'] . "'>X</a></td>";
    echo "</tr>";
}

echo "</table>";

?>

<script>
    document.getElementById("profesori").className += " current";
</script>

    </body>
</html>