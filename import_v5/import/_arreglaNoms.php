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
require_once('../../bbdd/connect.php');
include("../funcions/func_prof_alum.php");
include("../funcions/funcions_generals.php");

$camps=array();
recuperacampdedades($camps,$db);

$sql="SELECT idalumnes FROM alumnes ";
//echo $sql."<br>";
$result=mysql_query($sql);if (!$result) {die(_ERR_SELECT_ALUM . mysql_error());}
while ( $fila = mysql_fetch_row($result))
    {
    $sql = "SELECT Valor FROM contacte_alumne WHERE id_tipus_contacte =".$camps[cognom1_alumne]." AND id_alumne =". $fila[0].";";
    $result2=mysql_query($sql);if (!$result2) {die(_ERR_SELECT_ALUM_COG1 . mysql_error());}
    $fila2 = mysql_fetch_row($result2);
    $cognom1 = $fila2[0];
    
    $sql = "SELECT Valor FROM contacte_alumne WHERE id_tipus_contacte =".$camps[cognom2_alumne]." AND id_alumne =". $fila[0].";";
    $result2=mysql_query($sql);if (!$result2) {die(_ERR_SELECT_ALUM_COG2 . mysql_error());}
    $fila2 = mysql_fetch_row($result2);
    $cognom2 = $fila2[0];
    
    $sql = "SELECT Valor FROM contacte_alumne WHERE id_tipus_contacte =".$camps[nom_alumne]." AND id_alumne =". $fila[0].";";
    $result2=mysql_query($sql);if (!$result2) {die(_ERR_SELECT_ALUM_NOM . mysql_error());}
    $fila2 = mysql_fetch_row($result2);
    $nom = $fila2[0];
    
    $nomComplet = $cognom1." ".$cognom2.", ".$nom;
    
    $sql2 = "UPDATE contacte_alumne SET Valor = '".$nomComplet."' WHERE ";
    $sql2 .= " id_alumne =". $fila[0]." AND id_tipus_contacte =".$camps[nom_complet].";";
    $result2=mysql_query($sql2);
    if (!$result2) {die(_ERR_UPDATE_ALUM_NOM . mysql_error());}
    
    
}
        
        
        

  
?>
</body>

	




