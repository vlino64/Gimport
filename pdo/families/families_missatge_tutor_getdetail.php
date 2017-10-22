<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id   = $_REQUEST['id']; 
$sql  = "select * from missatges_tutor where idmissatges_tutor=$id";
$rs   = $db->query($sql);
foreach($rs->fetchAll() as $item) { 

//$aa = explode('-',$id);	
?>
    
<table class="dv-table" border="0" style="width:100%;">
<tr>
    <td style="border:0" valign=top width=650>
    <b>Missatge</b><br><?=  nl2br($item['missatge']) ?><br>
    <td width=2>&nbsp;</td>
</tr>
</table>
                            
<?php
}
$rs->closeCursor();
//mysql_close();
?>