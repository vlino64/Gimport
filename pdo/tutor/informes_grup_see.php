<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : getCursActual($db)["data_inici"];
if ($data_inici=='--') {
    $data_inici = getCursActual($db)["data_inici"];
}
$txt_inici  = isset($_REQUEST['data_inici']) ? $_REQUEST['data_inici'] : '';
  
$data_fi    = isset($_REQUEST['data_fi'])    ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : getCursActual($db)["data_fi"];
if ($data_fi=='--') {
    $data_fi = getCursActual($db)["data_fi"];
}
$txt_fi     = isset($_REQUEST['data_fi'])    ? $_REQUEST['data_fi'] : '';
  
$curs_escolar    = getCursActual($db)["idperiodes_escolars"]; 
$grup_materia    = 0;
$escicleloe      = 0;
$total_classes   = 0;
$total_seguiment = 0;

if ( isset($_REQUEST['grup_materia'])) {
	 $grup_materia = $_REQUEST['grup_materia'];
	 $idgrups      = getGrupMateria($db,$grup_materia)["id_grups"];
	 $idmateria    = getGrupMateria($db,$grup_materia)["id_mat_uf_pla"];
}
else {
	 $idgrups      = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0;
	 $idmateria    = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0;
	 $grup_materia = existGrupMateria($db,$idgrups,$idmateria);
}
  
if ($idmateria != 0) {
	  $escicleloe      = isMateria($db,$idmateria) ? 0 : 1 ;
	  if ($escicleloe) {
		$data_inici = getGrupMateria($db,$grup_materia)["data_inici"];
		$txt_inici  = substr($data_inici,8,2)."-".substr($data_inici,5,2)."-".substr($data_inici,0,4);
		$data_fi    = getGrupMateria($db,$grup_materia)["data_fi"];
		$txt_fi     = substr($data_fi,8,2)."-".substr($data_fi,5,2)."-".substr($data_fi,0,4);
	  }
	  $total_classes   = classes_entre_dates($db,$data_inici,$data_fi,$grup_materia,$curs_escolar);
  	  $total_seguiment = getTotalSeguimientoGrupMateria($db,$data_inici,$data_fi,$idgrups,$idmateria,$curs_escolar);
}
  
$sql  = "SELECT DISTINCT(agm.idalumnes),ca.Valor ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN alumnes            a ON agm.idalumnes         = a.idalumnes ";
$sql .= "INNER JOIN contacte_alumne   ca ON agm.idalumnes         = ca.id_alumne ";
$sql .= "INNER JOIN grups_materies    gm ON agm.idgrups_materies  = gm.idgrups_materies ";	 
$sql .= "INNER JOIN grups              g ON gm.id_grups           = g.idgrups ";
$sql .= "INNER JOIN materia            m ON gm.id_mat_uf_pla      = m.idmateria ";
$sql .= "WHERE a.activat='S' AND g.idgrups=".$idgrups." AND ca.id_tipus_contacte=".TIPUS_nom_complet;	

$sql .= " UNION ";

$sql .= "SELECT DISTINCT(agm.idalumnes),ca.Valor ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN alumnes             a ON agm.idalumnes        = a.idalumnes ";
$sql .= "INNER JOIN contacte_alumne    ca ON agm.idalumnes        = ca.id_alumne ";
$sql .= "INNER JOIN grups_materies     gm ON agm.idgrups_materies = gm.idgrups_materies ";	 
$sql .= "INNER JOIN grups               g ON gm.id_grups          = g.idgrups ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";
$sql .= "WHERE a.activat='S' AND g.idgrups=".$idgrups." AND ca.id_tipus_contacte=".TIPUS_nom_complet;	

$sql .= " ORDER BY 2  ";
$rsAlumnes = $db->query($sql);

?>

<style type="text/css">

@page {
	margin: 0.5cm 0.25cm 0.5cm 0.5cm;
}

body {
  margin: 1cm 0 0.5cm;
}

#header,
#footer {
  position: fixed;
  left: 0;
  right: 0;
  color: #aaa;
  font-size: 0.9em;
}

#header {
  top: 0;
  border-bottom: 0.1pt solid #aaa;
  margin-bottom:15px;
}

#footer {
  bottom: 0;
  border-top: 0.1pt solid #aaa;
}

#header table,
#footer table {
  width: 100%;
  border-collapse: collapse;
  border: none;
}

#header td,
#footer td {
  padding: 0;
  width: 50%;
}

.page-number {
  text-align: right;
}

.page-number:before {
  content: " " counter(page);
}

hr {
  page-break-after: always;
  border: 0;
}

</style>

