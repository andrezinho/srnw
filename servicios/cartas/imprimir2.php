<?php
if(!session_id()){ session_start(); }
    include("../../config.php");
    include("num2letraK.php");
    $IdCarta = (isset($_GET["IdCarta"]))?$_GET["IdCarta"]:'';
    $SQL 		= "SELECT * FROM carta WHERE idcarta='$IdCarta'";
    $Consulta           = $Conn->Query($SQL);
    $row 		= $Conn->FetchArray($Consulta);
    $SQLDistrito	= "SELECT TRIM(descripcion) FROM ubigeo WHERE idubigeo='".$row[10]."'";
    $ConsDistrito 	= $Conn->Query($SQLDistrito);
    $rowdistrito 	= $Conn->FetchArray($ConsDistrito);
    $Distrito 		= $rowdistrito[0];
    header('Content-type: application/msword');
    header('Content-Disposition: inline; filename=Carta_'.$row[3].'.doc'); 
    $Fecha = $row[2];
    $mifecha=explode('-', $Fecha);
    //ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $Fecha, $mifecha);
 
           

    $Fecha = $mifecha[2]."/".$mifecha[1]."/".$mifecha[0];
    $Hora = $row[22];
    $HoraH	= trim(CantidadEnLetra((int)substr($Hora, 0, 2)));
    $HoraM	= trim(CantidadEnLetra((int)substr($Hora, 3, 2)));
    $Observacion = utf8_decode($row[13]);
    $SqlO	= "SELECT TRIM(descripcion) FROM ocurrencia WHERE idocurrencia='$row[11]'";
    $ConsO 	= $Conn->Query($SqlO);
    $rowO 	= $Conn->FetchArray($ConsO);
    $Ocurrencia = $rowO[0];       /*Crea el Documento de Word*/	
    $output	=	"{\\rtf1"; 
    $output.= "{\\b CERTIFICO: }";
    
    $fva=explode(':', $Hora);
    $fv=explode('0', $fva[0]);
    

   // if($fv[1]==1){
    $output.= "{\\ Que, el Original de la presente Carta Notarial ha sido diligenciada el ".utf8_decode(día)." ".$mifecha[2]." de ".ucwords(strtolower($Meses[(int)$mifecha[1]]))." del ".$mifecha[0].", }";    
    //$output.= "{\\ QUE, SIENDO LA UNA CON ".$HoraM." DEL DIA ".trim(CantidadEnLetra((int)$mifecha[2]))." DE ".strtoupper($Meses[(int)$mifecha[1]])." DEL ".trim(CantidadEnLetra((int)$mifecha[0])).", }";    
    //}
    //else{
        //$output.= "{\\ QUE, SIENDO LAS ".$HoraH." CON ".$HoraM." DEL DIA ".trim(CantidadEnLetra((int)$mifecha[2]))." DE ".strtoupper($Meses[(int)$mifecha[1]])." DEL ".trim(CantidadEnLetra((int)$mifecha[0])).", }";
          $output.= "{\\ en ".utf8_decode(dirección)." indicada en la misma, ".utf8_decode(strtolower($row[9]))." distrito de ".ucwords(strtolower(utf8_decode($Distrito))).", }";
    //}
    
    $output.= "{\\ siendo recepcionada por el destinatario, quien ". utf8_decode(firmó) ." el presente duplicado. Doy ".utf8_decode(fé).".}";
    $output.="\\par";
    $output.= "{\\ Ciudad de Tarapoto, ".$mifecha[2]." de ".ucwords(strtolower($Meses[(int)$mifecha[1]]))." del ".$mifecha[0]."3}";
    $output.= "}"; 
    echo $output;
?>