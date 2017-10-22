<?php
  session_start();	 
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $idprofessors  = intval($_REQUEST['idprofessors']);
  $rsTorns = $db->query("select * from torn");
                                   
  $cursactual    = getCursActual($db)["idperiodes_escolars"];
  $cursliteral   = getCursActual($db)["Nom"];
  $idTutorCarrec = getIdTutor($db)["idcarrecs"];
  
  $rsDies        = $db->query("select * from dies_setmana where laborable='S'");
  $mode_impresio = isset($_REQUEST['mode_impresio']) ? $_REQUEST['mode_impresio'] : 0;
?>

<style type="text/css">

@page {
	margin: 1cm;
}

body {
  margin: 1.5cm 0;
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
		.left{
			width:5px;
			float:left;
		}
		.left table{
			background:#E0ECFF;
		}
		.left td{
			background:#eee;
		}
		.right{
			padding-left: 5px;
			width:1000px;
		}
		.right table{
			background:#E0ECFF;
			width:100%;
		}
		.right td{
			background:#fafafa;
			text-align:center;
			padding:2px;
		}
		.right td{
			background:#E0ECFF;
		}
		.right td.drop{
			background:#fafafa;
			width:95px;
		}
		.right td.over{
			background:#FBEC88;
		}
		.item{
			text-align:center;
			border:1px solid #499B33;
			background:#fafafa;
			/*width:100px;*/
		}
		.assigned{
			border:1px solid #BC2A4D;
		}
		
</style>

<?php
  	if ($mode_impresio) {
?>

<div id="header">
  <table>
    <tr>
      <td>
      <b><?= getDadesCentre($db)["nom"] ?></b><br />
      <?= getDadesCentre($db)["adreca"] ?>&nbsp;&nbsp;
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
        <?= getDadesCentre($db)["tlf"] ?>&nbsp;&nbsp;<?= getDadesCentre($db)["email"] ?>
      </td>
      <td align="right">
  	<div class="page-number"></div>
      </td>
    </tr>
  </table>
</div>

<?php
  	}
?>

<div>
 <br />
 <h5 style="margin-bottom:0px">
  &nbsp;&nbsp;
  Curs escolar <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= $cursliteral ?></a>&nbsp;&nbsp;
  Horari de <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= getProfessor($db,$idprofessors,TIPUS_nom_complet) ?></a>
 </h5>
 
 <?php
    foreach($rsTorns->fetchAll() as $row_t) {
     if (countItemsHorariTorn($db,$cursactual,$idprofessors,$row_t['idtorn']) > 0) {
        echo "<h5 style='margin-bottom:0px'>";
	echo "&nbsp;&nbsp;&nbsp;Torn >>&nbsp;".getTorn($db,$row_t['idtorn'])["nom_torn"];
	echo "</h5>";
 ?>       
       
	<div class="right">
	<table border=0>
		<tr>
                    <td class="blank" width="50"></td>
                <?php
                    $rsDies = $db->query("select * from dies_setmana where laborable='S'");
                    foreach($rsDies->fetchAll() as $row) {
                        echo "<td class='title' width=158>";
		        echo $row["dies_setmana"];
			echo "</td>";
		    }
		?>
		</tr>
			
                <?php
                    $rsHores = $db->query("select * from franges_horaries where idtorn=".$row_t['idtorn']." order by hora_inici");
                    foreach($rsHores->fetchAll() as $row) {
				      $franjahoraria = $row["idfranges_horaries"];
					  if ($row["esbarjo"]=='S') {
					    echo "<tr height='20'>";
						echo "<td width='10' valign='top'>".substr($row["hora_inici"],0,5)."-".substr($row["hora_fi"],0,5)."</td>";
						echo "<td class='lunch' colspan='5'>ESBARJO</td>";
						echo "</tr>";
					  }
					  else {
						  echo "<tr>";
						  echo "<td valign='top'>".substr($row["hora_inici"],0,5)."-".substr($row["hora_fi"],0,5)."</td>";
						  for ($dia=1; $dia<=5; $dia++) {
							 echo "<td valign='top' class='drop'>";
							 $rsMateries = getMateriesDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
							 foreach($rsMateries->fetchAll() as $row) {
							        echo "<div style='border:2px solid #162b48;margin-bottom:3px;'>";
								echo "<div style='color:#cc092f;border-bottom:1px solid #cc092f;'>".$row['materia']."</div>";
								echo "<div style='color:#39892f;border-bottom:1px solid #39892f;'>".$row['grup']."</div>";
								echo "<div style='color:#162b48;border-bottom:1px solid #162b48;'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }
							 
                                                         $rsGuardies = getGuardiaDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
                                                         foreach($rsGuardies->fetchAll() as $row) {
							    /*echo "<div style='border:2px dashed #ffcb00;margin-bottom:3px;'>";
								echo "<div style='color:#988600;border-bottom:1px solid #988600;'>GUARDIA</div>";
								echo "<div style='color:#b26f00'>".$row['espaicentre']."</div>";
								echo "</div>";*/
								
								echo "<div style='background:#f2b735;margin-bottom:3px;'>";
								echo "<div style='color:#fff;border-bottom:1px solid #fff;'>GUARDIA</div>";
								echo "<div style='color:#fff'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }

							 $rsDireccio = getDireccioDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
							 foreach($rsDireccio->fetchAll() as $row) {  
							    //echo "<div style='border:2px dashed #e76000;margin-bottom:3px;'>";
								//echo "<div style='color:#988600;border-bottom:1px solid #e76000;'>DIRECCIO</div>";
								//echo "<div style='color:#b26f00'>".$row['espaicentre']."</div>";
								
								echo "<div style=background:#b43624;margin-bottom:3px;'>";
								echo "<div style='color:#fff;border-bottom:1px solid #fff;'>DIRECCIO</div>";
								echo "<div style='color:#fff'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }
							 
							 $rsCoordinacio = getCoordinacioDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
							 foreach($rsCoordinacio->fetchAll() as $row) {  
								echo "<div style='background:#0074c5;margin-bottom:3px;'>";
								echo "<div style='color:#fff;border-bottom:1px solid #fff;'>COORDINACIO</div>";
								echo "<div style='color:#fff'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }
							 
							 $rsAtencio = getAtencionsDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
							 foreach($rsAtencio->fetchAll() as $row) {  
								echo "<div style='background:#359444;margin-bottom:3px;'>";
								echo "<div style='color:#fff;border-bottom:1px solid #fff;'>ATENCIONS</div>";
								echo "<div style='color:#fff'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }
							 
							 $rsPermanencia = getPermanenciesDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
                                                         foreach($rsPermanencia->fetchAll() as $row) {  
								echo "<div style='background:#8b5632;margin-bottom:3px;'>";
								echo "<div style='color:#fff;border-bottom:1px solid #fff;'>PERMANENCIA</div>";
								echo "<div style='color:#fff'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }
							 
							 $rsReunio = getReunionsDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
                                                         foreach($rsReunio->fetchAll() as $row) {    
								echo "<div style='background:#562b9a;margin-bottom:3px;'>";
								echo "<div style='color:#fff;border-bottom:1px solid #fff;'>REUNIO</div>";
								echo "<div style='color:#fff'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }
                                                         
                                                         $rsAltre = getAltresDiaHoraProfessor($db,$dia,$franjahoraria,$cursactual,$idprofessors);
                                                         foreach($rsAltre->fetchAll() as $row) {    
								echo "<div style='background:#ff33dd;margin-bottom:3px;'>";
								echo "<div style='color:#fff;border-bottom:1px solid #fff;'>ALTRES</div>";
								echo "<div style='color:#fff'>".$row['espaicentre']."</div>";
								echo "</div>";
							 }
							 
							 
							 echo "</td>";
						  }
						  echo "</tr>";
					  }
				   }
                    }
		?>          
	</table><br />
	</div> 
 <?php       
    }
 ?>

</div>

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
</script>

<?php
if (isset($rsTorns)) {
	//mysql_free_result($rsReunio);
    $rsTorns->closeCursor();
}
if (isset($rsGrupsProfessor)) {
	//mysql_free_result($rsGrupsProfessor);
        $rsGrupsProfessor->closeCursor();
}
if (isset($rsDies)) {
	//mysql_free_result($rsDies);
    $rsDies->closeCursor();
}
if (isset($rsHores)) {
	//mysql_free_result($rsHores);
    $rsHores->closeCursor();
}
if (isset($rsMateries)) {
	//mysql_free_result($rsMateries);
    $rsMateries->closeCursor();
}
if (isset($rsGuardies)) {
	//mysql_free_result($rsGuardies);
    $rsGuardies->closeCursor();
}
if (isset($rsDireccio)) {
	//mysql_free_result($rsDireccio);
    $rsDireccio->closeCursor();
}
if (isset($rsCoordinacio)) {
	//mysql_free_result($rsCoordinacio);
    $rsCoordinacio->closeCursor();
}
if (isset($rsAtencio)) {
	//mysql_free_result($rsAtencio);
    $rsAtencio->closeCursor();
}
if (isset($rsPermanencia)) {
	//mysql_free_result($rsPermanencia);
    $rsPermanencia->closeCursor();
}
if (isset($rsReunio)) {
	//mysql_free_result($rsReunio);
    $rsReunio->closeCursor();
}

//mysql_close();
?>