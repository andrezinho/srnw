<?php
if(!session_id()){session_start();}
include('../../config.php');
include("../../libs/clasemantem.php");

$Op = $_GET["Op"];
$participantes = json_decode($_POST['participantes']);

function search_conyuge($idp)
{
    global $participantes;    
    $p = false;
    for($k=0;$k<$participantes->nitem;$k++)
     {                          
        if($participantes->estado[$k])
        {
           if($idp==$participantes->conyuge[$k])
              $p = true;
        }
    }
    return $p;
}

$mantem = new dbMantimiento($Conn->GetConexion());
$Sql = $mantem->__dbMantenimiento($_POST, "form1", "kardex", $Op);	//Se genera la sentencia SQL de acuerdo a la operación	
$NroKardex = $_POST['0form1_correlativo'];	
if ($Op==2)
{		
    $Sql = "UPDATE kardex SET estado='2' WHERE idkardex='".$_POST['1form1_idkardex']."' AND idnotaria='".$_SESSION['notaria']."' ";
}
if ($Op==3)
{
    $Sql = "UPDATE kardex SET estado='0' WHERE idkardex='".$_POST['1form1_idkardex']."' AND idnotaria='".$_SESSION['notaria']."' ";
}	
$Conn->NuevaTransaccion();	
$SqlCo = "SELECT kardex_tipo_notaria.actual, kardex_tipo_notaria.idkardex_tipo FROM servicio INNER JOIN kardex ON (servicio.idservicio = kardex.idservicio) INNER JOIN kardex_tipo_notaria ON (servicio.idkardex_tipo = kardex_tipo_notaria.idkardex_tipo) WHERE kardex_tipo_notaria.idnotaria='".$_SESSION["notaria"]."' AND kardex.idservicio='".$_POST['0form1_idservicio']."' AND kardex_tipo_notaria.anio='".$_POST['0form1_anio']."' ";	
$ConsultaCo = $Conn->Query($SqlCo);
$rowCo = $Conn->FetchArray($ConsultaCo);
$Actual = $rowCo[0];
$KTipo = $rowCo[1];	
$SqlCo2 = "SELECT escritura FROM kardex WHERE idkardex='".$_POST['1form1_idkardex']."' AND idnotaria='".$_SESSION['notaria']."' ";
$ConsultaCo2 = $Conn->Query($SqlCo2);
$rowCo2 = $Conn->FetchArray($ConsultaCo2);	
if ($rowCo2[0]=='')
{
    if ($Actual<(int)$_POST['0form1_escritura'])
    {
        $ConsultaKT = $Conn->Query($SqlKT);
    }
}
$Consulta = $Conn->Query($Sql);
if (!$Consulta)
    {	
        $Conn->TerminarTransaccion("ROLLBACK"); 
        $Res=2;
        $Mensaje ="Error al intentar ".$Accion[$Op]." los datos de la Carta";
    }
    else
    {
        

        $item = $participantes->nitem;
        $str_idp = "0";
        $slq_set = "";
        for($i=0;$i<$item;$i++)
        {
                if($participantes->estado[$i]==true&&search_conyuge($participantes->idparticipante[$i])!=true)
                {
                    /*
                        Lista inicial 1,2,3,4
                        Lista nueva 1,3,4,5

                        para x = 1,3,4,5
                         si x existe => update
                          sino => insert
                        fin

                        eliminar todos los participantes menos los que estan en (1,3,4,5)
                    */
                    $str_idp .= ",".$participantes->idparticipante[$i];

                    $sql = "SELECT count(*) from kardex_participantes 
                            where idkardex = ".$_POST['1form1_idkardex']." and idparticipante = ".$participantes->idparticipante[$i];
                    $q = $Conn->Query($sql);
                    $n1 = $Conn->FetchArray($q);

                    if($n1[0]>0)
                    {
                        //Update
                        $sql = "UPDATE kardex_participantes set 
                                        idparticipacion = ".$participantes->idparticipacion[$i].",
                                        porcentage='".$participantes->porcentage[$i]."',
                                        idrepresentado=".$participantes->idrepresentado[$i].",
                                        conyuge=".$participantes->conyuge[$i].", tipo=".$participantes->tipo[$i].", idzona='".$participantes->idzona[$i]."', zona='".$participantes->zona[$i]."' 
                                WHERE idkardex = ".$_POST['1form1_idkardex']." and idparticipante = ".$participantes->idparticipante[$i];                      
                        $Conn->Query($sql);
                    }
                    else
                    {                        
                        $sql = "INSERT INTO kardex_participantes(
                                        idkardex, idparticipante, idparticipacion, firmo, firmofecha, 
                                        foto, porcentage, idrepresentado, conyuge, 
                                        tipo,partida,idzona,zona)
                                VALUES (".$_POST['1form1_idkardex'].", 
                                        ".$participantes->idparticipante[$i].", 
                                        ".$participantes->idparticipacion[$i].", 
                                        0,
                                        NULL,
                                        '',
                                        '".$participantes->porcentage[$i]."',
                                        ".$participantes->idrepresentado[$i].",
                                        ".$participantes->conyuge[$i].",
                                        ".$participantes->tipo[$i].",
                                        '".$participantes->partida[$i]."',
                                        '".$participantes->idzona[$i]."',
                                        '".$participantes->zona[$i]."');";
                        $Consulta2 = $Conn->Query($sql);
                    }

                    if($participantes->idrepresentado[$i])
                    {
                        $slq_set .= "UPDATE kardex_participantes 
                                        set representado = 1
                                    where idkardex = ".$_POST['1form1_idkardex']." 
                                        and idparticipante = ".$participantes->idrepresentado;                        
                    }
                }
            }
            if($sql_set!="")
            {
                $Conn->Query($sql_set);
            }
            if ($Op!=4)
            {
                $SQLDelete = "DELETE FROM kardex_participantes WHERE idkardex='".$_POST['1form1_idkardex']."' and idparticipante not in (".$str_idp.") ";
                $result = $Conn->Query($SQLDelete);
                if (!$result) {die("Error in SQL query: ");}

                $SQLDelete = "DELETE FROM detalle_forma_pago WHERE idkardex='".$_POST['1form1_idkardex']."' ";
                $result = $Conn->Query($SQLDelete);
                if (!$result) {die("Error in SQL query: ");}
            }	

    $Cont = $_POST["ConMedioPago"];
    for ($i=1; $i<=$Cont; $i+= 1){			
        if (isset($_POST["0formX".$i."_idkardex"])){	
            $nPost = array();
            $FormN = "formX".$i;
            foreach($_POST as $ind=>$val){
                if(stripos($ind, $FormN.'_')!==false){
                    $nPost[$ind] = $val;
                    if ($ind=='0formX'.$i.'_idkardex'){
                        $nPost[$ind] = $_POST['1form1_idkardex'];
                    }
                }
            }
            $mantem    = new dbMantimiento($Conn->GetConexion());
            $Sql2      = $mantem->__dbMantenimiento($nPost, $FormN, "detalle_forma_pago", 0);	//Se genera la sentencia SQL de acuerdo a la operación
            $Consulta2 = $Conn->Query($Sql2);
        }
    }
    $SQLDELETEBIEN="DELETE FROM kardex_bien WHERE idkardex={$_POST['1form1_idkardex']}";
    $consultaDELETE=$Conn->Query($SQLDELETEBIEN);
    $ContBienes = $_POST["ConBienes"];
    for ($i=1; $i<=$ContBienes; $i++){			
        if (isset($_POST["0formB".$i."_idkardex"]))
            {	
                $nPost = array();
                $FormN = "formB".$i;
                foreach ($_POST as $key => $value) 
                {
                    if(stripos($key, $FormN.'_'))
                    {
                        $nPost[$key]=$value;
                        if ($key=='0formB'.$i.'_idkardex')
                        {
                            $nPost[$key] = $_POST['1form1_idkardex'];
                        }
                    }
                }
                $mantem    = new dbMantimiento($Conn->GetConexion());
                $Sql2      = $mantem->__dbMantenimiento($nPost, $FormN, "kardex_bien", 0);	//Se genera la sentencia SQL de acuerdo a la operación
                $Consulta2 = $Conn->Query($Sql2);
                if(!$Consulta2)
                {
                    $Conn->TerminarTransaccion("ROLLBACK");
                    $Res=2;
                    $Mensaje ="Error al intentar ".$Accion[$Op]." los Datos del Bien .<br/>Detalle de Error:".$Sql2;
                    $i=$ContBienes;
                }
            
            }
    }
    if ($_POST['0form1_firmado']==1){
        $mantem = new dbMantimiento($Conn->GetConexion());
        $Sql3 = "UPDATE kardex SET estado='2' WHERE idkardex='".$_POST['1form1_idkardex']."' AND idnotaria='".$_SESSION['notaria']."' ";
        $Consulta2 = $Conn->Query($Sql3);
    }else{
        $mantem = new dbMantimiento($Conn->GetConexion());
        $Sql3 = "UPDATE kardex SET estado='1' WHERE idkardex='".$_POST['1form1_idkardex']."' AND idnotaria='".$_SESSION['notaria']."' ";
        $Consulta2 = $Conn->Query($Sql3);
    }
    /*** Agregado Para la digitación ***/
    $Actualizar = $Conn->Query("UPDATE kardex SET correlativo='".$_POST['0form1_correlativo']."' WHERE idkardex='".$_POST['1form1_idkardex']."' ");
    $Atencion = $Conn->Query("SELECT * FROM kardex WHERE idkardex='".$_POST['1form1_idkardex']."' ");
    $FillAtencion = $Conn->FetchArray($Atencion);
    $Actualizar = $Conn->Query("UPDATE atencion_detalle SET correlativo='".$_POST['0form1_correlativo']."' WHERE idatencion='".$FillAtencion[1]."' ");
    /***********************************/
    
    $Conn->TerminarTransaccion("COMMIT");
    $Res=1;
    $Mensaje ="Registro ".$Accion[$Op + 4]." Correctamente";
}
?>
<script>
    OperMensaje('<?php echo $Mensaje;?>',<?php echo $Res;?>);
</script>