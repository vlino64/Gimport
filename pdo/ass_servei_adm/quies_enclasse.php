<?php
   //session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   
   $strNoCache     = "";
   $modul_reg_prof = getModulsActius($db)["mod_reg_prof"];
   $retard_refresc = isset($_REQUEST['rf']) ? $_REQUEST['rf'] : 1000 ;
?>
        
<table width="99%" cellpadding="0" cellspacing="0">
<tr>
<td>
	<h2>Qui est&agrave; en classe?</h2>
</td>
</tr>
<tr>
<td align="right">
    <?php
      if ($modul_reg_prof) {
         echo '<img src="./images/block_orange.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;Al centre';
      }
    ?>
    <img src="./images/block_green.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;En classe
    <img src="./images/block_yellow.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;De sortida
    <img src="./images/block_red.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;Abs&egrave;ncia comunicada
    <img src="./images/block_blue.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;Prof. gu&agrave;rdia passa llista
    <img src="./images/block_white.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;No est&agrave; en classe
</td>
</tr>
</table>

<div id="dlg_assist" class="easyui-dialog" style="width:900px;height:600px;"  
            maximized="true" maximizable="true" closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_assist-toolbar">  
</div>
        
<div id="dlg_assist-toolbar">
    <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_assist').dialog('refresh')">Recarregar</a>
    <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="tancarAssist()">Tancar</a>  
</div>
         
<div id="llistaClasses" class="llistaclasses">  

</div>
    
<style type="text/css">  
        .llistaclasses{  
            /*background:#fafafa;*/
        }  
        .llistaclasses ul{
                list-style:none;  
                margin:0;  
                padding:0px; 
        }  
        .llistaclasses li{  
                display:inline;  
                float:left;  
		width:183px;
		height:135px;
                margin:1px;  
		border:1px dashed #ccc;
		padding-left:0px;
		padding-top:1px;
		overflow: hidden;
                text-align: top;
        }  
        .item{  
                display:block;
		float:left; 
                text-decoration:none;
		background-color: whitesmoke;
		color: #666;
		margin:1px;
		height: auto;
		width:auto;
		overflow: hidden;
                text-align: top;
        } 
	.itemclase{  
                display:block;
		float:left; 
                text-decoration:none;
		background-color:#a1d88b;
		color: #009a49;
		margin:1px;
		height: auto;
		width:auto;
		height:auto;
		overflow:auto;
        }
	.itemguardia{  
                display:block;
                float:left; 
                text-decoration:none;
		background-color:#6eaff2;
		color: #004c94;
		margin:1px;
		height: auto;
		width:auto;
		height:auto;
		overflow:auto;
        }
	.itemabsencia{  
                display:block;
            	float:left; 
                text-decoration:none;
		background-color:#a70e11;
		color: #fff;
		margin:1px;
		height: auto;
		width:auto;
		height:auto;
		overflow:auto;
        }
	.itemsortida{  
                display:block;
		float:left; 
                text-decoration:none;
		background-color:#ffcb00;
		color: #009a49;
		margin:1px;
		height: auto;
		width:auto;
		height:auto;
		overflow:auto;
        }
        .itemcentre{  
                display:block;
                float:left; 
                text-decoration:none;
		background-color:#F0780E;
		color: #fff;
		margin:1px;
		height: auto;
		width:auto;
		height:auto;
		overflow:auto;
        }
        .item img{  
             
        }  
        .item p{  
                margin:0;
                text-align:left;  
                color: #777;  
                margin:1px;
        }
	.clear {
                clear:both;
                height:1px;
                overflow:hidden;
	}
    </style>   
     
    
        <script type="text/javascript">
	$(document).ready(function(){
            setInterval(function() {
		$("#llistaClasses").load("./ass_servei_adm/llista_classes_actuals.php?hora=$hora");
            }, <?=$retard_refresc?>);
	});  
	
	function doAssist(idprofessors,idgrups,idmateria,idfranges_horaries,idespais_centre){
	    url = './guard/guard_assist_grid.php?act=0&idprofessors='+idprofessors+'&idgrups='+idgrups+'&idmateria='+idmateria+'&idfranges_horaries='+idfranges_horaries+'&idespais_centre='+idespais_centre;
            
            $('#dlg_assist').dialog('open').dialog('setTitle','Passar llista professor de gu&agrave;rdia');
            $('#dlg_assist').dialog('refresh', url);
	}
	
	function tancarAssist() {
	    javascript:$('#dlg_assist').dialog('close');
	}
	
	</script>