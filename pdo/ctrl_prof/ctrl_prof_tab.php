<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   $strNoCache = "";
?>
    <div id="tt" class="easyui-tabs" border="true" style="width:auto;height:auto">
        <div id="prof_fan_tard" title="Professors que fan tard" style="padding:5px">
            <?php
            	include('../ctrl_prof/ctrl_prof_grid_in.php');
            ?>
        </div>
        
        <div id="prof_marxan_aviat" title="Professors que marxan aviat" style="padding:5px">
            <?php
            	include('../ctrl_prof/ctrl_prof_grid_out.php');
            ?>
        </div>       
    </div>
    
    <script type="text/javascript"> 
	
	    $('#tt').tabs({
		  plain:true,
		  onSelect: function(title){
		  	   $("#llistaProfessorsFanTard").load("./ctrl_prof/ctrl_prof_getdata_in.php");
			   $("#llistaProfessorsMarxanAviat").load("./ctrl_prof/ctrl_prof_getdata_out.php");
			   editIndex = undefined;
		  }
		});
   
	</script>