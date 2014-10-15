<?php
include('../config.php');


$id=$_POST['id_foto'];//20120214052450.jpg
$nombre=$_POST['nombre_foto'];
$des=$_POST['des'];
$sub=(substr($id,-18));
$id_foto=str_replace(".jpg", "", $sub);//20120214052450
$sql="update fotos set id_foto=id_foto, nombre='$nombre', des='$des' where id_foto='$id_foto'";
$modifica=$Conn->Query($sql);
print("<script>window.location.replace('index.php');</script>");

?>