<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page   = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows   = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort   = isset($_POST['sort']) ? strval($_POST['sort']) : '2';  
$order  = isset($_POST['order']) ? strval($_POST['order']) : 'desc';

$desde = isset($_REQUEST['desde']) ? substr($_REQUEST['desde'],6,4)."-".substr($_REQUEST['desde'],3,2)."-".substr($_REQUEST['desde'],0,2) : getCursActual($db)["data_inici"];
if ($desde=='--') {
    $desde = getCursActual($db)["data_inici"];
}
  
$fins_a = isset($_REQUEST['fins_a']) ? substr($_REQUEST['fins_a'],6,4)."-".substr($_REQUEST['fins_a'],3,2)."-".substr($_REQUEST['fins_a'],0,2) : getCursActual($db)["data_fi"];
if ($fins_a=='--') {
    $fins_a = getCursActual($db)["data_fi"];
}

$idprofessors = isset($_REQUEST['idprofessors']) ? $_REQUEST['idprofessors'] : 0 ;

if (isset($_REQUEST['idprofessors']) && $_REQUEST['idprofessors']!='') {
	$where = " AND ip.idprofessors=$idprofessors";
	//$where = " ";
}
else {
	$where = " ";
}

$offset = ($page-1)*$rows;
$result = array();

$sql  = "SELECT count(*)";
$sql .= "FROM incidencia_professor ip ";
$sql .= "WHERE ip.data BETWEEN '$desde' AND '$fins_a' $where ";

$rs = $db->query($sql);  

foreach($rs->fetchAll() as $row) { 
    $result["total"] = $row[0];
}

$sql  = "SELECT distinct(ip.idincidencia_professor),ip.data,ip.comentari,";
$sql .= "CONCAT(SUBSTR(ip.data,9,2),'-',SUBSTR(ip.data,6,2),'-',SUBSTR(ip.data,1,4)) AS data_incidencia, ";
$sql .= "ip.id_tipus_incidencia,tf.tipus_falta,m.nom_materia, ";
$sql .= "CONCAT(ELT(WEEKDAY(ip.data) + 1, 'DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES', 'DISSABTE', 'DIUMENGE')) AS dia, ";
$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,cp.Valor AS professor,ip.idprofessors ";
$sql .= "FROM incidencia_professor ip ";
$sql .= "INNER JOIN contacte_professor     cp ON  ip.idprofessors         = cp.id_professor ";
$sql .= "INNER JOIN franges_horaries       fh ON  ip.idfranges_horaries   = fh.idfranges_horaries ";
$sql .= "INNER JOIN tipus_falta_professor  tf ON  ip.id_tipus_incidencia  = tf.idtipus_falta_professor ";	 
$sql .= "INNER JOIN materia                 m ON  ip.id_mat_uf_pla        = m.idmateria ";
$sql .= "WHERE cp.id_tipus_contacte=".TIPUS_nom_complet." AND ip.data BETWEEN '$desde' AND '$fins_a' $where ";

$sql .= " UNION ";

$sql .= "SELECT distinct(ip.idincidencia_professor),ip.data,ip.comentari,";
$sql .= "CONCAT(SUBSTR(ip.data,9,2),'-',SUBSTR(ip.data,6,2),'-',SUBSTR(ip.data,1,4)) AS data_incidencia, ";
$sql .= "ip.id_tipus_incidencia,tf.tipus_falta,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom_materia, ";
$sql .= "CONCAT(ELT(WEEKDAY(ip.data) + 1, 'DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES', 'DISSABTE', 'DIUMENGE')) AS dia, ";
$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,cp.Valor AS professor,ip.idprofessors ";
$sql .= "FROM incidencia_professor ip ";
$sql .= "INNER JOIN contacte_professor     cp ON  ip.idprofessors         = cp.id_professor ";
$sql .= "INNER JOIN franges_horaries       fh ON  ip.idfranges_horaries   = fh.idfranges_horaries ";
$sql .= "INNER JOIN tipus_falta_professor  tf ON  ip.id_tipus_incidencia  = tf.idtipus_falta_professor ";	 
$sql .= "INNER JOIN unitats_formatives     uf ON  ip.id_mat_uf_pla        = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs             mu ON  ip.id_mat_uf_pla        = mu.id_ufs ";
$sql .= "INNER JOIN moduls                  m ON  mu.id_moduls            = m.idmoduls ";
$sql .= "WHERE cp.id_tipus_contacte=".TIPUS_nom_complet." AND ip.data BETWEEN '$desde' AND '$fins_a' $where ";

$sql .= "ORDER BY $sort $order ";

//$sql .= "ORDER BY $sort $order LIMIT $offset,$rows";

$rs = $db->query($sql);
  
$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>