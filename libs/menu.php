<?php 
	$IdUsuario 	= $_SESSION["id_user"];
	$Usurio		= isset($_SESSION["Usuario"])?$_SESSION["Usuario"]:'';
	$Ruta 		= $_SESSION["Ruta"];
	$IdPerfil	= $_SESSION["IdPerfil"];
	$IdSistema	= $_SESSION["IdSistema"];	
	$UrlBase 	= $Ruta;	
	$Servidor2 	= $_SESSION["db_Servidor"];
	$Puerto2 	= $_SESSION["db_Puerto"];
	$Usuario2 	= $_SESSION["db_Usuario"];
	$Password2 	= $_SESSION["db_PassWord"];
	$Base2 		= "seguridad2";

	$conectar2 	= pg_connect("host=$Servidor2 port=$Puerto2 password=$Password2 user=$Usuario2 dbname=$Base2");
	if (pg_ErrorMessage($conectar2)) { echo "<p><b>Ocurrio un error conectando a la base de datos: .</b></p>"; exit; }
	$Sesion = isset($_GET["sesion"])?$_GET["sesion"]:0;
	if($Sesion==1){$_SESSION["Activo"]=0;$Activo=0;}else{$Activo=$_SESSION["Activo"];}
	$d="";
?>
<script>
	var cmThemeOffice={
	mainFolderLeft:"&nbsp;",
	mainFolderRight:"&nbsp; |",
	mainItemLeft:"&nbsp;",
	mainItemRight:"&nbsp;",
	folderLeft:'<img alt="" src="<?php echo $UrlBase;?>imagenes/spacer.png">',
	folderRight:'<img alt="" src="<?php echo $UrlBase;?>imagenes/arrow.png">',
	itemLeft:'<img alt="" src="<?php echo $UrlBase;?>imagenes/spacer.png">',
	itemRight:'<img alt="" src="<?php echo $UrlBase;?>imagenes/blank.png">',
	mainSpacing:0,subSpacing:0,delay:500};
	var cmThemeOfficeHSplit=[_cmNoAction,'<td class="ThemeOfficeMenuItemLeft"></td><td colspan="2"><div class="ThemeOfficeMenuSplit"></div></td>'];
	var cmThemeOfficeMainHSplit=[_cmNoAction,'<td class="ThemeOfficeMainItemLeft"></td><td colspan="2"><div class="ThemeOfficeMenuSplit"></div></td>'];
	var cmThemeOfficeMainVSplit=[_cmNoAction,"&nbsp;"];
</script>
<style type="text/css">
  a{text-decoration: none;color: #000}
</style>
<table width="98%" class="menubar" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td class="menubackgr">
<?php
        if($Activo==1){
?>
    <div id="myMenuID"></div>
	<script>
		var myMenu =
		[
			<?php
					$SQL = "SELECT Mu.idmodulo,M.descripcion,M.url FROM modulos_perfil Mu, modulos M WHERE Mu.idmodulo=M.idmodulo AND M.estado=1 AND M.idpadre=0 AND Mu.idperfil='$IdPerfil' AND Mu.idsistema='$IdSistema' ORDER BY M.orden ASC";

					$Consulta = pg_query($conectar2, $SQL);
					while($row=pg_fetch_array($Consulta)){
			?>
					[null,'<a href="<?php echo $row[2];?>"><?php echo $row[1];?></a>','',null,''
                        <?php
						$SQL1 = "SELECT Mu.idmodulo,M.descripcion,M.url,M.imagen FROM modulos_perfil Mu, modulos M WHERE Mu.idmodulo=M.idmodulo AND M.estado=1 AND M.idpadre='".$row[0]."' AND Mu.idperfil='$IdPerfil' AND Mu.idsistema='$IdSistema' ORDER BY M.orden ASC";
						$Consulta1 = pg_query($conectar2,$SQL1);
						while($row1=pg_fetch_array($Consulta1)){
							$Imagen1 = "http://".$_SERVER['HTTP_HOST']."/seguridad/catalogos/modulos/imagenes/".$row1[3];
							$Imagen = '<img src="'.$Imagen1.'" width="19" height="19"/>';
                        ?>
						,['<?php echo !empty($row1[3])?$Imagen:'';?>','<?php echo $row1[1];?>','<?php echo $UrlBase.$row1[2];?>',null,''
                        <?php
								$SQL2 = "SELECT Mu.idmodulo, M.descripcion, M.url, M.imagen FROM modulos_perfil Mu, modulos M WHERE Mu.idmodulo=M.idmodulo AND M.estado=1 AND M.idpadre='".$row1[0]."' AND Mu.idperfil='$IdPerfil' AND Mu.idsistema='$IdSistema' ORDER BY M.orden ASC";
								$Consulta2	= pg_query($conectar2,$SQL2);
								while($row2=pg_fetch_array($Consulta2)){
									$Imagen2 = "http://".$_SERVER['HTTP_HOST']."/seguridad/catalogos/modulos/imagenes/".$row2[3];
									$Imagen2 = '<img src="'.$Imagen2.'" width="19" height="19"/>';
							?>
								,['<?php echo !empty($row2[3])?$Imagen2:'';?>','<?php echo $row2[1];?>','<?php echo $UrlBase.$row2[2];?>',null,'']
							<?php }?>												
						]
					<?php }?>				
					],
			<?php 
					}
			?>
			];
			cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script>
        <?php 
        $d = "/ <a href='?sesion=1' style='color: #333333; font-weight: bold'>Cambiar de Modulo</a>";
        } ?>
</td>
    <td class="menubackgr" align="right">
        <div id="wrapper1"></div>
	</td>
    <td class="menubackgr" align="right"></td>
  </tr>
</table>
