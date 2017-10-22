<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id   = $_REQUEST['id']; 
$sql  = "select * from ccc_alumne_principal where idccc_alumne_principal='$id'";
$rs   = $db->query($sql); 
$item = mysql_fetch_array($rs);

$aa = explode('-',$id);
$idgrup    = $item['idgrup'];
$idalumne  = $item['idalumne'];
$idmateria = $item['idmateria'];
$idespais  = $item['idespais'];
		
$imgalum = "../images/alumnes/".$idalumne.".jpg";
		
if (file_exists($imgalum)) {
	$imgalum = "./images/alumnes/".$idalumne.".jpg";
}
else {
	$imgalum = "./images/alumnes/alumne.png";
}

?>
    
<table class="dv-table" border="0" style="width:100%;">
<tr>
    <td style="border:0" valign=top width=120>
    <b>Alumne</b><br><?= getAlumne($db,$item['idalumne'],TIPUS_nom_complet) ?><br>
    <?php echo "<img src=\"$imgalum\" style=\"border:1px dashed #eee;width:51px;height:70px;margin-right:1px\" />"; ?></td>
    <td width=2>&nbsp;</td>
    <td style="border:0" valign=top width=150>
    <b>Grup</b><br><?= (intval($idgrup!=0) ? getGrup($db,$idgrup)->nom : '') ?><br>
    <b>Mat&egrave;ria</b><br><?= (intval($idmateria!=0) ? getMateria($db,$idmateria)["nom_materia"] : '') ?><br>
    <b>Espai</b><br><?= (intval($idespais!=0) ? getEspaiCentre($db,$idespais)->descripcio : '') ?><br>
    </td>
    <td width=2>&nbsp;</td>
    <td style="border:0" valign=top>
    <b>Descripci&oacute; detallada</b><br><?= nl2br($item['descripcio_detallada']) ?><br>
    </td>
</tr>
</table>
                            
<?php
$rs->closeCursor();
//mysql_close();
?>