<?php
if(!session_id()){ session_start(); }	
	include('../../config.php');
	include('../../config_seguridad.php');	
	$Op = $_POST["Op"];
	$Id = isset($_POST["Id"])?$_POST["Id"]:'';	
	$Enabled	= "";
	$Enabled2	= "";
	$Guardar	= "";	
	$Usuario = $_SESSION["Usuario"];	
	$Fecha	= date('d/m/Y');
	$Estado = "<label style='color:#FF6600'>PENDIENTE</label>";
	$Anio = $_SESSION["Anio"];
	$Guardar = "Op=".$Op;
	if($Op==2 or $Op==4)
  {
    $Enabled = "readonly";
	}	
	$Enabled2 = "readonly";	
	if($Id!='')
    {
        $Select 	= "SELECT * FROM kardex WHERE idkardex = ".$Id;
        $Consulta 	= $Conn->Query($Select);
        $row 	= $Conn->FetchArray($Consulta);
        $Usuario 	= $_SESSION["Usuario"];
        $Fecha	= $Conn->DecFecha($row[2]);
        $Firmado	= $row[13];
        if ($row[15]==1){
                $Estado = "<label style='color:#003366'>GENERADO</label>";
        }
        if ($row[15]==2){
                $Estado = "<label style='color:#003366'>TERMINADO</label>";
        }
        if ($row[15]==3){
                $Estado = "<label style='color:#FF00000'>ANULADO</label>";
        }
        $Anio = $row[18];
        $Sql = "SELECT nombres FROM usuario WHERE idusuario=".$row[16];
        $ConsultaS = $ConnS->Query($Sql);
        $rowS = $ConnS->FetchArray($ConsultaS);
        $Usuario	= $rowS[0];
        $SqlSe = "SELECT descripcion FROM servicio WHERE idservicio=".$row[4];
        $ConsultaSe = $Conn->Query($SqlSe);
        $rowSe = $Conn->FetchArray($ConsultaSe);
        $Servicio	= $rowSe[0];
	}
?>
<script>	
    function tomarFoto(idk,idp, c)
    {
        var ventana = window.open('../../webcam/index.php?idk='+idk+'&idp='+idp+'&c='+c, 'Buscar', 'width=1100, height=700, resizable=no, scrollbars=no');
        ventana.focus();
    }
	function AgregarFoto(IdParticipante)
    {
        var url = 'ftp/index.php?IdKardex=<?php echo $Id;?>&IdParticipante=' + IdParticipante;
        var ventana = window.open(url, 'Buscar', 'width=430, height=250, resizable=no, scrollbars=no');
        ventana.focus();
	}
	function VerFoto(IdParticipante)
    {
        var url = 'ftp/vistaprevia.php?IdKardex=<?php echo $Id;?>&IdParticipante=' + IdParticipante;
        var ventana = window.open(url, 'Buscar', 'width=595.3, height=841.9, resizable=no, scrollbars=no');
        ventana.focus();
	}	
	function CambiaFirmado(Id){
		if (document.getElementById('Firmo2D' + Id).checked)
        {
            $('#FirmoD' + Id).val(1);
            $('#FirmoFecha' + Id).css("display", "block");
            $('#lblFechaD' + Id).css("display", "none");
            $('#FirmoFecha' + Id).val('<?php echo date('d/m/Y');?>');
            $('#lblFechaD' + Id).html('<?php echo date('d/m/Y');?>');
		}
        else
        {
            $('#FirmoD' + Id).val(0);
            $('#FirmoFecha' + Id).css("display", "none");
            $('#lblFechaD' + Id).css("display", "block");
            $('#FirmoFecha' + Id).val('01/01/1990');
            $('#lblFechaD' + Id).html('01/01/1990');
		}
	}	
	function Cancelar(){
            window.location.href='index.php';
	}	
	function ValidarFormEnt(evt)
    {
        var keyPressed = (evt.which) ? evt.which : event.keyCode;
        if (keyPressed == 13 ){
                 Guardar(<?php echo $Op;?>);
        }
	}
    function getParticipantes(idkardex)
    {        
        $.get('list_participantes.php','idk='+idkardex,function(data){
            $("#box-list-participantes").empty().append(data);
            $(".box-pic a").lightBox(); 
        })
    }
