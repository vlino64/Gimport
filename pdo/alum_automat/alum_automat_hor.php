<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $idalumnes       = intval($_REQUEST['idalumnes']);
  $idgrups 	   = getGrupAlumne($db,$idalumnes)["idgrups"];
  $idtorn          = getGrup($db,$idgrups)["idtorn"];
  $nom_alumne      = getAlumne($db,$idalumnes,TIPUS_nom_complet);
  $cursactual      = getCursActual($db)["idperiodes_escolars"];
  $cursliteral     = getCursActual($db)["Nom"];
  $idTutorCarrec   = getIdTutor($db)["idcarrecs"];
  
  $rsDies     = $db->query("select * from dies_setmana where laborable='S'");
  $rsHores    = $db->query("select * from franges_horaries where idtorn=".$idtorn." order by hora_inici");   
?>

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
			width:890px;
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

<div style="width:890px;">
 <h5 style="margin-bottom:0px">
  &nbsp;&nbsp;
  Curs escolar <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= $cursliteral ?></a>&nbsp;&nbsp;
  Horari de <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= $nom_alumne ?></a>&nbsp;&nbsp;
 </h5>
 <br />
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
							 echo "<td valign='top' class='drop'>";
							 $rsMateries = getMateriesDiaHoraAlumne($db,$dia,$franjahoraria,$cursactual,$idalumnes);
							 foreach($rsMateries->fetchAll() as $row) {
							    echo "<div style='border:2px solid #162b48;margin-bottom:3px;'>";
								echo "<div style='color:#39892f;border-bottom:1px solid #39892f;'>".$row['materia']."</div>";
								echo "<div style='color:#cc092f;border-bottom:1px solid #cc092f;'>".$row['grup']."</div>";
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