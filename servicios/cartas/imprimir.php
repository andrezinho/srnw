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
    $output.="\\par";
    $output.="\\par";
    $output.="\\par";
    $output.= "{\\qj\\b\\fs28  CERTIFICO: }";
    
    $fva=explode(':', $Hora);
    $fv=explode('0', $fva[0]);
    
    $diaText = CantidadEnLetra((int)$mifecha[2]);
    $anioText = CantidadEnLetra((int)$mifecha[0]);        

   
    $output.= "{\\qj\\fs28 Que, el ".utf8_decode(día)." ".$diaText."  de ".ucwords(strtolower($Meses[(int)$mifecha[1]]))." del ".$anioText." a las 4.30pm se ".utf8_decode(realizó)." la diligencia de entrega del original de la presente carta notarial y copia de nueve anexos qeu sello y rubrico, }";    
    $output.= "{\\qj\\fs28 en ".utf8_decode(dirección)." indicada ".utf8_decode(strtolower($row[9]))." distrito de ".ucwords(strtolower(utf8_decode($Distrito))).", }";
    $output.= "{\\qj\\fs28 por intermedio de la empresa de correos 'SLRN.' conformidad con lo dispuesto por el ". utf8_decode(artículo) ." 108 del Decreto Legislativo 1049 Ley del Notariado, siendo recepcionada por un empleado de mesa de parte de la, quien ". utf8_decode(selló) ." y ". utf8_decode(firmó) ." el presente duplicado. Doy ". utf8_decode(fé) ." }";
    
    //$output.= "{\\qj\\fs28 siendo recepcionada por el destinatario, quien ". utf8_decode(firmó) ." el presente duplicado. Doy ".utf8_decode(fé).".}";
    //$output.="\\qj\\par";
    $output.= "{\\qj\\fs28 Ciudad de Tarapoto, ".$mifecha[2]." de ".ucwords(strtolower($Meses[(int)$mifecha[1]]))." del ".$mifecha[0].".}";

    $output.= "{\\qj\\par\\fs28 Observaciones: }";
    $output.= "}\\qj"; 
    chmod('Carta_'.$row[3].'.doc',0755);
    echo $output;
?>