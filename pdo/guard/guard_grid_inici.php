<?php
   //session_start();
   require_once('./bbdd/connect.php');
   require_once('./func/constants.php');
   require_once('./func/generic.php');
   require_once('./func/seguretat.php');
   $db->exec("set names utf8");
   
   $hora = strtotime(isset($_REQUEST['hora']) ? $_REQUEST['hora'] : date('H:i'));
   $_SESSION['hora_guardia'] = date("H:i");
   $idprofessors  = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0;
   $strNoCache = "";
   
   if (validEntryLogProfessor($db,$idprofessors,TIPUS_ACCIO_ENTRAGUARDIA)) {
	insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_ENTRAGUARDIA);
   } 
?>
    <div id="tt" class="easyui-tabs" border="true" style="width:auto;height:auto">

        <div id="classes_actual" title="Qui est&agrave; en classe" style="padding:5px">
            <?php
            	include('./ass_servei/quies_enclasse.php');
            ?>
        </div>
        
        <div id="guardies_actual" title="Qui est&agrave; de gu&agrave;rdia" style="padding:5px">
            <?php
            	include('./ass_servei/quies_enguardia.php');
            ?>
        </div>
        
        
        
    </div>
        
    <script type="text/javascript"> 
	
	 $('#tt').tabs({
		  plain:true,
		  onSelect: function(title){
		  	   $("#llistaClasses").load("./ass_servei/llista_classes_actuals.php");
			   $("#llistaGuardies").load("./ass_servei/llista_guardies_actuals.php");
			   //$("#llistaProfessorsAbsents").load("./ass_servei/llista_professors_absents.php");
			   editIndex = undefined;
		  }
		});
   
    </script>