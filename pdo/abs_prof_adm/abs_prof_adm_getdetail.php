<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id   = $_REQUEST['id'];
$sql  = "select * from incidencia_professor where idincidencia_professor='$id'";
$rs   = $db->query($sql); 

foreach ($rs->fetchAll() as $item) { 
    $aa                 = explode('-',$id);
    $idprofessors       = $item['idprofessors'];
    $idfranges_horaries = $item['idfranges_horaries'];
    $data               = $item['data'];
    $any                = substr($data,0,4);
    $mes                = substr($data,5,2);
    $dia                = substr($data,8,2);
}

$imgprof      = "../images/prof/".$idprofessors.".jpg";
		
if (file_exists($imgprof)) {
	$imgprof = "./images/prof/".$idprofessors.".jpg";
}
else {
	$imgprof = "./images/prof/prof.png";
}

$fitxer_tasca              = "";
$link                      = "";
$sTempFileName_noextension = '../feina_guardies/'.$any.$mes.$dia."_".$idprofessors."_".$idfranges_horaries;
foreach (glob($sTempFileName_noextension."*.*") as $filename) {
    $fitxer_tasca = $filename;
	$link = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-39).substr($fitxer_tasca,3,strlen($fitxer_tasca)-2);
}
?>
    
<table class="dv-table" border="0" style="width:100%;">
<tr>
    <td style="border:0" valign=top width=150>
    <b>Professor</b><br><?= getProfessor($db,$item['idprofessors'],TIPUS_nom_complet) ?><br>
    <?php echo "<img src=\"$imgprof\" style=\"border:1px dashed #eee;width:60px;height:70px;margin-right:1px\" />"; ?></td>
    <td width=2>&nbsp;</td>
    <td style="border:0" valign=top width=150>
    <?php
	   if ($link != '') {
	?>
        <b>Fitxer tasca</b><br>
        <?php echo "<a href='".$link."' target='_fitxer_tasca'><img src='./images/task_view.png' width=30></a>"; ?></td>
    <?php
	   }
	?>   
    <td width=2>&nbsp;</td>
    <td style="border:0" valign=top>  
    <b>Grup</b><br><?= getGrup($db,$item['idgrups'])["nom"] ?><br>
    <b>Mat&egrave;ria</b><br><?= getMateria($db,$item['id_mat_uf_pla'])["nom_materia"] ?><br>
    <b>Detall tasca</b><br><?= $item['comentari_tasca'] ?><br>
    </td>
</tr>
</table>
                            
<?php
$rs->closeCursor();
//mysql_close();
?>