<?php
include("class.upload.php");
include('../config.php');
$id_foto=$_GET['kardex']."_".$_GET['idp'];//
unlink("../../srnw_webcam/".$id_foto.".jpg");
$foo = new Upload('php:'.$_SERVER['HTTP_X_FILE_NAME']);
if ($foo->uploaded) 
{    
    $foo->file_new_name_body = $id_foto;    
    $name = strtoupper($_GET['nombres']);	
    $foo->image_text            = $_GET['kardex']."\n".utf8_decode($name)."\n".date('d/m/Y')." Hora:".date('H:m');
    $foo->image_text_color      = '#000000';
    $foo->image_text_position   = 'BL';
    $foo->image_text_alignment  = 'L';
    //$foo->image_text_font       = "angsanalupc.gdf";
    $foo->image_text_padding_x  = 10;    
    $foo->image_text_padding_y  = 2;
    $foo->image_text_background = '#FFFFFF';
    $foo->image_text_background_opacity = 30;
    $foo->image_convert = 'jpg';    
    $foo->Process("../../srnw_webcam/");   
    if ($foo->processed) 
    {
      echo 'original image copied';
      $foo->image_text            = $_GET['kardex']."\n".utf8_decode($name)."\n".date('d/m/Y');
        $foo->image_text_color      = '#000000';
        $foo->image_text_position   = 'BL';
        $foo->image_text_alignment  = 'L';
        //$foo->image_text_font       = "angsanalupc.gdf";
        $foo->image_text_padding_x  = 10;    
        $foo->image_text_padding_y  = 2;
        $foo->image_text_background = '#FFFFFF';
        $foo->image_text_background_opacity = 30;
        $foo->processed;
    } 
    else 
    {
      echo 'error : ' . $foo->error;
    }
}

chmod("../../srnw_webcam/".$id_foto.".jpg", 0777);

$fecha = date('Y-m-d');
if(!isset($_GET['c'])||$_GET['c']=="")
{
    $_GET['c'] = 1;
}
if((int)$_GET['c']==0)
{
    $sql="update kardex_participantes set foto = '".$id_foto."', firmo=1, firmofecha='".$fecha."' where idkardex = ".$_GET['idkardex']." and idparticipante = ".$_GET['idp'];    
}
else
{
    $sql="update kardex_participantes set foto_conyuge = '".$id_foto."', firmo_conyuge=1, firmofecha_conyuge='".$fecha."' where idkardex = ".$_GET['idkardex']." and conyuge = ".$_GET['idp'];
}
//echo $sql;
$inserta_foto=$Conn->Query($sql);

//Verifiacmos Estado
$Id=$_GET['idk'];
$sqlr = "SELECT distinct idrepresentado 
        from kardex_participantes 
        where idrepresentado is not null and idkardex = ".$_GET['idkardex'];
$qr = $Conn->Query($sqlr);
$idsr = "0";
while($rr = $Conn->FetchArray($qr))
{
    $idsr .= ",".$rr[0];
}


$sql = "SELECT count(*),firmo from kardex_participantes
        where idkardex = ".$_GET['idkardex']." and idparticipante not in (".$idsr.")
        group by firmo";
$q = $Conn->Query($sql);
$flag = true;
while($r=$Conn->FetchArray($q))
{
    if($r['firmo']=="0")
        $flag = false;
}

if($flag)
{
    $sql = "SELECT count(*),firmo_conyuge
            from kardex_participantes
            where idkardex = ".$_GET['idkardex']." and idparticipante not in (".$idsr.") and conyuge is not null
            group by firmo_conyuge";
    $q = $Conn->Query($sql);    
    while($r=$Conn->FetchArray($q))
    {
        if($r['firmo']=="0")
            $flag = false;
    }
    if($flag)
    {
        $sql = "UPDATE kardex set estado = 2 where idkardex = ".$_GET['idkardex'];
        $q = $Conn->Query($sql);
    }
}

$filename = $id_foto.'.jpg';//nombre del archivo

$url = 'http://' . $_SERVER['HTTP_HOST'].'/srnw_webcam/' . $filename;//generamos la respuesta como la ruta completa
print "$url\n";
?>