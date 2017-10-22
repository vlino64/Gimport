<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$data  = $_REQUEST['data'];
$data2 = conversioDataJS($data);
$periode = getCursActual($db)["idperiodes_escolars"];

$dia          = $_REQUEST['dia'];
$curs         = $_SESSION['curs_escolar'];
$professor    = $_SESSION['professor'];

if (festiu($db,$data2,$periode)== 0)   
    {    
    $sql  = "SELECT distinct(g.idgrups),g.nom as grup ";
    //$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora ";
    $sql .= "FROM prof_agrupament pa ";
    $sql .= "INNER JOIN unitats_classe     uc ON pa.idagrups_materies  = uc.idgrups_materies ";
    $sql .= "INNER JOIN dies_franges       df ON uc.id_dies_franges    = df.id_dies_franges ";
    $sql .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
    $sql .= "INNER JOIN espais_centre      ec ON uc.idespais_centre    = ec.idespais_centre ";
    $sql .= "INNER JOIN grups_materies     gm ON uc.idgrups_materies   = gm.idgrups_materies ";
    $sql .= "INNER JOIN materia             m ON gm.id_mat_uf_pla      = m.idmateria ";
    $sql .= "INNER JOIN grups               g ON gm.id_grups           = g.idgrups ";
    $sql .= "WHERE df.iddies_setmana=$dia AND fh.esbarjo<>'S' AND df.idperiode_escolar=$curs ";
    $sql .= "GROUP BY g.idgrups ";

    $sql .= "UNION ";

    $sql .= "SELECT distinct(g.idgrups),g.nom as grup ";
    //$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora ";
    $sql .= "FROM prof_agrupament pa ";
    $sql .= "INNER JOIN unitats_classe     uc ON pa.idagrups_materies  = uc.idgrups_materies ";
    $sql .= "INNER JOIN dies_franges       df ON uc.id_dies_franges    = df.id_dies_franges ";
    $sql .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
    $sql .= "INNER JOIN espais_centre      ec ON uc.idespais_centre    = ec.idespais_centre ";
    $sql .= "INNER JOIN grups_materies     gm ON uc.idgrups_materies   = gm.idgrups_materies ";
    $sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives ";
    $sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs ";
    $sql .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";
    $sql .= "INNER JOIN grups               g ON gm.id_grups           = g.idgrups ";
    $sql .= "WHERE df.iddies_setmana=$dia AND fh.esbarjo<>'S' AND df.idperiode_escolar=$curs ";
    $sql .= "GROUP BY g.idgrups ";

    $sql .= " ORDER BY 2 ";

    /*$fp = fopen("log.txt","a");
    fwrite($fp, $sql . PHP_EOL);
    fclose($fp);*/

    $rs = $db->query($sql);

    $items = array();  
    foreach($rs->fetchAll() as $row) {  
        array_push($items, $row);  
    }  
    $result["rows"] = $items;  
    }
else
    {
    $items = array();
    $result["rows"] = $items;
    }  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
