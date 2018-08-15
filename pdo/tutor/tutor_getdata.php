<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups    = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0 ;
$idalumnes  = isset($_REQUEST['alumne']) ? $_REQUEST['alumne'] : 0 ;

$page       = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows       = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort       = isset($_POST['sort']) ? strval($_POST['sort']) : 'data,hora';  
$order      = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$data_inici = isset($_REQUEST['data_inici_tutor']) ? substr($_REQUEST['data_inici_tutor'],6,4)."-".substr($_REQUEST['data_inici_tutor'],3,2)."-".substr($_REQUEST['data_inici_tutor'],0,2) : getCursActual($db)["data_inici"];

if ($data_inici == '--') {
	$data_inici = getCursActual($db)["data_inici"];
}

$data_fi    = isset($_REQUEST['data_fi_tutor'])    ? substr($_REQUEST['data_fi_tutor'],6,4)."-".substr($_REQUEST['data_fi_tutor'],3,2)."-".substr($_REQUEST['data_fi_tutor'],0,2)          : getCursActual($db)["data_fi"];

if ($data_fi == '--') {
	$data_fi = getCursActual($db)["data_fi"];
}

$offset     = ($page-1)*$rows;
$result     = array();

if ($idalumnes == 0) {
	$sql  = "SELECT count(*)";
	$sql .= "FROM incidencia_alumne ia ";
	$sql .= "WHERE ia.data BETWEEN '".$data_inici."' AND '".$data_fi."' AND ia.idalumnes IN ";
	$sql .= "(SELECT DISTINCT(agm.idalumnes) ";
	$sql .= "FROM alumnes_grup_materia agm ";
	$sql .= "INNER JOIN grups_materies gm ON agm.idgrups_materies = gm.idgrups_materies ";
	$sql .= "WHERE gm.id_grups=".$idgrups.") ";
}
else {
	$sql  = "SELECT count(*)";
	$sql .= "FROM incidencia_alumne ia ";
	$sql .= "WHERE ia.data BETWEEN '".$data_inici."' AND '".$data_fi."' AND ia.idalumnes=".$idalumnes;
}

if ($idalumnes != 0) {
	$where = "=".$idalumnes;
}
else {
    $where  = " IN (SELECT DISTINCT(agm2.idalumnes) ";
    $where .= "FROM alumnes_grup_materia agm2 ";
    $where .= "INNER JOIN grups_materies gm2 ON agm2.idgrups_materies = gm2.idgrups_materies ";	 
    $where .= "WHERE gm2.id_grups=".$idgrups.") ";
}

$rs = $db->query($sql);  

foreach($rs->fetchAll() as $row) {
    $result["total"] = $row[0];
}

$sql  = "SELECT distinct(ia.idincidencia_alumne),ia.data,ca.Valor AS alumne,ia.comentari,";
$sql .= "CONCAT(SUBSTR(ia.data,9,2),'-',SUBSTR(ia.data,6,2),'-',SUBSTR(ia.data,1,4)) AS data_incidencia, ";
$sql .= "ia.id_tipus_incidencia,cp.Valor AS professor,tf.tipus_falta,m.nom_materia,ca.id_alumne,cp.id_professor, ";
$sql .= "CONCAT(ELT(WEEKDAY(ia.data) + 1, 'DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES', 'DISSABTE', 'DIUMENGE')) AS dia, ";
$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,ia.id_tipus_incident,ti.tipus_incident ";
$sql .= "FROM incidencia_alumne ia ";
$sql .= "INNER JOIN tipus_incidents        ti ON ia.id_tipus_incident    = ti.idtipus_incident "; 
$sql .= "INNER JOIN contacte_professor     cp ON ia.idprofessors         = cp.id_professor ";
$sql .= "INNER JOIN franges_horaries       fh ON ia.idfranges_horaries   = fh.idfranges_horaries ";
$sql .= "INNER JOIN tipus_falta_alumne     tf ON ia.id_tipus_incidencia  = tf.idtipus_falta_alumne ";	 
$sql .= "INNER JOIN alumnes_grup_materia  agm ON ia.idalumnes  			 = agm.idalumnes ";
$sql .= "INNER JOIN contacte_alumne        ca ON agm.idalumnes           = ca.id_alumne ";
$sql .= "INNER JOIN grups_materies         gm ON agm.idgrups_materies    = gm.idgrups_materies ";
$sql .= "INNER JOIN materia                 m ON  ia.id_mat_uf_pla       = m.idmateria ";
$sql .= "WHERE ca.id_tipus_contacte=".TIPUS_nom_complet." AND cp.id_tipus_contacte=".TIPUS_nom_complet." ";
$sql .= "AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."' AND ia.idalumnes ".$where;
/*$sql .= "(SELECT DISTINCT(agm2.idalumnes) ";
$sql .= "FROM alumnes_grup_materia agm2 ";
$sql .= "INNER JOIN grups_materies    gm2 ON agm2.idgrups_materies  = gm2.idgrups_materies ";	 
$sql .= "WHERE gm2.id_grups=".$idgrups.") ";	*/

$sql .= " UNION ";

$sql .= "SELECT distinct(ia.idincidencia_alumne),ia.data,ca.Valor AS alumne,ia.comentari,";
$sql .= "CONCAT(SUBSTR(ia.data,9,2),'-',SUBSTR(ia.data,6,2),'-',SUBSTR(ia.data,1,4)) AS data_incidencia, ";
$sql .= "ia.id_tipus_incidencia,cp.Valor AS professor,tf.tipus_falta,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom_materia,ca.id_alumne,cp.id_professor, ";
$sql .= "CONCAT(ELT(WEEKDAY(ia.data) + 1, 'DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES', 'DISSABTE', 'DIUMENGE')) AS dia, ";
$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,ia.id_tipus_incident,ti.tipus_incident ";
$sql .= "FROM incidencia_alumne ia ";
$sql .= "INNER JOIN tipus_incidents        ti ON ia.id_tipus_incident    = ti.idtipus_incident "; 
$sql .= "INNER JOIN contacte_professor     cp ON ia.idprofessors         = cp.id_professor ";
$sql .= "INNER JOIN franges_horaries       fh ON ia.idfranges_horaries   = fh.idfranges_horaries ";
$sql .= "INNER JOIN tipus_falta_alumne     tf ON ia.id_tipus_incidencia  = tf.idtipus_falta_alumne ";	 
$sql .= "INNER JOIN alumnes_grup_materia  agm ON ia.idalumnes            = agm.idalumnes ";
$sql .= "INNER JOIN contacte_alumne        ca ON agm.idalumnes           = ca.id_alumne ";
$sql .= "INNER JOIN grups_materies         gm ON agm.idgrups_materies    = gm.idgrups_materies ";
$sql .= "INNER JOIN unitats_formatives uf ON ia.id_mat_uf_pla     = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON ia.id_mat_uf_pla     = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";
$sql .= "WHERE ca.id_tipus_contacte=".TIPUS_nom_complet." AND cp.id_tipus_contacte=".TIPUS_nom_complet." ";
$sql .= "AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."' AND ia.idalumnes ".$where;
/*$sql .= "(SELECT DISTINCT(agm2.idalumnes) ";
$sql .= "FROM alumnes_grup_materia agm2 ";
$sql .= "INNER JOIN grups_materies    gm2 ON agm2.idgrups_materies  = gm2.idgrups_materies ";	 
$sql .= "WHERE gm2.id_grups=".$idgrups.") ";*/

$sql .= " ORDER BY $sort $order LIMIT $offset,$rows";

$rs = $db->query($sql);

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);*/

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>