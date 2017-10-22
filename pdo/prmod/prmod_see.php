<?php
  session_start();	 
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $id_professor  = isset($_REQUEST['id_professor']) ? $_REQUEST['id_professor'] : 0 ;
  
  $sql  = "SELECT gm.idgrups_materies,gm.id_mat_uf_pla as id_mat_uf_pla,ma.nom_materia AS nom, ";
  $sql .= "CONCAT(SUBSTR(gm.data_inici,9,2),'-',SUBSTR(gm.data_inici,6,2),'-',SUBSTR(gm.data_inici,1,4)) AS data_inici, ";
  $sql .= "CONCAT(SUBSTR(gm.data_fi,9,2),'-',SUBSTR(gm.data_fi,6,2),'-',SUBSTR(gm.data_fi,1,4)) AS data_fi,gr.nom AS grup ";
  $sql .= "FROM prof_agrupament pa ";
  $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
  $sql .= "INNER JOIN materia ma        ON gm.id_mat_uf_pla=ma.idmateria ";
  $sql .= "INNER JOIN grups gr          ON gm.id_grups=gr.idgrups ";
  $sql .= "WHERE pa.idprofessors='".$id_professor."' ";
  $sql .= "UNION ";
  $sql .= "SELECT gm.idgrups_materies,gm.id_mat_uf_pla as id_mat_uf_pla,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom, ";
  $sql .= "CONCAT(SUBSTR(gm.data_inici,9,2),'-',SUBSTR(gm.data_inici,6,2),'-',SUBSTR(gm.data_inici,1,4)) AS data_inici, ";
  $sql .= "CONCAT(SUBSTR(gm.data_fi,9,2),'-',SUBSTR(gm.data_fi,6,2),'-',SUBSTR(gm.data_fi,1,4)) AS data_fi,gr.nom AS grup ";
  $sql .= "FROM prof_agrupament pa ";
  $sql .= "INNER JOIN grups_materies gm     ON pa.idagrups_materies=gm.idgrups_materies ";
  $sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla=uf.idunitats_formatives ";
  $sql .= "INNER JOIN moduls_ufs mu         ON gm.id_mat_uf_pla=mu.id_ufs ";
  $sql .= "INNER JOIN moduls m              ON mu.id_moduls=m.idmoduls ";
  $sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
  $sql .= "WHERE pa.idprofessors='".$id_professor."' ";
  $sql .= "ORDER BY 3";
  
  $rsUFs = $db->query($sql);   
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
			float:left;
			
		}
		.right table{
			background:#E0ECFF;
			width:850px;
		}
		.right td{
			background:#fff;
			text-align:left;
			padding:2px;
		}
		.right td{
			background:#fff;
		}
		.right td.drop{
			text-align:left;
			background:#fff;
			
		}
		.right td.over{
			text-align:left;
			width:50px;
			color:#000000;
			background:#fff;
		}
		.item{
			text-align:left;
			border:0px solid #499B33;
			background:#ffffff;
			/*width:100px;*/
		}
		
	</style>

<div style="width:900px;">
 <h5 style="margin-bottom:0px">
  &nbsp;&nbsp;
  Mat&egrave;ries / Unitats Formatives de <a style=" color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px ">
  <?= getProfessor($db,$id_professor,TIPUS_nom_complet) ?></a>&nbsp;&nbsp;
 </h5>       
	<div class="left">
		&nbsp;
	</div>
	<div class="right">
		<table>
                <?php
				   $idx=1;
                                   foreach($rsUFs->fetchAll() as $row) {
					  echo "<tr>";
					  //echo "<td valign='top' class='drop'>".$idx."</td>";
                                          echo "<td width='10' valign='top'>&nbsp;</td>";
                                          echo "<td valign='top' class='drop'>";
					  echo $row["nom"]." - ".$row["grup"];
				  	  echo "</td>";
					  echo "</tr>";
					  $idx++;
				   }
				?>          
		</table>
	</div>
</div>

<?php
//mysql_free_result($rsUFs);
//mysql_close();
?>