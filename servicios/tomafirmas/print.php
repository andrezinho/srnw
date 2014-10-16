<?php
if(!session_id()){ session_start(); } 
require('../../libs/fpdf.php');
include('../../config.php');
class PDF extends FPDF{
  function Header()
  {
    
  } 
}

  $pdf = new PDF('P'); 
  // Carga de datos
  $idkardex = $_GET['idk'];
  $idparticipante = $_GET['idp'];

  $sql = "SELECT kardex_participantes.idkardex, 
                    case documento.iddocumento when 1 then 'D.N.I'
                        else documento.descripcion end, 
                    kardex_participantes.idparticipante, 
                    cliente.dni_ruc, 
                    cliente.nombres||' '||coalesce(cliente.ape_paterno,'')||' '||coalesce(cliente.ap_materno,'') as nombres, 
                    kardex_participantes.idparticipacion, 
                    participacion.descripcion as par, 
                    kardex_participantes.firmo,
                    kardex.correlativo,
                    kardex_participantes.foto,
                    kardex_participantes.foto_conyuge
             FROM cliente 
                    INNER JOIN kardex_participantes ON (cliente.idcliente = kardex_participantes.idparticipante) 
                    INNER JOIN participacion ON (kardex_participantes.idparticipacion = participacion.idparticipacion) 
                    INNER JOIN documento ON (cliente.iddocumento = documento.iddocumento) 
                    INNER JOIN kardex on kardex.idkardex = kardex_participantes.idkardex 
             WHERE kardex_participantes.idkardex='$idkardex'";
    if((int)$_GET['c']==0)
    {
      $sql .= " and kardex_participantes.idparticipante = ".$idparticipante;
    }
    else
    {
      $sql .= " and kardex_participantes.conyuge = ".$idparticipante;
    }
    $q = $Conn->Query($sql);
    $row = $Conn->FetchArray($q);

$pdf->SetMargins(12, 5, 10, 10);  
$pdf->AddPage();

if((int)$_GET['c']==0)
{  
  $pdf->Image('../../../srnw_webcam/'.$row['foto'].'.jpg' , 10 ,55, 190 , 150,'JPG');
}
else
{
  $pdf->Image('../../../srnw_webcam/'.$row['foto_conyuge'].'.jpg' , 10 ,55, 190 , 150,'JPG');
}

$pdf->Output();
?>