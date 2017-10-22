<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;

$sql  = "SELECT uc.idunitats_classe,CONCAT(ds.dies_setmana,'(',LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5),')') AS dia_hora,m.nom_materia,ec.descripcio,g.nom as grup,ds.iddies_setmana,fh.hora_inici ";
$sql .= "FROM unitats_classe uc ";
$sql .= "INNER JOIN dies_franges     df ON uc.id_dies_franges    = df.id_dies_franges ";
$sql .= "INNER JOIN dies_setmana     ds ON df.iddies_setmana     = ds.iddies_setmana ";
$sql .= "INNER JOIN franges_horaries fh ON df.idfranges_horaries = fh.idfranges_horaries ";
$sql .= "INNER JOIN espais_centre    ec ON uc.idespais_centre    = ec.idespais_centre ";
$sql .= "INNER JOIN grups_materies   gm ON uc.idgrups_materies   = gm.idgrups_materies ";
$sql .= "INNER JOIN prof_agrupament  pa ON gm.idgrups_materies   = pa.idagrups_materies ";
$sql .= "INNER JOIN materia           m ON gm.id_mat_uf_pla      = m.idmateria ";
$sql .= "INNER JOIN grups             g ON gm.id_grups           = g.idgrups ";
$sql .= "WHERE pa.idprofessors=".$idprofessors." ";

$sql .= " UNION ";

$sql .= "SELECT uc.idunitats_classe,CONCAT(ds.dies_setmana,'(',LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5),')') AS dia_hora,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom_materia,ec.descripcio,g.nom as grup,ds.iddies_setmana,fh.hora_inici ";
$sql .= "FROM unitats_classe uc ";
$sql .= "INNER JOIN dies_franges       df ON uc.id_dies_franges    = df.id_dies_franges ";
$sql .= "INNER JOIN dies_setmana       ds ON df.iddies_setmana     = ds.iddies_setmana ";
$sql .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
$sql .= "INNER JOIN espais_centre      ec ON uc.idespais_centre    = ec.idespais_centre ";
$sql .= "INNER JOIN grups_materies     gm ON uc.idgrups_materies   = gm.idgrups_materies ";
$sql .= "INNER JOIN prof_agrupament pa ON gm.idgrups_materies      = pa.idagrups_materies ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla      = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla      = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls          =  m.idmoduls ";
$sql .= "INNER JOIN grups               g ON gm.id_grups           = g.idgrups ";
$sql .= "WHERE pa.idprofessors=".$idprofessors." ";

$sql .= "ORDER BY 6,7 ";

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);*/

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
    array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>