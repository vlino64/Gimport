<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id   = $_REQUEST['id']; 
$sql  = "select * from incidencia_alumne where idincidencia_alumne='$id'";
$rs   = $db->query($sql); 
foreach($rs->fetchAll() as $item) {
    $aa = explode('-',$id);
    $idalumnes = $item['idalumnes'];
    $idprofessors = $item['idprofessors'];
}

$imgalum = "../images/alumnes/".$idalumnes.".jpg";
$imgprof = "../images/prof/".$idprofessors.".jpg";
		
if (file_exists($imgalum)) {
	$imgalum = "./images/alumnes/".$idalumnes.".jpg";
}
else {
	$imgalum = "./images/alumnes/alumne.png";
}
		
if (file_exists($imgprof)) {
	$imgprof = "./images/prof/".$idprofessors.".jpg";
}
else {
	$imgprof = "./images/prof/prof.png";
}

?>
    
<table class="dv-table" border="0" style="width:100%;">
<tr>
    <td style="border:0" valign=top width=180>
    <b>Alumne</b><br><?= getAlumne($db,$item['idalumnes'],TIPUS_nom_complet) ?><br>
    <?php echo "<img src=\"$imgalum\" style=\"border:1px dashed #eee;width:51px;height:70px;margin-right:1px\" />"; ?></td>
    <td width=2>&nbsp;</td>
    <td style="border:0" valign=top width=150>
    <b>Professor</b><br><?= getProfessor($db,$item['idprofessors'],TIPUS_nom_complet) ?><br>
    <?php echo "<img src=\"$imgprof\" style=\"border:1px dashed #eee;width:51px;height:70px;margin-right:1px\" />"; ?></td>
    <td width=2>&nbsp;</td>
    <td style="border:0" valign=top>
    <b>Grup</b><br><?= getGrup($db,$item['idgrups'])["nom"] ?><br>
    <b>Mat&egrave;ria</b><br><?= getMateria($db,$item['id_mat_uf_pla'])["nom_materia"] ?><br>
    <b>Comentari</b><br><?= nl2br($item['comentari']) ?><br> 
    </td>
</tr>
</table>
                            
<?php
$rs->closeCursor();
//mysql_close();
?>