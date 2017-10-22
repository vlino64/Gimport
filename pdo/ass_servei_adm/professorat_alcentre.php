<?php
   //session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   //require_once('../func/seguretat.php');
   
   $strNoCache = "";
?>        
        
<table width="99%" cellpadding="0" cellspacing="0">
<tr>
<td>
	<h2>Professorat al centre</h2>
</td>
</tr>
<tr>
<td align="right">
    <img src="./images/block_green.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;En classe&nbsp;&nbsp;
    <img src="./images/block_yellow.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;De gu&agrave;rdia&nbsp;
    <img src="./images/block_white.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;Al centre&nbsp;
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
         
<div id="llistaProfessorsAlCentre" class="llistaprofessorsalcentre">  

</div>  

    <style type="text/css">  
        .llistaprofessorsalcentre{  
            /*background:#fafafa;*/
        }  
        .llistaprofessorsalcentre ul{
            list-style:none;  
            margin:0;  
            padding:0px;  
        }  
        .llistaprofessorsalcentre li{  
            display:inline;  
            float:left;  
			width:183px;
			height:245px;
            margin:1px;  
			border:1px dashed #ccc;
			padding-left:0px;
			padding-top:1px;
			overflow: hidden;
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
			overflow:auto;
        }
		.itemguardia{  
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
	.registre_sortida {
            width:100%;
            background-color:#a70e11;
            color: #fff;
	}
    </style>  
    
    <script type="text/javascript">
	$(document).ready(function(){
			setInterval(function() {
				$("#llistaProfessorsAlCentre").load("./ass_servei/llista_professors_alcentre.php");
			}, 5000);
	});
    </script>