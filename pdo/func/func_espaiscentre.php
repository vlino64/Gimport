<?php

/*  getEspaiCentre --> Dades espai centre */
function getEspaiCentre($db,$idespais_centre) {
    $sql = "SELECT * FROM espais_centre WHERE idespais_centre = '$idespais_centre'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
    }
	//mysql_free_result($rec);
    return $result;
}
/* ********************************************************************************************************* */

?>