<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$strNoCache    = "";
$curs          = getCursActual($db)["idperiodes_escolars"];
$dia           = date('w');
$hora_actual   = date('H:i');

$rsProfessors = getProfessorsLogAccioData($db,TIPUS_ACCIO_ENTROALCENTRE,date("Y/m/d"));
echo "<ul>";
				foreach($rsProfessors->fetchAll() as $row) {
                                        $id_professor  = $row['id_professor'];
					$img_professor = "../images/prof/".$id_professor.".jpg";
											
					$hora_entra_centre = getLastLogProfessor($db,$id_professor,date("y-m-d"),TIPUS_ACCIO_ENTROALCENTRE);
					$hora_entra_clase  = getLastLogProfessor($db,$id_professor,date("y-m-d"),TIPUS_ACCIO_ENTRACLASSE);
					
					$sql_f = "SELECT idfranges_horaries,idtorn FROM franges_horaries WHERE hora_inici<='$hora_actual' AND hora_fi>='$hora_actual' order by 2";
					$rsFranjes = $db->query($sql_f);
					$itera_franjes = 1;
					
					foreach($rsFranjes->fetchAll() as $row_f) {
						$txt_professor = '';
						$id_prof_actual = $id_professor;
						
						if (($id_prof_actual == $id_professor) && ($itera_franjes > 1)) {
							
							if (existsGuardiaDiaHoraProfessor($db,$dia,$row_f['idfranges_horaries'],$curs,$id_professor)) {				
								echo "<div id='clas".$id_professor."' class='itemguardia'><li>";
								$rsGuardies = getGuardiaDiaHoraProfessor($db,$dia,$row_f['idfranges_horaries'],$curs,$id_professor);
								foreach($rsGuardies->fetchAll() as $row_g) {
									$txt_professor = "<br><br><font color=navy>".substr($row_g['espaicentre'],0,24)."</font>";
								}
							}
							
							else if(existMateriaDiaHoraProfessor($db,$dia,$row_f['idfranges_horaries'],$curs,$id_professor)) {
									echo "<div id='clas".$id_professor."' class='itemclase'><li>";
									$rsMateries = getMateriesDiaHoraProfessor($db,$dia,$row_f['idfranges_horaries'],$curs,$id_professor);
									foreach($rsMateries->fetchAll() as $row_g) {
										$txt_professor  = "<font color=red>".substr($row_g['materia'],0,27)."</font><br>";
										$txt_professor .= "<strong>".substr($row_g['grup'],0,24)."</strong><br>";
										$txt_professor .= "<font color=navy>".substr($row_g['espaicentre'],0,24)."</font><br>";
									}
							}
												
							else {
									echo "<div id='clas".$id_professor."' class='item'><li>";
									$txt_professor = "<br><br><br>";
							}
							
							echo "<table width=100% cellpadding=0 cellspacing=0 border=0><tr>";
							if (file_exists($img_professor)) {
								   echo "<td valign=top align=left width=40>
								   <img src='./images/prof/".$id_professor.".jpg".$strNoCache."' width='40' height='50'>";
							}
							else {
								   echo "<td valign=top align=left width=60><img src='./images/prof/prof.png' width='40' height='50'>";
							}
							
							echo "</td>";
							 
							//echo "<td><strong>".substr($row['grup'],0,15)."</strong><br>";
							//echo "<font color=red>".substr($row['materia'],0,40)."</font><br>";
							//echo "<font color=navy>".$row['espaicentre']."</font><br>";
							echo "<td valign='top'>";
							echo "<strong><font>".getProfessor($db,$id_professor,TIPUS_nom_complet)."</font></strong><br>";
							echo "</td></tr>";
							
							echo "<tr><td colspan='2' valign='top'>";
							echo "<b>".$txt_professor."</b>";
							echo "</td></tr>";
							
							echo "<tr><td colspan='2' valign='top' bgcolor='#0066FF'>";
							echo "<font color='#FFFFFF'>HORA ENTRADA <strong>".substr($hora_entra_centre,0,5)."</strong> h</font></td></tr>";
							echo "<tr><td colspan='2' valign='top' bgcolor='#E80000'>";
							if (existLogProfessorData($db,$id_professor,TIPUS_ACCIO_SURTODELCENTRE,date("y-m-d"))) {
							 echo "<font color='#FFFFFF'>HORA SORTIDA <strong>".substr(getLastLogProfessor($db,$id_professor,date("y-m-d"),TIPUS_ACCIO_SURTODELCENTRE),0,5)."</strong> h</font>";
							}
							echo "</td>";
							
							echo "</tr></table>";
							echo "</li></div>";	
						}
						
						$itera_franjes++;
					}
					
				}
echo "</ul>";
echo "<div class='clear'></div>";
?>
			
<div class="clear"></div> 
<br /><br /><br /><br />
    
<?php
if (isset($rsProfessors)) {
	//mysql_free_result($rsProfessors);
}
if (isset($rsGuardies)) {
	//mysql_free_result($rsGuardies);
}
if (isset($rsMateries)) {
	//mysql_free_result($rsMateries);
}
if (isset($rsFranjes)) {
	//mysql_free_result($rsFranjes);
}
//mysql_close();
?>