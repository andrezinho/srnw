<?php
    include('../../config.php');	
    include("../../libs/clasemantem.php");
    $Op       = $_GET["Op"];
    $mantem   = new dbMantimiento($Conn->GetConexion());
    $Sql      = $mantem->__dbMantenimiento($_POST, "form1", "kardex_tipo_notaria", $Op);
    $Conn->NuevaTransaccion();
    $Consulta = $Conn->Query($Sql);
    if (!$Consulta){
        $Conn->TerminarTransaccion("ROLLBACK");
        $Res  = 2;
        $Mensaje = "Error al intentar ".$Accion[$Op]." los datos del Kardex";
    }else{
        $Conn->TerminarTransaccion("COMMIT");
        $Res  = 1;
        $Mensaje = "Registro ".$Accion[$Op + 4]." Correctamente";
    }
?>
<script>
    OperMensaje('<?php echo $Mensaje?>',<?php echo $Res?>);
</script>