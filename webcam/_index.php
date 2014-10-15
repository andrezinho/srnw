<script type="text/javascript" src="jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="jquery.lightbox-0.5.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.lightbox-0.5.css" media="screen" />
<link rel="stylesheet" type="text/css" href="estilos.css" media="screen" />
<script type="text/javascript" src="webcam.js"></script>
<script language="JavaScript">
    webcam.set_api_url( 'test.php?kardex=K001212' );//PHP adonde va a recibir la imagen y la va a guardar en el servidor
    webcam.set_quality( 100 ); // calidad de la imagen
    webcam.set_shutter_sound( true ); // Sonido de flash
</script>
<script language="JavaScript">
		webcam.set_hook( 'onComplete', 'my_completion_handler' );		
		function do_upload() 
                {
                    // subir al servidor
                    document.getElementById('upload_results').innerHTML = '<h1>Cargando al servidor...</h1>';
                    webcam.upload();
		}
		
		function my_completion_handler(msg) 
                {
                    if (msg.match(/(http\:\/\/\S+)/)) {
                            var image_url = RegExp.$1;//respuesta de text.php que contiene la direccion url de la imagen
                            // Muestra la imagen en la pantalla
                            document.getElementById('upload_results').innerHTML = '<img src="' + image_url + '" width="400" height="225">';
                            // reset camera for another shot
                            webcam.reset();
                    }
                    else alert("PHP Error: " + msg);
		}
</script>
<div align="left" id="cuadro_camara" style="padding:10px;">    
    <div style="width:auto; float: left;padding:10px;">
        <div style="padding:5px; background: #CCC; ">
            <script language="JavaScript">
                document.write( webcam.get_html(800,450,800,450) );//dimensiones de la camara
            </script>
        </div>
        <div>
            <form>
		<input type=button value="Configurar" onClick="webcam.configure()" class="botones_cam">
		&nbsp;&nbsp;
		<input type=button value="Tomar foto" onClick="webcam.freeze()" class="botones_cam">
		&nbsp;&nbsp;
		<input type=button value="Reset" onClick="webcam.reset()" class="botones_cam">
            </form>
        </div>
    </div>
    <div style="width:400px; float: left; padding: 10px;">
        <div id="upload_results" style="height: 225px; padding: 5px; background: #ccc"></div>
        <div style="margin-top:5px;">
            <form>
                <a href="javascript:do_upload()" class="myButton">Grabar</a>                
            </form>
        </div>
    </div>
    <div id="" class="formulario" > </div>
    <div style="clear: both"></div>
</div>

<script type="text/javascript">
    $(function() {
        $('#gallery a').lightBox();//Galeria jquery
    });
    </script>
    <style type="text/css">
	/* jQuery lightBox plugin - Gallery style */
	#gallery {
		background-color: #444;
		width: 100%;
	}
	#gallery ul { list-style: none; }
	#gallery ul li { display: inline; }
	#gallery ul img {
		border: 5px solid #3e3e3e;
		border-width: 5px 5px 5px;
	}
	#gallery ul a:hover img {
		border: 5px solid #fff;
		border-width: 5px 5px 5px;
		color: #fff;
	}
	#gallery ul a:hover { color: #fff; }
	</style>