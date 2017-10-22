<?php
   //session_start();
   require_once('./bbdd/connect.php');
   require_once('./func/constants.php');
   require_once('./func/generic.php');
   require_once('./func/seguretat.php');
   
   $modul_reg_prof = getModulsActius($db)["mod_reg_prof"];
   $strNoCache = "";
?>        
        
<table width="99%" cellpadding="0" cellspacing="0">
<tr>
<td>
	<h2>Qui est&agrave; de gu&agrave;rdia?</h2>
</td>
</tr>
<tr>
<td align="right">
    <?php
      if ($modul_reg_prof) {
         echo '<img src="./images/block_orange.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;Al centre';
      }
    ?>
    <img src="./images/block_green.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;En gu&agrave;rdia
    <img src="./images/block_yellow.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;De sortida
    <img src="./images/block_red.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;Abs&egrave;ncia comunicada
    <img src="./images/block_white.png" width="25" height="15" style="border:1px dashed #ccc" />&nbsp;No est&agrave; en gu&agrave;rdia
</td>
</tr>
</table>    
    
     
<div id="llistaGuardies" class="llistaguardies">  

</div>  
    
   
    <style type="text/css">  
        .llistaguardies{  
            /*background:#fafafa;*/
        }  
        .llistaguardies ul{
            list-style:none;  
            margin:0;  
            padding:0px;  
        }  
        .llistaguardies li{  
            display:inline;  
            float:left;  
            width:183px;
            height:115px;
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
				$("#llistaGuardies").load("./ass_servei/llista_guardies_actuals.php");
			}, 500);
	});	
	</script>