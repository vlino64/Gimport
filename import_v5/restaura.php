<?php
/*---------------------------------------------------------------
* Aplicatiu: sms_gest. Programa de gestió de sms de GEIsoft
* Fitxer: alum_act.php
* Autor: VÃíctor Lino
* Descripció: Actualitza i dóna d'alta els alumnes
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
require_once('../pdo/bbdd/connect.php');
include("../funcions/func_prof_alum.php");
include("../funcions/funcions_generals.php");
ini_set("display_errors", 1);

        $file = "buida_2.6.1.sql";
        $filename = "/home/vlino/Dropbox/GEISoft_gassist/importacions\ centres/".$file;
        //$filename = "/home/vlino/Baixades/*****.sql";
        
        $sql = "DROP DATABASE cooper_actual" ;
	$result=$db->prepare($sql);
        $result->execute();
        
        $sql = "CREATE DATABASE cooper_actual";
 	$result=$db->prepare($sql);
        $result->execute();      
        
        $cmd = "mysql -u ".USER." --password=".PASS." ".DB." < $filename";
        exec($cmd);
        
        die("<script>location.href = './index.php'</script>");

?>
</body>

	




