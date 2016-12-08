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
    <p>On this page you manage different subjects that are taught by school’s curriculum. Delete, edit or add new subject.</p>
</div>

<?php

echo "<a href='predmet.php?action=add' class='novi'>Novi predmet</a>";

$predmeti = queryMysql("SELECT * FROM predmeti ORDER BY naziv");
$broj = $predmeti->num_rows;

echo "<table border='1'><tr><th>Naziv</th><th>edit</th><th>delete</th></tr>";

for ($j = 0; $j < $broj; ++$j) {
    $predmet = $predmeti->fetch_array(MYSQLI_ASSOC);
    echo "<tr>";
    echo "<td>" . $predmet['naziv'] . "</td>";
    echo "<td><a href='predmet.php?action=edit&predmetID=" . $predmet['predmetID'] . "'>E</a></td>";
    echo "<td><a href='predmet.php?action=delete&predmetID=" . $predmet['predmetID'] . "'>X</a></td>";
    echo "</tr>";
}

echo "</table>";

?>


<script>
    document.getElementById("predmeti").className += " current";
</script>
    </body>
</html>