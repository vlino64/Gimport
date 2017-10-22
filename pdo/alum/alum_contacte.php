<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $strNoCache        = "";
  $idalumnes         = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0 ;
  $dadesalumneArray  = array(TIPUS_nom_complet,TIPUS_iden_ref,TIPUS_cognom1_alumne,
        		     TIPUS_cognom2_alumne,TIPUS_nom_alumne,TIPUS_email,
			     TIPUS_login,TIPUS_contrasenya,TIPUS_data_naixement);
  
  $dadesocultesArray  = array(TIPUS_contrasenya,TIPUS_contrasenya_notifica,TIPUS_contrasenya_notifica2,
  			      TIPUS_login1,TIPUS_login2,TIPUS_contrasenya2,TIPUS_nom_profe,
                              TIPUS_cognoms_profe);

  $imgalum = "../images/alumnes/".$idalumnes.".jpg";
		
  if (file_exists($imgalum)) {
	$imgalum = "./images/alumnes/".$idalumnes.".jpg";
  }
  else {
	$imgalum = "./images/alumnes/alumne.png";
  }
  echo "<table>";
  echo "<tr>";
  echo "<td width='70' valign='top'>";
  echo "<img src='".$imgalum.$strNoCache."' style='width:51px;height:70px'>";
  echo "</td>";
  echo "<td>";
  echo "<form name='fm_fitxa' method='post'>";
  echo "<div class='fitem'>";
  echo "<label>Codi SAGA</label> ";
  echo "<input name='codi_alumnes_saga' class='easyui-numberbox' size='55' value='".getCodiSagaAlumne($db,$idalumnes)."'>";
  echo "</div>";
  echo "<hr>";
  
  $rsTipusContacte = getallTipusContacte($db);
  foreach($rsTipusContacte->fetchAll() as $row) {
  	  
	  if (in_array($row['idtipus_contacte'], $dadesocultesArray)) {
		  if ($row['idtipus_contacte']==TIPUS_contrasenya) {
                       $valor = "";
		       echo "<input type='hidden' name='elem".$row['idtipus_contacte']."' class='easyui-validatebox' size='55' value='".$valor."'>";
                  }  
	  }
	  else {
		  echo "<div class='fitem' style='border-bottom:1px dashed #CCC; padding-bottom:3px; margin-bottom:3px; '>";
		  if (in_array($row['idtipus_contacte'], $dadesalumneArray)) {
                        $fet = "esta";
			$valor = getValorTipusContacteAlumne($db,$idalumnes,$row['idtipus_contacte']);
		  }
		  else {
                        $fet = "no esta";
			$valor = getValorTipusContacteFamilies($db,$idalumnes,$row['idtipus_contacte']);
		  }
                  
		  echo "<label><strong>".$row['dada_contacte']."</strong></label> ";
		  echo "<input name='elem".$row['idtipus_contacte']."' class='easyui-validatebox' size='45' value='".$valor."'>";

		  if ($row['idtipus_contacte']==TIPUS_data_naixement) {
			  echo "<br>&nbsp;Format: DD/MM/AAAA";
		  }
		  echo "</div>";
	  }
	  
  }
  echo "</form>";
  echo "</td>";
?>

<td valign="bottom">
<div style="padding:5px 0;text-align:right;padding-right:5px">
<a href="#" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="saveItem(<?php echo $_REQUEST['index'];?>)">Guardar</a>
<a href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="cancelItem(<?php echo $_REQUEST['index'];?>)">Cancel.lar</a>
</div>
</td></tr></table>