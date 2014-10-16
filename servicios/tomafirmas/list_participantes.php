<?php 
	include('../../config.php');
	$NumRegs = 0;
	$Id=$_GET['idk'];

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