<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql       = "SELECT a.idalumnes,ca.Valor FROM alumnes a ";
$sql      .= "INNER JOIN contacte_alumne ca ON ca.id_alumne=a.idalumnes ";
$sql      .= "WHERE a.activat='S' AND ca.id_tipus_contacte=".TIPUS_nom_complet;
$sql      .= " ORDER BY 2 ";
$rsAlumnes = $db->query($sql);

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
		}
		.right td.drop{
			background:#fafafa;
			
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

<div class="left">
		&nbsp;
</div>
<div class="right">
<?php
$fila = 1;
echo "<table width=95% cellpadding=0 cellspacing=1>";
echo "<tr>";
echo "<td class='title'></td>";
echo "<td class='title'><strong>Alumne</strong></td>";
echo "<td class='title'><strong>Login familia</strong></td>";
echo "<td class='title'><strong>Contrasenya</strong></td>";
echo "<td class='title'><strong>EMail</strong></td>";
echo "<td class='title'><strong>Tlf.SMS</strong></td>";
echo "</tr>";

foreach($rsAlumnes->fetchAll() as $row) {
	$idalumnes  = $row["idalumnes"];

	echo "<tr>";
	echo "<td width='30'>".$fila."</td>";
	echo "<td width='440' class='drop'>".$row["Valor"]."</td>";
	echo "<td width='160' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_login)."</td>";
	echo "<td width='60' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_contrasenya_notifica)."</td>";
	echo "<td width='60' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_email1)."</td>";
	echo "<td width='50' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_mobil_sms)."</td>";
		
	$fila++;
	echo "</tr>";
	
	if (getValorTipusContacteFamilies($db,$idalumnes,TIPUS_login2) != '') {
		echo "<tr>";
		echo "<td width='30'></td>";
		echo "<td width='440' class='drop'>".$row["Valor"]."  <strong>(Tutor 2)</strong></td>";
		echo "<td width='160' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_login2)."</td>";
		echo "<td width='60' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_contrasenya_notifica2)."</td>";
		echo "<td width='60' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_email2)."</td>";
		echo "<td width='50' class='drop'>".getValorTipusContacteFamilies($db,$idalumnes,TIPUS_mobil_sms2)."</td>";
		echo "</tr>";
	}	
}
?>
</div>

<?php	
//mysql_free_result($rsAlumnes);
//mysql_close();
?>