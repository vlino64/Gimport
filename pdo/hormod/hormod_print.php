<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  //$db->exec("set names utf8");
  
  $idgrups       = intval($_REQUEST['idgrups']);
  $cursactual    = getCursActual($db)["idperiodes_escolars"];
  $idTutorCarrec = getIdTutor($db)["idcarrecs"];
  
  $idprofessors = 0;
  $idprofessors = getIdProfessorByCarrecGrup($db,$idTutorCarrec,$idgrups)["idprofessors"];
  if ($idprofessors != 0) {
      $nom_tutor    = getProfessor($db,$idprofessors,TIPUS_nom_complet);
  }
  
  $rsDies     = $db->query("select * from dies_setmana where laborable='S'");
  $rsHores    = $db->query("select * from franges_horaries order by hora_inici");   
?>

    <link rel="shortcut icon" type="image/x-icon" href="../images/icons/favicon.ico">
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/cupertino/easyui.css">
    <link rel="stylesheet" type="text/css" href="../css/icon.css">  
    <link rel="stylesheet" type="text/css" href="../css/demo.css">
    
	<style type="text/css">
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
			float:right;
			width:950px;
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
			width:155px;
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

<div style="width:1000px;">
 <h5 style="margin-bottom:0px">
  &nbsp;&nbsp;
  Curs escolar <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= $_SESSION['curs_escolar_literal'] ?></a>&nbsp;&nbsp;
  Horari de <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= getGrup($db,$idgrups)["nom"] ?></a>&nbsp;&nbsp;
  Tutor/a  <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= $nom_tutor ?></a>
 </h5>
 <br>       
	<div class="left">
		&nbsp;
	</div>
	<div class="right">
		<table>
			<tr>
				<td class="blank" width="50"></td>
                <?php
                                   foreach($rsDies->fetchAll() as $row) {
                                          echo "<td class='title'>";
					  echo $row["dies_setmana"];
					  echo "</td>";
				   }
				?>
			</tr>
			
                <?php
                                   foreach($rsHores->fetchAll() as $row) {
				      $franjahoraria = $row["idfranges_horaries"];
					  if ($row["esbarjo"]=='S') {
					    echo "<tr height='20'>";
						echo "<td width='30' class='time' valign='top'>".substr($row["hora_inici"],0,5)."-".substr($row["hora_fi"],0,5)."</td>";
						echo "<td class='lunch' colspan='5'>ESBARJO</td>";
						echo "</tr>";
					  }
					  else {
						  echo "<tr>";
						  echo "<td valign='top' class='time'>".substr($row["hora_inici"],0,5)."-".substr($row["hora_fi"],0,5)."</td>";
						  for ($dia=1; $dia<=5; $dia++) {
						     $rsMateries = getMateriesDiaHoraGrup($db,$dia,$franjahoraria,$cursactual,$idgrups);
							 echo "<td valign='top' class='drop'>";
							
							 foreach($rsMateries->fetchAll() as $row) {
							    echo "<div style='border:2px solid #162b48;margin-bottom:3px;'>";
								echo "<div style='color:#b26f00;border-bottom:1px solid #b26f00;'>".$row['materia']."</div>";
								echo "<div style='color:#162b48;border-bottom:1px solid #162b48;'>".$row['espaicentre']."</div>";
								$rsProfMateria = getProfessorByGrupMateria($db,$row['idgrups_materies']);
                                                                $txt_professor = "";
                                                                foreach($rsProfMateria->fetchAll() as $row_pm) {
                                                                    $txt_professor .= getProfessor($db,$row_pm["idprofessors"],TIPUS_nom_complet)."<br>";
                                                                }
								
								echo "<div style='color:#39892f'>".$txt_professor."</div>";
								echo "</div>";
							 }
							 echo "</td>";
						  }
						  
						  echo "</tr>";
					  }
				   }
				?>          
		</table>
	</div>
</div>

<?php
//mysql_free_result($rsDies);
//mysql_free_result($rsHores);
//mysql_close();
?>