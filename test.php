<?php
function Conectar(){
$host = "localhost";
$user = "postgres";
$pass = "12345678";
$db = "srnw";
$puerto = "5432";

// conectarse a la base de datos
$connection = pg_connect ("host=$host dbname=$db user=$user password=$pass port=$puerto");
if (!$connection){
die("No ha sido posible establecer la conexión con la base de datos.");
}
return($connection);
}

?>