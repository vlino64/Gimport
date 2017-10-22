<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$strNoCache     = "";
$modul_reg_prof = getModulsActius($db)["mod_reg_prof"];
$curs           = getCursActual($db)["idperiodes_escolars"];   
$dia            = date('w');
$franja_actual  = date('H:i');
$sql       = "SELECT idfranges_horaries,idtorn FROM franges_horaries WHERE hora_inici<='$franja_actual' AND hora_fi>='$franja_actual' order by 2";
$rsFranjes = $db->query($sql) or die(mysql_error());

				foreach($rsFranjes->fetchAll() as $row_f) {
				echo "<h5>";
					echo getTorn($db,$row_f['idtorn'])["nom_torn"];
					echo "&nbsp;&nbsp;";
					echo getLiteralFranjaHoraria($db,$row_f['idfranges_horaries']);
					echo "</h5>";
					
					$idfranges_horaries = $row_f['idfranges_horaries'];
								
					$sql_c  = "SELECT cp.id_professor,g.id_dies_franges,cp.Valor AS professor, ";
					$sql_c .= "ec.descripcio AS espaicentre,fh.hora_inici,fh.hora_fi, ";
					$sql_c .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,fh.idfranges_horaries ";
					$sql_c .= "FROM contacte_professor cp ";
					$sql_c .= "INNER JOIN guardies            g ON g.idprofessors        = cp.id_professor ";
					$sql_c .= "INNER JOIN dies_franges       df ON g.id_dies_franges     = df.id_dies_franges ";
					$sql_c .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
					$sql_c .= "INNER JOIN espais_centre      ec ON g.idespais_centre    = ec.idespais_centre ";
					$sql_c .= "WHERE df.iddies_setmana=$dia AND fh.esbarjo<>'S' AND df.idperiode_escolar=$curs AND ";
					$sql_c .= "fh.idfranges_horaries=$idfranges_horaries AND cp.id_tipus_contacte=".TIPUS_nom_complet;
					$sql_c .= " ORDER BY 3";

					$rsGuardies    = $db->query($sql_c);
					echo "<ul>";
					foreach($rsGuardies->fetchAll() as $row_cl) {
                                                $id_professor  = $row_cl['id_professor'];
						$img_professor = "../images/prof/".$id_professor.".jpg";
                                                
						$hora_entra_centre = getLastLogProfessor($db,$id_professor,date("y-m-d"),TIPUS_ACCIO_ENTROALCENTRE);
                                                $hora_entra_clase = getLastLogProfessor($db,$id_professor,date("y-m-d"),TIPUS_ACCIO_ENTRAGUARDIA);
						
						if (existProfessorSortidaData($db,$id_professor,date("y-m-d"),date("H:i")) != 0) {
							echo "<div id='guar".$row_cl['id_professor']."' class='itemsortida'><li>";
							$id_sortida = existProfessorSortidaData($db,$id_professor,date("y-m-d"),date("H:i"));
							echo "<strong>".getSortida($db,$id_sortida)["lloc"]."</strong>";
						}
						else if (getIncidenciaProfessor($db,$id_professor,date("y-m-d"),$idfranges_horaries) == TIPUS_FALTA_PROFESSOR_ABSENCIA) {
							echo "<div id='guar".$row_cl['id_professor']."' class='itemabsencia'><li>";
						}
						else if( ($hora_entra_clase>=$row_cl['hora_inici']) || ($hora_entra_clase>=$row_cl['hora_fi']) ) {
							echo "<div id='guar".$row_cl['id_professor']."' class='itemclase'><li>";
						}
						else if ( ($modul_reg_prof) && (existLogProfessorData($db,$id_professor,TIPUS_ACCIO_ENTROALCENTRE,date("y-m-d"))) ) {
                                                        echo "<div id='clas".$row_cl['idunitats_classe']."' class='itemcentre'><li>";
                                                        echo "<font color=navy>Entrada al centre: <strong>".$hora_entra_centre."</strong></font>";
                                                }
						else {
							echo "<div id='guar".$row_cl['id_professor']."' class='item'><li>";
						}
						
						echo "<table width=100% cellpadding=0 cellspacing=0 border=0><tr>";
						if (file_exists($img_professor)) {
						   echo "<td valign=top align=left width=60>
						   <img src='./images/prof/".$id_professor.".jpg".$strNoCache."' width='50' height='60'>";
						}
						else {
						   echo "<td valign=top align=left width=60><img src='./images/prof/prof.png' width='50' height='60'>";
						}
						
						echo "</td>";
                        
						echo "<td><strong>".getProfessor($db,$id_professor,TIPUS_nom_complet)."</strong><br>";
						echo "<font color=#a70e11>".$row_cl['espaicentre']."</font><br>";
						echo "</tr></table>";
						echo "</li></div>";	
					}
					echo "</ul>";
					echo "<div class='clear'></div>";	
				}
			?>
         
        <div class="clear"></div> 
        <br /><br /><br /><br />
        
<?php
if (isset($rsFranjes)) {
	//mysql_free_result($rsFranjes);
}
if (isset($rsGuardies)) {
	//mysql_free_result($rsGuardies);
}
//mysql_close();
?>