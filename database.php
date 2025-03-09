<?php

$serveur="localhost";
$user="root";
$pwd="";
$dbname="bibliotheque";
//true or false
$connexion=mysqli_connect($serveur,$user,$pwd,$dbname);

if(!$connexion){
    echo "Erreur de connexion";
}else{
    echo "";
}

?>