<?php
/* ---------------------------------------------------------------
 * Aplicatiu: sms_gest. Programa de gestió de sms de GEIsoft
 * Fitxer: alum_act.php
 * Autor: VÃíctor Lino
 * Descripció: Actualitza i dóna d'alta els alumnes
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../pdo/bbdd/connect.php');
//include("./funcions/func_prof_alum.php");
//include("./funcions/funcions_generals.php");
ini_set("display_errors", 1);

$file = "buida_2.6.3.sql";
// ******* COPERNIC ***********
//$file = "GP1_finsFranges_ESO.sql";
//$file = "GP1_finsAlumnes_ESO.sql";
//$file = "GP1_complet_ESO.sql";
//$file = "GP1_finsFranges_CCFF.sql";
//$file = "GP2_finsGrups_CCFF_ESO.sql";
//$file = "GP2_Complet_CCFF_ESO.sql";
// ******* LLOBREGAT C1 C2***********
//$file = "GP1_Llobre_finsFranges_ESO.sql";
//$file = "GP1_Llobre_Complet_ESO.sql";
//$file = "GP2_Llobre_finsGrups_ESO_CCFF.sql";
//$file = "GP2_Llobre_finsFranges_ESO_CCFF.sql";
//$file = "GP2_Llobre_Complet_ESO_CCFF.sql";
//  ******* MIXT SORT ***********
//$file = "GP1_finsFranges_CCFF_ESO_SORT.sql";
// ******** PEÑALARA ************
//$file = "PN1_finsFranges_CCFF_ESO_LLANCA.sql";
//$file = "PN1_finsHoraris_CCFF_ESO_LLANCA.sql";
// ******** PEÑALARA C1 C2 SERRETA *******
//$file = "PN1_finsMateries_ESO_Serreta.sql";
//$file = "PN1_finsHoraris_ESO_Serreta.sql";
//$file = "PN1_Complet_ESO_Serreta.sql";
//$file = "PN2_finsMateries_ESO_CCFF_Serreta.sql";
//$file = "PN2_finsFranges_ESO_CCFF_Serreta.sql";
// ******** HORWIN *******************
////$file = "HW1_finsFranges_CCFF_ESO_GUINO.sql";
//
//
//
//$file = "KW1_finsFranges_CCFF_ESO_CASTELL.sql";
//$file = "ASC1_finsFranges_CCFF_ESO_ANDREUNIN.sql";
//$file = "ASC1_finsHoraris_CCFF_ESO_ANDREUNIN.sql";
//$file = "ASC1_finsAlumnes_CCFF_ESO_ANDREUNIN.sql";
// CALLIPOLIS
//$file ="callipolis_fins_franges.sql";

$filename = "/home/vlino/Dropbox/GEISoft_gassist/importacions\ centres/" . $file;
//$filename = "/home/vlino/Baixades/*****.sql";

$sql = "DROP DATABASE cooper_actual";
$result = $db->prepare($sql);
$result->execute();

$sql = "CREATE DATABASE cooper_actual";
$result = $db->prepare($sql);
$result->execute();

$cmd = "mysql -u root --password=vlino cooper_actual < $filename";
//echo $cmd;
exec($cmd);

die("<script>location.href = './index.php'</script>");
?>
</body>






