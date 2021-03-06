<?php
if(!session_id()){ session_start(); } 
require('../libs/fpdf.php');
include('../config.php');
include('../config_seguridad.php');
include('../libs/num2letra.php'); 
class PDF extends FPDF{
  function Header()
  {
    global $Cab, $w, $Titulo;    
  } 
  // Cargar los datos
  function LoadData($data,$Id)
  {
    global $Conn,$ConnS; 
    $this->SetFont('Arial', '', 10);  
    $row  = $Conn->FetchArray($data);
    $Igv  = $row[15];
    $SubTotal       = $row[16];
    $nIgv           = 0;
    if ($row[14] == 1)
    {
        $SubTotal = $row[16] - ($row[16] * ($Igv/100));
        $nIgv       = $row[16] - $SubTotal;
    } 
    $Sql = "SELECT login FROM usuario WHERE idusuario='".$row[18]."'";
    $ConsultaS = $ConnS->Query($Sql);
    $rowS = $ConnS->FetchArray($ConsultaS);
    $Usuario        = $rowS[0];

    $this->Ln(55);
    $h = 5;
    $border = 0;    
    $this->Cell(125,$h,"CLIENTE: ".utf8_decode($row[5]),$border,0,'L',false);    
    $this->Cell(65,$h,utf8_decode("INTERNO N°: ").$row[1]."/".$row[2]."-".$row[3],$border,1,'L',false);
    //$this->Cell(65,$h,utf8_decode("INTERNO N°: ").$row[1]."/".$row[2]."-".$row[3]." (".$row[4].")",$border,1,'L',false);
    $this->Cell(125,$h,"DIRECCION: ".utf8_decode($row[6]),$border,0,'L',false);    
    $this->Cell(65,$h,utf8_decode("TIPO PAGO: ").$row[10]."-EFECTIVO",$border,1,'L',false);

    $this->Cell(125,$h,$row[7].": ".utf8_decode($row[8]),$border,0,'L',false);    
    $this->Cell(65,$h,utf8_decode("FECHA DE EMISIÓN: ").$Conn->DecFecha($row[11])." : ".substr($row[12],0,8),$border,1,'L',false);

    $border = 'TB';

    $this->Cell(20,$h,'CANT',$border,0,'C',false);  
    $this->Cell(95,$h,'SERVICIO',$border,0,'C',false);  
    $this->Cell(25,$h,'KARDEX',$border,0,'C',false);  
    $this->Cell(20,$h,'P. UNIT',$border,0,'C',false); 
    $this->Cell(25,$h,'IMPORTE',$border,1,'C',false);   

    $SQL2 = "SELECT facturacion_detalle.anio, facturacion_detalle.idfacturacion, facturacion_detalle.item, facturacion_detalle.idservicio, servicio.descripcion, facturacion_detalle.correlativo, facturacion_detalle.cantidad, facturacion_detalle.monto, (facturacion_detalle.cantidad * facturacion_detalle.monto)FROM servicio INNER JOIN facturacion_detalle ON (servicio.idservicio = facturacion_detalle.idservicio) WHERE facturacion_detalle.idfacturacion = '$Id' ";
    $Consulta2 = $Conn->Query($SQL2);   
    $h=5;
    $border = 0;    
    while($row2 = $Conn->FetchArray($Consulta2))
    {

	$t = strlen($row2[4]);
	$add="";
	if($t>45){ $add="..."; }
      $this->Cell(20,$h,$row2[6],$border,0,'C',false);
      //$this->Cell(95,$h,substr(strtoupper($row2[4]),0,45).$add,$border,0,'L',false);
      $this->MultiCellp(120,$h,strtoupper($row2[4]),$border,'J',false);
      $this->Cell(25,$h,$row2[5],$border,0,'C',false);
      $this->Cell(20,$h,$row2[7],$border,0,'R',false);
      $this->Cell(25,$h,$row2[8],$border,1,'R',false);

    }
    $this->Ln(15);
    $this->SetFont('Arial', '', 9);
    //$this->Cell(0,$h,"Observacion: ".$row['observaciones'],$border,1,'L',false);

    $row['observaciones'] = substr($row['observaciones'],0,167).".";    

    $h = $h -1;
    $this->MultiCell(120,$h,"Observacion: ".$row['observaciones']." ",$border,'J',false);

    $this->SetFont('Arial', '', 11);
    $this->Cell(130,$h,'Son: '.CantidadEnLetra($row[16]),$border,0,'L',false);      
    $this->Cell(30,$h,'Sub Total S/.:',$border,0,'R',false); 
    $t = 0;
    if ($row[0]==1){$t = $row[16];} else{$t=$SubTotal;}
    $this->Cell(25,$h,$t,$border,1,'R',false);

    $this->Cell(140,$h,'',$border,0,'L',false);      
    $this->Cell(20,$h,'I.G.V. :',$border,0,'R',false); 
    $this->Cell(25,$h,number_format($nIgv, 2),$border,1,'R',false);  

    $this->Cell(130,$h,'Responsable: '.$Usuario,$border,0,'L',false);      
    $this->Cell(30,$h,'Importe Total S/. :',$border,0,'R',false); 
    $this->Cell(25,$h,$row[16],$border,1,'R',false);   
  }
}

  $pdf = new PDF('P'); 
  $Id = isset($_GET["Id"])?$_GET["Id"]:'';  
  // Carga de datos
  $Select   = "SELECT facturacion.idcomprobante, comprobante.abreviatura, facturacion.comprobante_serie, facturacion.comprobante_numero, ";
  $Select   = $Select." facturacion.idatencion, facturacion.nombres, facturacion.direccion, documento.descripcion, facturacion.dni_ruc, ";
  $Select   = $Select." facturacion.credito, forma_pago.descripcion, facturacion.facturacion_fecha, facturacion.facturacion_hora, facturacion.observaciones, ";
  $Select   = $Select." facturacion.igv_afecto, facturacion.igv, facturacion.total, facturacion.cancelacion_fecha, facturacion.idusuario,facturacion.observaciones ";
  $Select   = $Select." FROM facturacion INNER JOIN comprobante ON (facturacion.idcomprobante = comprobante.idcomprobante) ";
  $Select   = $Select." INNER JOIN documento ON (facturacion.iddocumento = documento.iddocumento) INNER JOIN forma_pago ON (facturacion.idforma_pago = forma_pago.idforma_pago) ";
  $Select   = $Select." WHERE facturacion.idfacturacion = '$Id'"; 
  $Consulta   = $Conn->Query($Select);

$pdf->SetMargins(12, 5, 10, 10);  
$pdf->AddPage();
$pdf->LoadData($Consulta,$Id);
$pdf->Output();
?>