<style type="text/css">
                h2 {
                    font-size: 16px;
                }
                h3 {
                    font-size: 13px;
                }
		.left{
			width:20px;
			float:left;
		}
		.left table{
			background:#E0ECFF;
		}
		.left td{
			background:#eee;
		}
		.right{
			/*float:right;*/
		}
		.right table{
			background:#E0ECFF;
		}
		.right td{
			background:#fafafa;
			padding:2px;
		}
		.right td{
			background:#E0ECFF;
                        font-size: 10px;
		}
		.right td.drop{
			background:#ffffff;
			
		}
		.right td.over{
			background:#FBEC88;
		}
		.item{
			text-align:left;
			border:1px solid #499B33;
			background:#fafafa;
			/*width:100px;*/
		}
		.assigned{
			border:1px solid #BC2A4D;
		}
		
	</style>

<div id="header">
  <table>
    <tr>
      <td>
      <b><?= getDadesCentre($db)["nom"] ?></b>&nbsp;<br />
      <?= getDadesCentre($db)["adreca"] ?>&nbsp;
      <?= getDadesCentre($db)["cp"] ?>&nbsp;<?= getDadesCentre($db)["poblacio"] ?>
      </td>
      <td style="text-align: right;">
      		<?php
		$img_logo = '../images/logo.jpg';
                if (file_exists($img_logo)) {
                	echo "<img src='".$img_logo."'>";
		}
		?>
      </td>
    </tr>
  </table>
</div>

<div id="footer">
  <table>
    <tr>
      <td>
        <?= getDadesCentre($db)["tlf"] ?>&nbsp;
      </td>
      <td align="right">
  	<?= getDadesCentre($db)["email"] ?>
      </td>
    </tr>
  </table>
</div>

<div class='left'>
		
</div>
<div class='right'>
 
<?php
foreach($rsAlumnes->fetchAll() as $row_a) {
	$c_alumne  = $row_a["idalumnes"];
?>
  <h2>
  Informe de faltes de l'alumne <a style=' color: #000066; padding:3px 3px 3px 3px '>
  <?= getAlumne($db,$c_alumne,TIPUS_nom_complet) ?></a>
  <br />Desde el <a style=' color: #000066; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
  &nbsp;&nbsp;fins al <a style=' color: #000066; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>
  
  <?php
  if ($idmateria != 0) {
	  echo "<br />Mat&egrave;ria:&nbsp;<a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '>".getMateria($db,$idmateria)["nom_materia"]."</a>";
  }
  ?>
 </h2>
 
 <table cellspacing="1">
    <tr>
        <td><strong>FALTES</strong></td>
        <td><strong>RETARDS</strong></td>
        <td><strong>JUSTIFICADES</strong></td>
        <td><strong>SEGUIMENTS</strong></td>
        <td><strong>CCC</strong></td>
    </tr>
    <?php
	  if ($idmateria == 0) {
	?>
    <tr>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCAlumne($db,$c_alumne,$data_inici,$data_fi)?></td>
    </tr>
    <?php
	  }
	  else
	    {
	?>
    <tr>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCAlumneGrupMateria($db,$c_alumne,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
    </tr>
    <?php
		}
	?>
 </table>
 
 <br />
 <h3>Relaci&oacute; de faltes</h3>
            <table cellspacing="1">
            <tr>
                <td>&nbsp;</td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
                                        $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi);   
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br />
        <h3>Relaci&oacute; de retards</h3>
 		<table cellspacing="1">
            <tr>
                <td>&nbsp;</td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
                                        $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br />
        <h3>Relaci&oacute; de justificacions</h3>
 		<table cellspacing="1">
            <tr>
                <td>&nbsp;</td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
                                        $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>

        <br />
        <h3>Relaci&oacute; de seguiments</h3>
 		<table cellspacing="1">
            <tr>
                <td>&nbsp;</td>
                <td><strong>TIPUS</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
				    $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusIncident($db,$row["id_tipus_incident"])["tipus_incident"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br />
        <h3>Relaci&oacute; de CCC</h3>
 		<table cellspacing="1">
            <tr>
                <td>&nbsp;</td>
                <td><strong>TIPUS CCC</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>EXPULSI&Oacute;</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>DESCRIPCI&Oacute;</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
                                        $rsIncidencias = getCCCAlumne($db,$c_alumne,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getCCCAlumneGrupMateria($db,$c_alumne,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
                                   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusCCC($db,$row["id_falta"])["nom_falta"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' class='drop'>".$row["expulsio"]."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessor"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".(intval($row["idmateria"]!=0) ? getMateria($db,$row["idmateria"])["nom_materia"] : '')."</td>";
						  echo "<td valign='top' width='270' class='drop'><strong>Desc. breu</strong><br>".getLiteralMotiusCCC($db,$row["id_motius"])["nom_motiu"];
						  echo "<br><strong>Desc. detallada</strong><br>".nl2br($row["descripcio_detallada"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
 
 
 <hr>       

<?php
    	
    }
?>

</div>
 
<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
</script>

<?php
if (isset($rsAlumnes)) {
	//mysql_free_result($rsAlumnes);
}
if (isset($rsIncidencias)) {
	//mysql_free_result($rsIncidencias);
}
?>