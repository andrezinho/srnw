<?php 
    include('../config.php');
    include('func.php');

    $dia = str_pad(date('d'),2,'0',0);
    $mes = str_pad(date('m'),2,'0',0);
    $anio = date('Y');

    if(isset($_GET['idarchivo']))
    {
        $Id = $_GET['idarchivo'];    
    }
    $sql = "SELECT a.nombre,a.contenido
            from editor.archivos as a              
            where a.idarchivo = ".$Id;
    
    $q = $Conn->Query($sql);
    $r = $Conn->FetchArray($q);
    $flag = false;
    if($r['contenido']!="")
    {
        $r['contenido'] = str_replace('“', '"', $r['contenido']);
        $r['contenido'] = str_replace('”', '"', $r['contenido']);
        $r['contenido'] = str_replace('–', '-', $r['contenido']);

        $plantilla = stripSlash(html_entity_decode($r['contenido'])); 
        $plantilla = str_replace('"Times New Roman"',"'Times New Roman'",$plantilla);
        $plantilla = str_replace('"times new roman"',"'Times New Roman'",$plantilla);
        $plantilla = utf8_decode($plantilla);  
    }
    else
    {
         $plantilla = '<div id="contenedor">
                          <div id="box-contenedor">                        
                          <div class="page">
                            <div class="write-page" >
                              <div>&nbsp;</div>
                            </div>
                          </div>
                        </div>
                      </div>';
          $plantilla = stripSlash($plantilla);
    }
?>
<html>
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
<link href="../css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="scripts.js"></script>
<link href="stylos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var fp = 'P';
var hhh = $(window).height()-145;
$(document).ready(function()
{  

    tinymce.init({
        selector: "textarea",     
        theme: "modern",
        height : hhh,
        language : "es",   
        browser_spellcheck : true,   
        convert_fonts_to_spans: true, 
        content_css : "estilos.css",  
        fontsize_formats: "10pt=13px 11pt=15px 12pt=16px 13pt=17px 13.5pt=18px 14pt=19px",
        style_formats: [                
                    {title: '16', inline: 'span', styles: {fontSize: '22px'}},                                
                    {title: '20', inline: 'span', styles: {fontSize: '26px'}},
                    {title: '22', inline: 'span', styles: {fontSize: '29px'}},
                    {title: '24', inline: 'span', styles: {fontSize: '32px'}},
                    {title: '26', inline: 'span', styles: {fontSize: '35px'}},
                    {title: '28', inline: 'span', styles: {fontSize: '37px'}},
                    {title: '32', inline: 'span', styles: {fontSize: '42px'}},
            ],
        
        plugins: [
            "advlist autolink lists link image charmap hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save autosave table contextmenu directionality",
            "emoticons template paste moxiemanager print tabfocus textcolor colorpicker textpattern fullpage "
        ],
        menubar: "file tools table format view insert edit",
        toolbar1: "save | fontselect styleselect fontsizeselect | undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image | forecolor backcolor | print | searchreplace |",    
        templates: [
            {title: 'Test template 1', content: 'Test 1'},
            {title: 'Test template 2', content: 'Test 2'}
        ],
        init_instance_callback : "call_back_function"
    });
    $("#print").click(function(){
        myPrint();
    });
    $("#grabar").click(function(){
        saveKardex();
    });
    $("#templates").dialog({
        title:'Escrituras Realizadas',
        modal:true,
        autoOpen: false,
        width: 330,
        buttons: {'Cerrar': function(){$(this).dialog('close')}}
    });    
    $("#modelo").click(function(){
        $("#templates").dialog('open');
    });
    $("#format_page").change(function(){
        var myfp = $(this).val();
        formatPage(myfp);
    })
 
});
function saveKardex()
{
    var idarchivo = $("#idarchivo").val(),
            cont = $("#content_ifr").contents().find("#tinymce").html(),
            params = { 
                        'idarchivo':idarchivo,
                        'cont':cont
                     },
            str = jQuery.param(params);

        $("#msg").css("display","inline");
        $.post('save_editor.php',str,function(data){            
            $("#msg").css("display","none");
            if(data!='1')
            {
                alert("HA OCURRIDO UN ERROR: "+data+"; S.O.S Sistemas");
            }
        });
}
function myPrint()
{
    var ida = $("#idarchivo").val();
    window.open('../eweb/print.php?idarchivo='+ida,'width=600,height=300');
}
function call_back_function()
{
  $("#content_ifr").contents().find("body").attr("contenteditable","false");  
  $("#content_ifr").contents().find("body").css("margin","0");  
  $("#content_ifr").contents().find(".page").attr("contenteditable","true");
  $("#content_ifr").contents().find(".page").select();
}
</script>
<body style="font-size:65%;background:#FAFAFA; margin:0; padding:0px">
<form method="post" action="index.php" name="frm" id="frm">
    <div style="width:100%; padding:5px 0; background:url('../png/green.png') repeat-x">
        <div style="padding:0 6px;">
        <span style="width:300px; color:#FFFFFF; font-size:12px; margin: 0px 10px 0 0; ">DOCUMENTO: <b><?php echo $r['nombre']; ?></b>  <?php if($_GET['template']!=""){ echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Usando el Modelo de ".$_GET['template']." del a&ntilde;o ".$_GET['anio']."]"; } ?></span>
        <span id="msg" style="font-size:11px; color:#fff; display:none">Guardando cambios...</span>        
        <a class="btn close" href="javascript:window.close();" style="float:right;">CERRAR</a>
        <a class="btn config" href="javascript:" id="config-page" style="float:right;">CONFIGURAR PAGINA</a>  
        <span style="float:right; padding:0 20px">
          <label style="color:#FFF">CARGAR PLANTILLA: </label>
          <input type="text" name="templat" id="templat" value="<?php echo $_GET['template'] ?>" style="width:80px" class="ui-widget-content ui-corner-all text" maxlength="7" />
          <input type="hidden" name="templat_anio" id="templat_anio" value="<?php echo $_GET['anio'] ?>" class="ui-widget-content ui-corner-all text"  />
          <input type="button" name="reload" id="reload" value="Cargar" />
        </span>        
        <input type="hidden" name="idarchivo" id="idarchivo" value="<?php echo $Id; ?>" />
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="info"></div>
    <textarea name="content" style="width:100%;">
        <?php   
            echo $plantilla;
        ?>
    </textarea>
</form>
</div>
<div id="margin">
    <label for="margen-left" style="width:80px; display:inline-block; text-align:right; font-size:12px">Izquierdo: </label><input type="text" name="margen-left" id="margen-left" class="ui-widget-content ui-corner-all" value="" size="3" /> cm.
    <label for="margen-top" style="width:60px; display:inline-block; text-align:right;font-size:12px">Arriba: </label><input type="text" name="margen-top" id="margen-top" class="ui-widget-content ui-corner-all" value="" size="3" readonly="" /> cm.
    <br/>
    <label for="margen-right" style="width:80px; display:inline-block; text-align:right;font-size:12px">Derecho: </label><input type="text" name="margen-right" id="margen-right" class="ui-widget-content ui-corner-all" value="" size="3" /> cm.
    <label for="margen-buttom" style="width:60px; display:inline-block; text-align:right;font-size:12px">Abajo: </label><input type="text" name="margen-buttom" id="margen-buttom" class="ui-widget-content ui-corner-all" value="" size="3" readonly="" /> cm.
    <br/>
    <select name="format_page" id="format_page">
        <option value="P">Vertical</option>
        <option value="L">Horizontal</option>
    </select>
</div>
<div id="templates">
    
</div>
</body>
</html>