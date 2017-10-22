<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  
  $db->exec("set names utf8");
  
  $idespais_centre = intval($_REQUEST['idespais_centre']);
  $cursactual      = getCursActual($db)["idperiodes_escolars"];
  $cursliteral     = getCursActual($db)["Nom"];
  
  $mode_impresio   = isset($_REQUEST['mode_impresio']) ? $_REQUEST['mode_impresio'] : 0;
  
  $rsDies     = $db->query("select * from dies_setmana where laborable='S'");
  $rsHores    = $db->query("select * from franges_horaries where esbarjo<>'S' order by hora_inici");   
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
			width:1150px;
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
        
<div style="width:1160px;">
 <h5 style="margin-bottom:0px">
  &nbsp;&nbsp;
  Curs escolar <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= $cursliteral ?></a>&nbsp;&nbsp;
  Horari de <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= getEspaiCentre($db,$idespais_centre)["descripcio"] ?></a>&nbsp;&nbsp;
 </h5>       
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
						     $rsMateries = getMateriesDiaHoraEspaiCentre($db,$dia,$franjahoraria,$cursactual,$idespais_centre);
							 echo "<td valign='top' class='drop'>";
							
							 foreach($rsMateries->fetchAll() as $row) {
							    echo "<div style='border:2px solid #162b48;margin-bottom:3px;'>";
								echo "<div style='color:#b26f00;border-bottom:1px solid #b26f00;'>".$row['materia']."</div>";
								echo "<div style='color:#39892f;border-bottom:1px solid #39892f;'>".$row['grup']."</div>";
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

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
</script>

<?php
//mysql_free_result($rsDies);
//mysql_free_result($rsHores);
//mysql_close();
?>