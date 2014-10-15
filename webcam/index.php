<?php include('../config.php'); ?>
<html>
<head>
<script type="text/javascript" src="jquery-1.6.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="estilos.css" media="screen" />
<script type="text/javascript" src="webcam.js"></script>
</head>
<?php
 $idkardex = $_GET['idk'];
 $idparticipante = $_GET['idp'];

 //Obtenemos los datos
 $sql = "SELECT kardex_participantes.idkardex, 
                    case documento.iddocumento when 1 then 'D.N.I'
                        else documento.descripcion end, 
                    kardex_participantes.idparticipante, 
                    cliente.dni_ruc, 
                    cliente.nombres||' '||coalesce(cliente.ape_paterno,'')||' '||coalesce(cliente.ap_materno,'') as nombres, 
                    kardex_participantes.idparticipacion, 
                    participacion.descripcion as par, 
                    kardex_participantes.firmo,
                    kardex.correlativo
             FROM cliente 
                    ";
  if($_GET['c']=='1')             
  {
    $sql .= " INNER JOIN kardex_participantes ON (cliente.idcliente = kardex_participantes.conyuge) 
                    INNER JOIN participacion ON (kardex_participantes.idparticipacion = participacion.idparticipacion) 
                    INNER JOIN documento ON (cliente.iddocumento = documento.iddocumento) 
                    INNER JOIN kardex on kardex.idkardex = kardex_participantes.idkardex 
             WHERE kardex_participantes.idkardex='$idkardex' and kardex_participantes.conyuge = ".$idparticipante;
  }
  else
  {
    $sql .= " INNER JOIN kardex_participantes ON (cliente.idcliente = kardex_participantes.idparticipante) 
                    INNER JOIN participacion ON (kardex_participantes.idparticipacion = participacion.idparticipacion) 
                    INNER JOIN documento ON (cliente.iddocumento = documento.iddocumento) 
                    INNER JOIN kardex on kardex.idkardex = kardex_participantes.idkardex 
             WHERE kardex_participantes.idkardex='$idkardex' and kardex_participantes.idparticipante = ".$idparticipante;
  }
  
$q = $Conn->Query($sql);
$row = $Conn->FetchArray($q);

?>
<div style="color:#FFFFFF; text-align:left; padding:5px 30px; font-family:arial">
<b>KARDEX: <?php echo $row['correlativo']; ?> </b><br/>
PARTICIPANTE: <?php echo $row['nombres']; ?> &nbsp;&nbsp;&nbsp;&nbsp; NRO DOCUMENTO: <?php echo $row['dni_ruc']; ?> &nbsp;&nbsp;&nbsp;&nbsp;
PARTICIPACION: <?php echo $row['par'] ?>
</div>
<script language="JavaScript">
    webcam.set_api_url( 'test.php?kardex=<?php echo $row['correlativo']; ?>&c=<?php echo $row['c'] ?>&idp=<?php echo $idparticipante ?>&idkardex=<?php echo $idkardex; ?>&nombres=<?php echo $row['nombres'] ?>' );//PHP adonde va a recibir la imagen y la va a guardar en el servidor
    webcam.set_quality( 100 ); // calidad de la imagen
    webcam.set_shutter_sound( true ); // Sonido de flash
	webcam.set_hook( 'onComplete', 'my_completion_handler' );		
	function do_upload() 
    {
        document.getElementById('upload_results').innerHTML = '<h1>Cargando al servidor...</h1>';
        webcam.upload();
	}
	function my_completion_handler(msg) 
    {
        if (msg.match(/(http\:\/\/\S+)/)) 
        {
            var image_url = RegExp.$1;//respuesta de text.php que contiene la direccion url de la imagen                    
            document.getElementById('upload_results').innerHTML = '<img src="' + image_url + '" width="320" height="240">';                    
            webcam.reset();
            opener.getParticipantes(<?php echo $idkardex; ?>);
        }
        else alert("PHP Error: " + msg);
	}

</script>
<body>
<div align="left" id="cuadro_camara" style="padding:10px;">    
    <div style="width:auto; float: left;padding:10px;">
        <div style="padding:5px; background: #444; ">
            <script language="JavaScript">
                document.write( webcam.get_html(640,480,640,480) );//dimensiones de la camara
            </script>
        </div>
        <div style="padding:5px 0">
            <form>				
                <a href="javascript:webcam.configure()" class="myButton">Configurar</a>                
                <a href="javascript:webcam.reset()" class="myButton">Reset</a>                
                <a href="javascript:webcam.freeze()" class="myButton">Tomar Foto</a>          
                <a href="javascript:do_upload()" class="myButton">Grabar</a>                
            </form>
        </div>
    </div>
    <div style="width:330px; float: left; padding: 10px;">
        <div id="upload_results" style="height: 240px; padding: 5px; background: #444"></div>
        <div style="margin-top:5px;"></div>
    </div>
    <div id="" class="formulario" > </div>
    <div style="clear: both"></div>
</div>
</body>
</html>