<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$action     = isset($_REQUEST['action'])     ? $_REQUEST['action']     : '' ;
$idalumnes  = isset($_REQUEST['idalumnes'])  ? $_REQUEST['idalumnes']  : 0 ;
$idfamilies = getFamiliaAlumne($db,$idalumnes);
$sql = "SELECT COUNT(*) FROM alumnes_families WHERE idfamilies = '".$idfamilies."'";
$result = $db->query($sql);

foreach($result->fetchAll() as $fila) {
    $nombreGermansInicial = $fila[0];
}

$id_germa   = isset($_REQUEST['id_germa'])   ? $_REQUEST['id_germa']   : 0 ;
$idfamiliesGerma = getFamiliaAlumne($db,$id_germa);
$sql = "SELECT COUNT(*) FROM alumnes_families WHERE idfamilies = '".$idfamiliesGerma."'";
$result = $db->query($sql);

foreach($result->fetchAll() as $fila) {
    $nombreGermansFinal = $fila[0];
}

if ($action == 'ADD') {
    if (($nombreGermansInicial == 1) AND ($nombreGermansFinal == 1))
        {
        // Li assignem al germa la família de l'alumne seleccionat
        $sql    = "UPDATE alumnes_families SET idfamilies = '".$idfamilies."' WHERE idalumnes = '".$id_germa."';";
	$result = $db->query($sql);
        }
    else if ((($nombreGermansInicial > 1) AND ($nombreGermansFinal == 1)) OR (($nombreGermansInicial == 1) AND ($nombreGermansFinal > 1)))
        {
        if ($nombreGermansInicial == 1)// L'alumne escollit inicialment no té germans previs
            {
            // Li assignem al alumne seleccionat la familia dels germans 
            $sql    = "UPDATE alumnes_families SET idfamilies = '".$idfamiliesGerma."' WHERE idalumnes = '".$idalumnes."';";
            $result = $db->query($sql);            
            }
        else // L'alumne escollit inicialment  té germans previs i el que es vol assignar no
            {
            // Li assignem al germà nou la familia de l'alumen seleccionat que ja tenia germans establerts
            $sql    = "UPDATE alumnes_families SET idfamilies = '".$idfamilies."' WHERE idalumnes = '".$id_germa."';";
            $result = $db->query($sql);         
            }
        }
    else // Si tots dos tenen més germans. Assignem  a tots els germans l'id de la família del primer ( per escollire-ne un). Serà poc habitual
        {
        $sql = "SELECT idalumnes FROM alumnes_families WHERE idfamilies = '".$idfamiliesGerma."'";
        $result = $db->query($sql);
        foreach($result->fetchAll() as $fila) {
            $sql2 = "UPDATE alumnes_families SET idfamilies = '".$idfamilies."' WHERE idalumnes = '".$fila[0]."';";
            $result2 = $db->query($sql2);         
        }
    }
}
else if ($action == 'DEL') {
	$sql    = "DELETE FROM alumnes_families WHERE idalumnes=$id_germa AND idfamilies=$idfamilies";
	$result = $db->query($sql);
}

echo json_encode(array('success'=>true));
//mysql_close();
?>