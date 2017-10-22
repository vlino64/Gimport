<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$id_professor       = $_REQUEST['id_professor'];
$idgrups            = $_REQUEST['idgrups'];
$idfranges_horaries = $_REQUEST['idfranges_horaries'];
$data               = isset($_REQUEST['data']) ? substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2) : date("Y-m-d");
$any                = substr($data,0,4);
$mes                = substr($data,5,2);
$dia                = substr($data,8,2);

if ($idgrups==0) {
	exit(0);
}

$nom_fitxer_tasca   = $any.$mes.$dia."_".$id_professor."_".$idfranges_horaries;
$fitxer_tasca		= glob ("../feina_guardies/".$nom_fitxer_tasca.".*");
if (count($fitxer_tasca) > 0) {
	$color = '#ccc';
}
else {
	$color = '#fff';
}
?>

<form id="fm" method="post" enctype="multipart/form-data">
            <br />
            <div style="border:1px dashed #ddd; width:900px; background-color:<?=$color?>">
                <label>Fitxer tasca&nbsp;</label>
                <input type="file" name="file" id="file" class="easyui-validatebox" validType="fileType['xls']" value="arxiu ..." />
                <!--
                <br />Comentari&nbsp;&nbsp;&nbsp;
                <input id="comentari_tasca" name="comentari_tasca" class="easyui-validatebox" type="text" size="75" data-options="type:'textarea',validType:'length[0,256]'">
                -->
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveItem(<?php echo $_REQUEST['index'];?>)">Enviar tasca ...</a>
                <?php
					foreach ($fitxer_tasca as $fitxer) { 
					  /*echo $_SERVER['SERVER_NAME'];
					  echo substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-31);
					  echo substr($fitxer,3,strlen($fitxer)-2);*/
					  $link = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-29).substr($fitxer,3,strlen($fitxer)-2); 
					  echo "<a href='".$link."' target='_fitxer_tasca'><img src='./images/task_view.png' width=30></a>";
					  
				    } 
				?>
                <div id="resultPhotoDiv<?php echo $_REQUEST['index'];?>" style="float:right; border:0px dashed #666666; width:50px; height:50px;">
				</div>
            </div>      
</form>