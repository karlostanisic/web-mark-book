<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once './includes/config.php';
require_once './includes/functions.php';

if (isset($_POST['korisnickoIme'])) {
    $korisnickoIme = sanitizeString($_POST['korisnickoIme']);
    $result = queryMysql("SELECT * FROM korisnici WHERE korisnickoIme='$korisnickoIme'");
    
    if ($result->num_rows)
        echo "Korisnicko ime vec postoji";
    else
        echo "Korisnicko ime je slobodno";
}

?>