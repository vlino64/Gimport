<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id   = $_REQUEST['id']; 
$sql  = "select * from missatges_tutor where idmissatges_tutor=$id";
$rs   = $db->query($sql);

foreach($rs->fetchAll() as $item) { 
    $idfamilia = getFamiliaAlumne($db,$item['idalumne']);
    $num_tutor = $item['num_tutor'];
    if ($num_tutor == 1) {
            $nom_tutor  = getFamilia($db,$idfamilia,TIPUS_nom_pare)." ";
            $nom_tutor .= getFamilia($db,$idfamilia,TIPUS_cognom1_pare)." ";
            $nom_tutor .= getFamilia($db,$idfamilia,TIPUS_cognom2_pare)." ";
    }
    else if ($num_tutor == 2) {
            $nom_tutor  = getFamilia($db,$idfamilia,TIPUS_nom_mare)." ";
            $nom_tutor .= getFamilia($db,$idfamilia,TIPUS_cognom1_mare)." ";
            $nom_tutor .= getFamilia($db,$idfamilia,TIPUS_cognom2_mare)." ";
    }
}

?>
    
<table class="dv-table" border="0" style="width:100%;">
<tr>
    <td style="border:0" valign=top width=650>
    <b>Tutor/a</b><br><?= $nom_tutor ?><br>
    <b>Missatge</b><br><?=  nl2br($item['missatge']) ?><br>
    <td width=2>&nbsp;</td>
</tr>
</table>
                            
<?php
$rs->closeCursor();
//mysql_close();
?>