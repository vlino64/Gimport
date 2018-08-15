<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
require_once('../func/func_alumnes.php');

$db->exec("set names utf8");

$sms     = isset($_REQUEST['sms']) ? $_REQUEST['sms'] : 0 ;
$idgrups = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0 ;
$cognoms = isset($_POST['cognoms']) ? $_POST['cognoms'] : '';

$sort   = isset($_POST['sort']) ? strval($_POST['sort']) : '2';  
$order  = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$result = array();

$where = "AND ca.Valor like '%$cognoms%'";

$sql  = "SELECT DISTINCT(agm.idalumnes),ca.Valor,a.acces_alumne,a.acces_familia,a.activat,a.codi_alumnes_saga ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN alumnes            a ON agm.idalumnes         = a.idalumnes ";
$sql .= "INNER JOIN contacte_alumne   ca ON agm.idalumnes         = ca.id_alumne ";
$sql .= "INNER JOIN grups_materies    gm ON agm.idgrups_materies  = gm.idgrups_materies ";	 
$sql .= "INNER JOIN grups              g ON gm.id_grups           = g.idgrups ";
$sql .= "INNER JOIN materia            m ON gm.id_mat_uf_pla      = m.idmateria ";
$sql .= "WHERE a.activat='S' AND g.idgrups=".$idgrups." AND ca.id_tipus_contacte=".TIPUS_nom_complet." ".$where;	
//$sql .= " ORDER BY $sort $order ";

$sql .= " UNION ";

$sql .= "SELECT DISTINCT(agm.idalumnes),ca.Valor,a.acces_alumne,a.acces_familia,a.activat,a.codi_alumnes_saga ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN alumnes             a ON agm.idalumnes        = a.idalumnes ";
$sql .= "INNER JOIN contacte_alumne    ca ON agm.idalumnes        = ca.id_alumne ";
$sql .= "INNER JOIN grups_materies     gm ON agm.idgrups_materies = gm.idgrups_materies ";	 
$sql .= "INNER JOIN grups               g ON gm.id_grups          = g.idgrups ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";
$sql .= "WHERE a.activat='S' AND g.idgrups=".$idgrups." AND ca.id_tipus_contacte=".TIPUS_nom_complet." ".$where;	

$sql .= " ORDER BY 2 $order ";

$rs = $db->query($sql);

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);*/

$items = array();  
foreach($rs->fetchAll() as $row) {  
    if ($sms) {
        $dada = $row["idalumnes"];
        
        if (getMajorEdat($db, $dada )) {
            $row["Valor"] = $row["Valor"]." (>=18)";
        }
        
        // Indica si ja s'ha enviat un sms
        $sql2 = "SELECT COUNT(*) AS Total FROM sms_tmp WHERE idalumne = ".$row["idalumnes"]." AND data = '".$data."';";

        $result2 = $db->query($sql2);
        foreach($result2->fetchAll() as $fila2) { 
            $enviat = $fila2['Total'];
            if ($enviat > 0) { 
                $row["Valor"] = $row["Valor"]." (Sms enviat)";
            }
        }
        
    }
    array_push($items, $row); 
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();



?>

