<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors       = $_REQUEST['idprofessors'];;
$idfranges_horaries = $_REQUEST['idfranges_horaries'];;
$data               = date("Y-m-d");
$any                = substr($data,0,4);
$mes                = substr($data,5,2);
$dia                = substr($data,8,2);

$comentari_tasca = "";
if (exitsIncidenciapProfessor($db,$idprofessors,$data,$idfranges_horaries)) {
    $comentari_tasca = getIncidenciapProfessor($db,$idprofessors,$data,$idfranges_horaries)["comentari_tasca"];
}

$fitxer_tasca              = "";
$link                      = "";
$sTempFileName_noextension = '../feina_guardies/'.$any.$mes.$dia."_".$idprofessors."_".$idfranges_horaries;
foreach (glob($sTempFileName_noextension."*.*") as $filename) {
    $fitxer_tasca = $filename;
	$link = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-20).substr($fitxer_tasca,3,strlen($fitxer_tasca)-2);
}

?>
<h5>&nbsp;Tasca deixada pel professor/a de la mat&egrave;ria</h5>
<p style="padding-left:10px; padding-right:10px;">
    <?php
	echo "<strong>Detall tasca</strong>&nbsp;".$comentari_tasca."<br><br>";
	   if ($link != '') {
	?>
    	
    <?php echo "Clica en la icona per obrir el fitxer amb la tasca desada pel professor/a de gu&agrave;rdia.&nbsp;&nbsp;<p align=center><a href='".$link."' target='_fitxer_tasca'><img src='./images/task_view.png' width=30></a></p>";
	   }
	   else {
	   		echo "No s'ha desat cap fitxer pel professorat de gu&agrave;rdia.";
	   }
	?>
</p>