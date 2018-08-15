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
$hora           = isset($_SESSION['hora_guardia']) ? $_SESSION['hora_guardia'] : date('H:i');

$sql = "SELECT idfranges_horaries,idtorn FROM franges_horaries WHERE hora_inici<='$hora' AND hora_fi>='$hora' order by 2";
$rsFranjes = $db->query($sql);

foreach($rsFranjes->fetchAll() as $row_f) {
        echo "<h5>";
        echo getTorn($db,$row_f['idtorn'])["nom_torn"];
        echo "&nbsp;&nbsp;";
        echo getLiteralFranjaHoraria($db,$row_f['idfranges_horaries']);
        echo "</h5>";
    
	$idfranges_horaries = $row_f['idfranges_horaries'];
								
	$sql_c  = "SELECT uc.*,pa.idagrups_materies,g.idgrups, m.idmateria AS idmateria, m.nom_materia AS materia, ";
	$sql_c .= "ec.descripcio AS espaicentre,g.nom as grup,fh.hora_inici,fh.hora_fi, ";
	$sql_c .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,fh.idfranges_horaries ";
	$sql_c .= "FROM prof_agrupament pa ";
	$sql_c .= "INNER JOIN professors          p ON pa.idprofessors       = p.idprofessors ";
	$sql_c .= "INNER JOIN unitats_classe     uc ON pa.idagrups_materies  = uc.idgrups_materies ";
	$sql_c .= "INNER JOIN dies_franges       df ON uc.id_dies_franges    = df.id_dies_franges ";
	$sql_c .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
	$sql_c .= "INNER JOIN espais_centre      ec ON uc.idespais_centre    = ec.idespais_centre ";
	$sql_c .= "INNER JOIN grups_materies     gm ON uc.idgrups_materies   = gm.idgrups_materies ";
	$sql_c .= "INNER JOIN materia             m ON gm.id_mat_uf_pla      = m.idmateria ";
	$sql_c .= "INNER JOIN grups               g ON gm.id_grups           = g.idgrups ";
	$sql_c .= "WHERE p.activat='S' AND df.iddies_setmana=$dia AND fh.esbarjo<>'S' AND df.idperiode_escolar=$curs AND fh.idfranges_horaries=$idfranges_horaries";
	$sql_c .= " UNION ";
	$sql_c .= "SELECT uc.*,pa.idagrups_materies,g.idgrups, uf.idunitats_formatives AS idmateria, CONCAT(m.nom_modul,'-',uf.nom_uf) AS materia, ";
	$sql_c .= "ec.descripcio AS espaicentre,g.nom as grup,fh.hora_inici,fh.hora_fi, ";
	$sql_c .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,fh.idfranges_horaries ";
	$sql_c .= "FROM prof_agrupament pa ";
	$sql_c .= "INNER JOIN professors          p ON pa.idprofessors       = p.idprofessors ";
	$sql_c .= "INNER JOIN unitats_classe     uc ON pa.idagrups_materies  = uc.idgrups_materies ";
	$sql_c .= "INNER JOIN dies_franges       df ON uc.id_dies_franges    = df.id_dies_franges ";
	$sql_c .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
	$sql_c .= "INNER JOIN espais_centre      ec ON uc.idespais_centre    = ec.idespais_centre ";
	$sql_c .= "INNER JOIN grups_materies     gm ON uc.idgrups_materies   = gm.idgrups_materies ";
	$sql_c .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives ";
	$sql_c .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs ";
	$sql_c .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";
	$sql_c .= "INNER JOIN grups               g ON gm.id_grups           = g.idgrups ";
	$sql_c .= "WHERE p.activat='S' AND df.iddies_setmana=$dia AND fh.esbarjo<>'S' AND df.idperiode_escolar=$curs AND fh.idfranges_horaries=$idfranges_horaries";
	$sql_c .= " AND gm.data_inici<='".date("y-m-d")."' AND gm.data_fi>='".date("y-m-d")."' ";
	$sql_c .= "ORDER BY 10";

        $rsClasses     = $db->query($sql_c);

	echo "<ul>";
	foreach($rsClasses->fetchAll() as $row_cl) {
            $rsProfMateria = getProfessorByGrupMateria($db,$row_cl['idgrups_materies']);
            foreach($rsProfMateria->fetchAll() as $row_pm) {
                $id_professor  = $row_pm["idprofessors"];
		$img_professor = "../images/prof/".$id_professor.".jpg";
	        
                //$id_professor  = getOneProfessorByGrupMateria($db,$row_cl['idgrups_materies'])["idprofessors"];
		//$img_professor = "../images/prof/".$id_professor.".jpg";
				
                $hora_entra_centre = getLastLogProfessor($db,$id_professor,date("y-m-d"),TIPUS_ACCIO_ENTROALCENTRE);
                $hora_entra_clase  = getLastLogProfessor($db,$id_professor,date("y-m-d"),TIPUS_ACCIO_ENTRACLASSE);
                                                
                if (existProfessorSortidaData($db,$id_professor,date("y-m-d"),date("H:i")) != 0) {
                	echo "<div id='clas".$row_cl['idunitats_classe']."' class='itemsortida'><li>";
			$id_sortida = existProfessorSortidaData($db,$id_professor,date("y-m-d"),date("H:i"));
			echo "<strong>".getSortida($db,$id_sortida)["lloc"]."</strong>";
		}
		else if (existsProfeGuardiaSignada($db,$idfranges_horaries,date("y-m-d"),$row_cl['idmateria'],$row_cl['idgrups'])) {
			echo "<div id='clas".$row_cl['idunitats_classe']."' class='itemguardia'><li>";
			$prof_guard_signada = getProfeGuardiaSignada($db,$idfranges_horaries,date("y-m-d"),$row_cl['idmateria'],$row_cl['idgrups']);
			echo "<strong>SIGNADA:".getProfessor($db,$prof_guard_signada,TIPUS_nom_complet)."</strong>";
		}
		else if (getIncidenciaProfessor($db,$id_professor,date("y-m-d"),$idfranges_horaries) == TIPUS_FALTA_PROFESSOR_ABSENCIA) {
			echo "<div id='clas".$row_cl['idunitats_classe']."' class='itemabsencia'><li>";
		}
		else if( ($hora_entra_clase>=$row_cl['hora_inici']) || ($hora_entra_clase>=$row_cl['hora_fi']) ) {
			echo "<div id='clas".$row_cl['idunitats_classe']."' class='itemclase'><li>";
		}
                else if ( ($modul_reg_prof) && (existLogProfessorData($db,$id_professor,TIPUS_ACCIO_ENTROALCENTRE,date("y-m-d"))) ) {
                        echo "<div id='clas".$row_cl['idunitats_classe']."' class='itemcentre'><li>";
                        echo "<font color=navy>Entrada al centre: <strong>".$hora_entra_centre."</strong></font>";
                }
		else {
			echo "<div id='clas".$row_cl['idunitats_classe']."' class='item'><li>";
		}
                                                                                              
		echo "<table width=100% cellpadding=0 cellspacing=0 border=0><tr>";
		if (file_exists($img_professor)) {
		   echo "<td valign=top align=left width=60>
		   <img src='./images/prof/".$id_professor.".jpg".$strNoCache."' width='50' height='60'>";
		}
		else {
		   echo "<td valign=top align=left width=60><img src='./images/prof/prof.png' width='50' height='60'>";
		}
						
		echo "<a href='javascript:void(0)' title='Passar llista professor de gu&agrave;rdia' class='easyui-linkbutton' plain='true'";
		echo "onclick='doAssist(".$id_professor.",".$row_cl['idgrups'].",".$row_cl['idmateria'].",".$idfranges_horaries.",".$row_cl['idespais_centre'].")'>";
		echo "<img src='./images/icons/icon_validate.png' width=36 border=0></a></td>";
                        
		echo "<td><strong>".substr($row_cl['grup'],0,14)."</strong><br>";
		echo "<font color=#a70e11>".substr($row_cl['materia'],0,28)."</font><br>";
		echo "<font color=navy>".substr($row_cl['espaicentre'],0,20)."</font><br>";
		echo "<strong><font>".substr(getProfessor($db,$id_professor,TIPUS_nom_complet),0,30)."</font></strong></td>";
		echo "</tr></table>";
		echo "</li></div>";
            }
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
if (isset($rsClasses)) {
	//mysql_free_result($rsClasses);
}
//mysql_close();
?>