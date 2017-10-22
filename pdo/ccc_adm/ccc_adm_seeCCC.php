<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;    
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

	<style type='text/css'>
		.left{
			
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
			/*width:1000px;*/
		}
	</style>

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

 <div style=''>
 
 
 <h2 style='margin-bottom:0px; color:#d50000;'>
  Detall de CCC
 </h2>  
 <br />
	<div class='left'>
		&nbsp;
	</div>
	<div class='right'>

                <?php
				   $sql = "SELECT * FROM ccc_taula_principal WHERE idccc_taula_principal=$id";
				   $rs  = $db->query($sql);
				   foreach($rs->fetchAll() as $row) {
						 echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Data incid&egrave;ncia</label>&nbsp;";
                                                 echo substr($row["data"],8,2)."/".substr($row["data"],5,2)."/".substr($row["data"],0,4)."&nbsp;&nbsp;&nbsp;";
						 
						 if ($row["idfranges_horaries"] != 0) {
						   echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Franja hor&agrave;ria</label>&nbsp;";
                                                   echo getLiteralFranjaHoraria($db,$row["idfranges_horaries"])."&nbsp;&nbsp;&nbsp;";
						 }
						 
						 echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Implica expulsi&oacute; de classe?</label>&nbsp;";
                                                 echo $row["expulsio"]."<br /><br />";

						 echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Alumne</label><br />";
						 echo getAlumne($db,$row["idalumne"],TIPUS_nom_complet)."<br /><br />";
						 
						 echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Professor</label><br />";
						 echo getProfessor($db,$row["idprofessor"],TIPUS_nom_complet)."<br /><br />";
						 
						 if ($row["idgrup"] != 0) {
						   echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Grup</label>&nbsp;";
                                                   echo getGrup($db,$row["idgrup"])["nom"]."<br /><br />";
						 }
						 
						 if ($row["idmateria"] != 0) {
						   echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Mat&egrave;ria</label>&nbsp;";
						   echo getMateria($db,$row["idmateria"])["nom_materia"]."<br /><br />";
						 }
						 
						 if ($row["idespais"] != 0) {
						   echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Espai</label>&nbsp;";
						   echo getEspaiCentre($db,$row["idespais"])["descripcio"]."<br /><br />";				 
						 }
						 
						 echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Tipus d'incid&egrave;ncia</label>&nbsp;";
						 echo getLiteralTipusCCC($db,$row["id_falta"])["nom_falta"]."<br /><br />";
						 
						 echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Motiu</label><br />";
						 echo getLiteralMotiusCCC($db,$row["id_motius"])["nom_motiu"]."<br /><br />";

                		 echo "<label style='font-size:18px; font-weight:bolder; color:#990000'>Fets que s'han produ&iuml;t</label><br />";
						 echo nl2br($row["descripcio_detallada"])."<br /><br />";

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
$rs->closeCursor();
//mysql_close();
?>