</script>
<link href="estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../webcam/jquery.lightbox-0.5.js"></script>
<link rel="stylesheet" type="text/css" href="../../webcam/jquery.lightbox-0.5.css" />
<script>        
    $(document).ready(function(){        
        $(".box-pic a").lightBox();        
    });
</script>
<div align="center">
<form id="form1" name="form1" method="post" action="guardar.php?<?php echo $Guardar;?>" enctype="multipart/form-data">
<table width="800" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="133" class="TituloMant">Nro Kardex :</td>
    <td width="292"><input type="text" class="inputtext" style="text-align:center; font-size:12px; width:65px" name="0form1_correlativo" id="Id" maxlength="2" value="<?php echo $row[3];?>" <?php echo $Enabled2;?> onkeypress="CambiarFoco(this, 'Cliente');"/>
    <input type="hidden" name="1form1_idkardex" value="<?php echo $row[0];?>" /><input type="hidden" name="0form1_archivo" value="<?php echo $row[3];?>.doc" /></td>
    <td width="267" align="right">
        <table width="160" border="0" cellspacing="0" cellpadding="0">
  		<tr>
                <td>&nbsp;</td>
                <td align="right"><?php echo $Estado;?></td>
	    </tr>
	</table>	
    </td>
  </tr>
  <tr>
    <td width="133" class="TituloMant">Fecha : </td>
    <td colspan="2"><input type="text"  align="left" class="inputtext" style="font-size:12px; width:80px; text-transform:uppercase;" name="3form1_fecha" id="Fecha" value="<?php echo $Fecha;?>" <?php echo $Enabled2;?> onkeypress="CambiarFoco(event, 'Servicio');"/></td>
  </tr>
  <tr>
    <td width="133" class="TituloMant">Servicio  :</td>
    <td colspan="2"><input type="text" class="inputtext" style="font-size:12px; width:350px;" name="Servicio" id="Servicio"  maxlength="100" value="<?php echo $Servicio;?>" <?php echo $Enabled2;?> onkeypress="CambiarFoco(event, 'NroEscritura');"/></td>
  </tr>
  <tr>
    <td colspan="3" >&nbsp;</td>
    </tr>
  </tr>
  
  </table>
  <div style="border:1px dotted #cccccc;">
  <div id="box-list-participantes" style="overflow-y: scroll;">
  <?php
    //Obtengo todos los id de los representados
    $sqlr = "SELECT distinct idrepresentado 
            from kardex_participantes 
            where idrepresentado is not null and idkardex = ".$Id;
    $qr = $Conn->Query($sqlr);
    $idsr = "0";

    while($rr = $Conn->FetchArray($qr))
    {
      $idsr .= ",".$rr[0];
    }

    $SQL2 = "SELECT kardex_participantes.idkardex, 
                    case documento.iddocumento when 1 then 'D.N.I'
                        else documento.descripcion end, 
                    kardex_participantes.idparticipante, 
                    cliente.dni_ruc, 
                    cliente.nombres||' '||coalesce(cliente.ape_paterno,'')||' '||coalesce(cliente.ap_materno,'') as nombres, 
                    kardex_participantes.idparticipacion, 
                    participacion.descripcion as participacion, 
                    kardex_participantes.firmo,
                    kardex_participantes.foto,
                    kardex_participantes.firmofecha,
                    0 as c,
                    kardex_participantes.conyuge,
                    kardex_participantes.idrepresentado
             FROM cliente 
                    INNER JOIN kardex_participantes ON (cliente.idcliente = kardex_participantes.idparticipante) 
                    INNER JOIN participacion ON (kardex_participantes.idparticipacion = participacion.idparticipacion) 
                    INNER JOIN documento ON (cliente.iddocumento = documento.iddocumento) 
             WHERE kardex_participantes.idkardex='$Id' and kardex_participantes.idparticipante not in (".$idsr.")
             UNION ALL 
             SELECT kardex_participantes.idkardex, 
                    case documento.iddocumento when 1 then 'D.N.I'
                        else documento.descripcion end, 
                    cliente.idcliente as idparticipante, 
                    cliente.dni_ruc, 
                    cliente.nombres||' '||coalesce(cliente.ape_paterno,'')||' '||coalesce(cliente.ap_materno,'') as nombres, 
                    kardex_participantes.idparticipacion, 
                    participacion.descripcion as participacion, 
                    kardex_participantes.firmo_conyuge as firmo,
                    kardex_participantes.foto_conyuge as foto,
                    kardex_participantes.firmofecha_conyuge as firmofecha,
                    1 as c,
                    kardex_participantes.conyuge,
                    kardex_participantes.idrepresentado
             FROM cliente 
                    INNER JOIN kardex_participantes ON (cliente.idcliente = kardex_participantes.conyuge) 
                    INNER JOIN participacion ON (kardex_participantes.idparticipacion = participacion.idparticipacion) 
                    INNER JOIN documento ON (cliente.iddocumento = documento.iddocumento) 
             WHERE kardex_participantes.idkardex='$Id' and conyuge is not null
             order by conyuge";
             //echo $SQL2;
    $Consulta2 = $Conn->Query($SQL2);           
    while($row2 = $Conn->FetchArray($Consulta2))
    {
      $representa = "";
      if($row2['idrepresentado']!="")
      {
         $sql_repre = "SELECT dni_ruc, 
                              nombres||' '||coalesce(ape_paterno,'')||' '||coalesce(ap_materno,'') as nombres
                       FROM cliente 
                       WHERE idcliente = ".$row2['idrepresentado'];
        $q_repre = $Conn->Query($sql_repre);
        $r_repre = $Conn->FetchArray($q_repre);
        $representa = $r_repre[1];
      }
    ?>
      <div class="box-participantes">
          <div class="box-pic">
            <?php 
                if($row2['foto']=="")
                {
            ?>
              <img src="../../imagenes/no_foto.jpg" width="150" height="112">
            <?php 
                }
                else
                {
                    ?>
                    <a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/srnw_webcam/<?php echo $row2['foto'] ?>.jpg" title="<?php echo $row2['nombres'].' - '.$row2['participacion'] ?>"><img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/srnw_webcam/<?php echo $row2['foto'] ?>.jpg" width="150" height="112" ></a>
                    <?php
                }
            ?>
          </div>
          <div class="box-tools">
              <a href="#" style="margin-left:10px"><img src="../../imagenes/iconos/foto.png" width="16" height="16" onclick="AgregarFoto(<?php echo $row2[2];?>);" style="cursor:pointer;" title="Agregar Foto del Participante" /></a>
              <a href="javascript:tomarFoto(<?php echo $row2[0]; ?>,<?php echo $row2[2];?>,<?php echo $row2[10] ?>)" style="margin-left:10px"><img src="../../png/camweb.png" width="16" height="16"  title="Tomar Foto del Participante" /></a>
              <a href="print.php?idk=<?php echo $row2[0]; ?>&idp=<?php echo $row2[2];?>&c=<?php echo $row2[10] ?>" target="_blank" style="margin-left:10px"><img src="../../imagenes/iconos/imprimir.png" width="16" height="16" style="cursor:pointer;" title="Ver Foto del Participante" /></a>
          </div>
          <div class="box-info">
              <div class="text-info">
                <?php 
                  echo $row2['nombres'];
                  if($representa!="")
                  {
                    echo "<p style='font-size:8px;'>Representado a ".$representa."</p>";
                  }
                ?>
              </div>
              <div><b><?php echo $row2['firmofecha']; ?></b></div>
          </div>
      </div>
        <?php 
        } 
    ?>
  </div>
  </div>
</form>
</div>